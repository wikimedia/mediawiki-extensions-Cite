<?php
namespace Cite\Hooks\ResourceLoaderModules;

use MediaWiki\Registration\ExtensionRegistry;
use MediaWiki\ResourceLoader\ResourceLoader;

/**
 * @license GPL-2.0-or-later
 */
class WikiEditorResourceModule {
	public function __construct(
		private readonly ExtensionRegistry $extensionRegistry
	) {
	}

	public function loadModule( ResourceLoader $rl ): void {
		if ( !$this->extensionRegistry->isLoaded( 'WikiEditor' ) ) {
			return;
		}

		$rl->register( [
			'ext.cite.wikiEditor' => [
				'localBasePath' => dirname( __DIR__, 3 ) . '/modules',
				'remoteExtPath' => 'Cite/modules',
				'scripts' => [
					'ext.cite.wikiEditor.js',
				],
				'dependencies' => [
					'ext.cite.styles',
					'ext.wikiEditor',
					'mediawiki.jqueryMsg',
					'mediawiki.language',
				],
				'messages' => [
					'cite-wikieditor-tool-reference',
					'cite-wikieditor-help-page-references',
					'cite-wikieditor-help-content-reference-example-text1',
					'cite-wikieditor-help-content-reference-example-text2',
					'cite-wikieditor-help-content-reference-example-text3',
					'cite-wikieditor-help-content-reference-example-ref-id',
					'cite-wikieditor-help-content-reference-example-extra-details',
					'cite-wikieditor-help-content-reference-example-ref-normal',
					'cite-wikieditor-help-content-reference-example-ref-named',
					'cite-wikieditor-help-content-reference-example-ref-reuse',
					'cite-wikieditor-help-content-reference-example-ref-details',
					'cite-wikieditor-help-content-reference-example-ref-result',
					'cite-wikieditor-help-content-reference-example-reflist',
					'cite-wikieditor-help-content-reference-description',
					'cite-wikieditor-help-content-named-reference-description',
					'cite-wikieditor-help-content-rereference-description',
					'cite-wikieditor-help-content-sub-reference-description',
					'cite-wikieditor-help-content-showreferences-description',
					'cite_reference_backlink_symbol',
				],
			],
		] );
	}

}
