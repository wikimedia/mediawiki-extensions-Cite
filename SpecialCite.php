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
	global $IP, $wgMessageCache, $wgHooks;
	
	$wgMessageCache->addMessages(
		array(
			'cite' => 'Cite',
			'cite_page' => 'Page: ',
			'cite_submit' => 'Cite',
			'cite_page_link' => 'Cite this page',
			'cite_text' =>
					"* ''{{FULLPAGENAME}}'' (last modified {{CURRENTTIME}}," .
					' {{CURRENTDAY}} {{CURRENTMONTHNAME}} {{CURRENTYEAR}} UTC).' .
					' {{SITENAME}}, {{int:sitesubtitle}}. Retrived <cite>{{CURRENTTIME}},' .
					' {{CURRENTDAY}} {{CURRENTMONTHNAME}} {{CURRENTYEAR}}</cite> from' .
					' {{fullurl:{{FULLPAGENAME}}|oldid={{REVISIONID}}}}'
		)
	);

	require_once "$IP/includes/SpecialPage.php";
	class Cite extends SpecialPage {
		function Cite() {
			SpecialPage::SpecialPage( 'Cite' );
		}
		
		function execute( $par ) {
			global $wgOut, $wgRequest;

			$this->setHeaders();

			$page = isset( $par ) ? $par : $wgRequest->getText( 'page' );
			
			$title = Title::newFromText( $page );
			$article = new Article( $title );

			$cform = new CiteForm( &$title );

			if ( is_null( $title ) || ! $article->exists() )
				$cform->execute();
			else {
				$cform->execute();
				
				$cout = new CiteOutput( &$title, &$article );
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
		var $mTitle, $mArticle, $mMsg, $mParserOptions;
		var $mParser;

		function CiteOutput( $title, $article ) {
			global $wgHooks, $wgParser;
			
			$this->mTitle =& $title;
			$this->mArticle =& $article;

			$wgHooks['ParserGetVariableValueRevid'][] = array( $this, 'revid' );
			$wgHooks['ParserGetVariableValueVarCache'][] = array( $this, 'varCache' );

			$this->genParserOptions();
			$this->genParser();

			$wgParser->setHook( 'cite', 'CiteParse', $this, &$mParser );
		}
		
		function execute() {
			global $wgOut, $wgUser, $wgParser, $wgHooks;

			$wgHooks['ParserGetVariableValueTs'][] = array( $this, 'timestamp' );

			$this->genMessage();
			$ret = $wgParser->parse( $this->mMsg, &$this->mTitle, $this->mParserOptions );
			$wgOut->addHtml( $ret->getText() );
		}

		function genMessage() {
			global $wgMessageCache;
			
			$setting = $wgMessageCache->mDisableTransform;
			$wgMessageCache->disableTransform();
			$this->mMsg = $wgMessageCache->get( 'cite_text', true, true );
			$wgMessageCache->mDisableTransform = $setting;
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
			if ( isset( $parser->mTagHooks['cite'] ) )
				$ts = wfTimestamp( TS_UNIX, $this->mArticle->getTimestamp() );
			
			return true;
		}

		function revid( &$parser, &$revid ) {
			$this->mArticle->fetchContent();
			$revid = $this->mArticle->getRevIdFetched();

			return false;
		}
	}
	
	SpecialPage::addPage( new Cite );
}

function wfSpecialCiteNav( &$skintemplate, &$nav_urls, &$oldid, &$revid ) {
	if ( (int)$oldid  )
		$nav_urls['cite'] = array(
			'text' => wfMsg( 'cite_page_link' ),
			'href' => ''
		);
	else if ( $revid !== 0 )
		$nav_urls['cite'] = array(
			'text' => wfMsg( 'cite_page_link' ),
			'href' => $skintemplate->makeSpecialUrl( 'Cite/' . $skintemplate->thispage )
		);

	return true;
}

function wfSpecialCiteToolbox( &$monobook ) {
	if ( isset( $monobook->data['nav_urls']['cite'] ) )
		if ( $monobook->data['nav_urls']['cite']['href'] == '' ) {
			?><li id="t-iscite"><?php echo $monobook->msg( 'cite_page_link' ); ?></li><?php
		} else {
			?><li id="t-cite">
				<a href="<?php echo htmlspecialchars( $monobook->data['nav_urls']['cite']['href'] ) ?>">
					<?php echo $monobook->msg( 'cite_page_link' ); ?>
				</a>
			</li>
			<?php
		}
	
	return true;
}
