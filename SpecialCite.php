<?php
if (!defined('MEDIAWIKI')) die();
/**
 * A special page extension that adds a special page that generates citations
 * for pages.
 *
 * @package MediaWiki
 * @subpackage Extensions
 *
 * @link http://meta.wikimedia.org/wiki/Help:Cite Documentation
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
	'url' => 'http://meta.wikimedia.org/wiki/Help:Cite'
);

$wgHooks['SkinTemplateBuildNavUrlsNav_urlsAfterPermalink'][] = 'wfSpecialCiteNav';
$wgHooks['MonoBookTemplateToolboxEnd'][] = 'wfSpecialCiteToolbox';

function wfSpecialCite() {
	global $IP, $wgMessageCache, $wgHooks, $wgLanguageCode;

	$wgMessageCache->addMessages(
		array(
			'cite' => 'Cite',
			'cite_page' => 'Page: ',
			'cite_submit' => 'Cite',
			'cite_article_link' => 'Cite this article',
		)
	);

	# FIXME long lines of code -- Hashar

	# Do we have a translated text for the current language ?
	if($wgLanguageCode && file_exists( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'cite_text'. '-' . strtolower($wgLanguageCode) ) ) {
		$wgMessageCache->addMessages(
			array( 'cite_text' => file_get_contents( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'cite_text' . '-' . strtolower($wgLanguageCode) ) )
		);
	} else {
		# Add default text (english)
		$wgMessageCache->addMessages(
			array( 'cite_text' => file_get_contents( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'cite_text' ) )
		);
	}

	require_once "$IP/includes/SpecialPage.php";
	class Cite extends SpecialPage {
		function Cite() {
			SpecialPage::SpecialPage( 'Cite' );
		}
		
		function execute( $par ) {
			global $wgOut, $wgRequest, $wgUseTidy;

			// Having tidy on causes whitespace and <pre> tags to
			// be generated around the output of the CiteOutput
			// class TODO FIXME.
			$wgUseTidy = false;

			$this->setHeaders();

			$page = isset( $par ) ? $par : $wgRequest->getText( 'page' );
			
			$title = Title::newFromText( $page );
			$article = new Article( $title );

			$cform = new CiteForm( $title );

			if ( is_null( $title ) || ! $article->exists() )
				$cform->execute();
			else {
				$cform->execute();
				
				$cout = new CiteOutput( $title, $article );
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
		var $mTitle, $mArticle, $mParserOptions;
		var $mParser;

		function CiteOutput( &$title, &$article ) {
			global $wgHooks, $wgParser;
			
			$this->mTitle =& $title;
			$this->mArticle =& $article;

			$wgHooks['ParserGetVariableValueVarCache'][] = array( $this, 'varCache' );

			$this->genParserOptions();
			$this->genParser();

			$wgParser->setHook( 'citation', array( $this, 'CiteParse' ) );
		}
		
		function execute() {
			global $wgOut, $wgUser, $wgParser, $wgHooks;

			$wgHooks['ParserGetVariableValueTs'][] = array( $this, 'timestamp' );

			$msg = wfMsgForContentNoTrans( 'cite_text' );
			$this->mArticle->fetchContent();
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
	
	SpecialPage::addPage( new Cite );
}

function wfSpecialCiteNav( &$skintemplate, &$nav_urls, &$oldid, &$revid ) {
	if ( $skintemplate->mTitle->getNamespace() === NS_MAIN ) {
		if ( (int)$oldid  )
			$nav_urls['cite'] = array(
				'text' => wfMsg( 'cite_article_link' ),
				'href' => ''
			);
		else if ( $revid !== 0 )
			$nav_urls['cite'] = array(
				'text' => wfMsg( 'cite_article_link' ),
				'href' => $skintemplate->makeSpecialUrl( 'Cite/' . $skintemplate->thispage )
			);
	}

	return true;
}

function wfSpecialCiteToolbox( &$monobook ) {
	if ( isset( $monobook->data['nav_urls']['cite'] ) )
		if ( $monobook->data['nav_urls']['cite']['href'] == '' ) {
			?><li id="t-iscite"><?php echo $monobook->msg( 'cite_article_link' ); ?></li><?php
		} else {
			?><li id="t-cite">
				<a href="<?php echo htmlspecialchars( $monobook->data['nav_urls']['cite']['href'] ) ?>">
					<?php echo $monobook->msg( 'cite_article_link' ); ?>
				</a>
			</li>
			<?php
		}
	
	return true;
}
