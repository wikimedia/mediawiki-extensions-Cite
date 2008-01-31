<?php
if (!defined('MEDIAWIKI')) die();
/**
 * A special page extension that adds a special page that generates citations
 * for pages.
 *
 * @addtogroup Extensions
 *
 * @link http://meta.wikimedia.org/wiki/Cite/SpecialCite.php Documentation
 *
 * @author Ævar Arnfjörð Bjarmason <avarab@gmail.com>
 * @copyright Copyright © 2005, Ævar Arnfjörð Bjarmason
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

$wgExtensionCredits['specialpage'][] = array(
	'name' => 'Cite',
	'version' => '2008-01-31',
	'author' => 'Ævar Arnfjörð Bjarmason',
	'description' => 'adds a [[Special:Cite|citation]] special page & toolbox link', // kept for b/c
	'descriptionmsg' => 'cite_article_desc',
	'url' => 'http://www.mediawiki.org/wiki/Extension:Cite/Special:Cite.php'
);

# Internationalisation file
$wgExtensionMessagesFiles['SpecialCite'] = dirname( __FILE__ ) . "/SpecialCite.i18n.php";

$wgHooks['SkinTemplateBuildNavUrlsNav_urlsAfterPermalink'][] = 'wfSpecialCiteNav';
$wgHooks['MonoBookTemplateToolboxEnd'][] = 'wfSpecialCiteToolbox';

if ( !function_exists( 'extAddSpecialPage' ) ) {
	require( dirname(__FILE__) . '/../ExtensionFunctions.php' );
}
extAddSpecialPage( dirname(__FILE__) . '/SpecialCite_body.php', 'Cite', 'SpecialCite' );

function wfSpecialCiteNav( &$skintemplate, &$nav_urls, &$oldid, &$revid ) {
	wfLoadExtensionMessages( 'SpecialCite' );
	if ( $skintemplate->mTitle->isContentPage() && $revid !== 0 )
		$nav_urls['cite'] = array(
			'text' => wfMsg( 'cite_article_link' ),
			'href' => $skintemplate->makeSpecialUrl( 'Cite', "page=" . wfUrlencode( "{$skintemplate->thispage}" ) . "&id=$revid" )
		);

	return true;
}

function wfSpecialCiteToolbox( &$monobook ) {
	wfLoadExtensionMessages( 'SpecialCite' );
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
