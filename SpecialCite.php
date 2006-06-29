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
 * @author  var Arnfjörð Bjarmason <avarab@gmail.com>
 * @copyright Copyright © 2005,  var Arnfjörð Bjarmason
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

$wgExtensionFunctions[] = 'wfSpecialCite';
$wgExtensionCredits['specialpage'][] = array(
	'name' => 'Cite',
	'author' => ' var Arnfjörð Bjarmason',
	'description' => 'adds a [[Special:Cite|citation]] special page & toolbox link',
	'url' => 'http://meta.wikimedia.org/wiki/Cite/SpecialCite.php'
);

$wgHooks['SkinTemplateBuildNavUrlsNav_urlsAfterPermalink'][] = 'wfSpecialCiteNav';
$wgHooks['MonoBookTemplateToolboxEnd'][] = 'wfSpecialCiteToolbox';

if ( !function_exists( 'extAddSpecialPage' ) ) {
	require( dirname(__FILE__) . '/../ExtensionFunctions.php' );
}
extAddSpecialPage( dirname(__FILE__) . '/SpecialCite_body.php', 'Cite', 'SpecialCite' );

function wfSpecialCite() {
	global $wgMessageCache;
	$wgMessageCache->addMessage( 'cite_article_link', 'Cite this article' );
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


?>
