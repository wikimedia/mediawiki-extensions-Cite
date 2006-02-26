<?php
if (!defined('MEDIAWIKI')) die();
/**
 * A special page extension that adds a special page that generates citations
 * for pages.
 *
 * @package MediaWiki
 * @subpackage Extensions
 *
 * @link http://meta.wikimedia.org/wiki/Cite/SpecialCite.php Documentation
 *
 * @author Ævar Arnfjörð Bjarmason <avarab@gmail.com>
 * @copyright Copyright © 2005, Ævar Arnfjörð Bjarmason
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

$wgExtensionFunctions[] = 'wfSpecialCite';
$wgExtensionCredits['specialpage'][] = array(
	'name' => 'Cite',
	'author' => 'Ævar Arnfjörð Bjarmason',
	'description' => 'adds a [[Special:Cite|citation]] special page & toolbox link',
	'url' => 'http://meta.wikimedia.org/wiki/Cite/SpecialCite.php'
);

$wgHooks['SkinTemplateBuildNavUrlsNav_urlsAfterPermalink'][] = 'wfSpecialCiteNav';
$wgHooks['MonoBookTemplateToolboxEnd'][] = 'wfSpecialCiteToolbox';

function wfSpecialCite() {
	global $IP, $wgMessageCache, $wgContLang, $wgContLanguageCode;

	$dir = dirname( __FILE__ ) . DIRECTORY_SEPARATOR;
	$code = $wgContLang->lc( $wgContLanguageCode );
	$file = file_exists( "${dir}cite_text-$code" ) ? "${dir}cite_text-$code" : "${dir}cite_text";
	
	$wgMessageCache->addMessages(
		array(
			'cite' => 'Cite',
			'cite_page' => 'Page: ',
			'cite_submit' => 'Cite',
			'cite_article_link' => 'Cite this article',
			'cite_text' => file_get_contents( $file )
		)
	);

	require_once "$IP/includes/SpecialPage.php";
	class SpecialCite extends SpecialPage {
		function SpecialCite() {
			SpecialPage::SpecialPage( 'Cite' );
		}
		
		function execute( $par ) {
			global $wgOut, $wgRequest, $wgUseTidy;

			// Having tidy on causes whitespace and <pre> tags to
			// be generated around the output of the CiteOutput
			// class TODO FIXME.
			$wgUseTidy = false;

			$this->setHeaders();
			$this->outputHeader();

			$page = isset( $par ) ? $par : $wgRequest->getText( 'page' );
			$id = $wgRequest->getInt( 'id' );
			
			$title = Title::newFromText( $page );
			$article = new Article( $title );

			$cform = new CiteForm( $title );

			if ( is_null( $title ) || ! $article->exists() )
				$cform->execute();
			else {
				$cform->execute();
				
				$cout = new CiteOutput( $title, $article, $id );
				$cout->execute();
			}
		}
	}

	class CiteForm {
		var $mTitle;
		
		function CiteForm( &$title ) {
			$this->mTitle =& $title;
		}
		
		function execute() {
			global $wgOut, $wgTitle;

			$wgOut->addHTML(
				wfElement( 'form',
					array(
						'id' => 'specialcite',
						'method' => 'get',
						'action' => $wgTitle->escapeLocalUrl()
					),
					null
				) .
					wfOpenElement( 'label' ) .
						wfMsgHtml( 'cite_page' ) .
						wfElement( 'input',
							array(
								'type' => 'text',
								'size' => 20,
								'name' => 'page',
								'value' => is_object( $this->mTitle ) ? $this->mTitle->getPrefixedText() : ''
							),
							''
						) .
						' ' .
						wfElement( 'input',
							array(
								'type' => 'submit',
								'value' => wfMsgHtml( 'cite_submit' )
							),
							''
						) .
					wfCloseElement( 'label' ) .
				wfCloseElement( 'form' )
			);
		}

	}

	class CiteOutput {
		var $mTitle, $mArticle, $mId;
		var $mParser, $mParserOptions;

		function CiteOutput( &$title, &$article, $id ) {
			global $wgHooks, $wgParser;
			
			$this->mTitle =& $title;
			$this->mArticle =& $article;
			$this->mId = $id;

			$wgHooks['ParserGetVariableValueVarCache'][] = array( $this, 'varCache' );

			$this->genParserOptions();
			$this->genParser();

			$wgParser->setHook( 'citation', array( $this, 'CiteParse' ) );
		}
		
		function execute() {
			global $wgOut, $wgUser, $wgParser, $wgHooks;

			$wgHooks['ParserGetVariableValueTs'][] = array( $this, 'timestamp' );

			$msg = wfMsgForContentNoTrans( 'cite_text' );
			$this->mArticle->fetchContent( $this->mId, false );
			$ret = $wgParser->parse( $msg, $this->mTitle, $this->mParserOptions, false, true, $this->mArticle->getRevIdFetched() );
			$wgOut->addHtml( $ret->getText() );
		}

		function genParserOptions() {
			$this->mParserOptions = ParserOptions::newFromUser( $wgUser );
			$this->mParserOptions->setDateFormat( MW_DATE_DEFAULT );
			$this->mParserOptions->setEditSection( false );
		}

		function genParser() {
			$this->mParser = new Parser;
		}

		function CiteParse( $in, $argv ) {
			global $wgTitle;
			
			$ret = $this->mParser->parse( $in, $wgTitle, $this->mParserOptions, false );
	
			return $ret->getText();
		}

		function varCache() { return false; }

		function timestamp( &$parser, &$ts ) {
			if ( isset( $parser->mTagHooks['citation'] ) )
				$ts = wfTimestamp( TS_UNIX, $this->mArticle->getTimestamp() );
			
			return true;
		}
	}
	
	SpecialPage::addPage( new SpecialCite );
}

function wfSpecialCiteNav( &$skintemplate, &$nav_urls, &$oldid, &$revid ) {
	if ( $skintemplate->mTitle->getNamespace() === NS_MAIN && $revid !== 0 )
		$nav_urls['cite'] = array(
			'text' => wfMsg( 'cite_article_link' ),
			'href' => $skintemplate->makeSpecialUrl( 'Cite', "page=" . wfUrlencode( "{$skintemplate->thispage}" ) . "&id=$revid" )
		);
	
	return true;
}

function wfSpecialCiteToolbox( &$monobook ) {
	if ( isset( $monobook->data['nav_urls']['cite'] ) )
		if ( $monobook->data['nav_urls']['cite']['href'] == '' ) {
			?><li id="t-iscite"><?php echo $monobook->msg( 'cite_article_link' ); ?></li><?php
		} else {
			?><li id="t-cite"><?php
				?><a href="<?php echo htmlspecialchars( $monobook->data['nav_urls']['cite']['href'] ) ?>"><?php
					echo $monobook->msg( 'cite_article_link' );
				?></a><?php
			?></li><?php
		}
	
	return true;
}
