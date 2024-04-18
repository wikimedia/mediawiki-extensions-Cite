'use strict';

/**
 * @file Temporary tracking to evaluate the impact of Reference Previews on
 * users' interaction with references.
 *
 * @see https://phabricator.wikimedia.org/T214493
 * @see https://phabricator.wikimedia.org/T231529
 * @see https://phabricator.wikimedia.org/T353798
 * @see https://meta.wikimedia.org/wiki/Schema:ReferencePreviewsBaseline
 * @see https://meta.wikimedia.org/wiki/Schema:ReferencePreviewsCite
 */

const isReferencePreviewsEnabled =
	require( './ext.cite.referencePreviews/isReferencePreviewsEnabled.js' );

const CITE_BASELINE_LOGGING_SCHEMA = 'ext.cite.baseline';

// EventLogging may not be installed
mw.loader.using( 'ext.eventLogging' ).then( function () {
	$( function () {
		if ( !navigator.sendBeacon ||
			!mw.config.get( 'wgIsArticle' )
		) {
			return;
		}

		// eslint-disable-next-line no-jquery/no-global-selector
		$( '#mw-content-text' ).on(
			'click',
			// Footnote links, references block in VisualEditor, and reference content links.
			'.reference a[ href*="#" ], .mw-reference-text a, .reference-text a',
			function () {
				const referencePreviewsState = isReferencePreviewsEnabled(
					mw.user,
					mw.popups.isEnabled,
					mw.config
				);
				const isInReferenceBlock = $( this ).parents( '.references' ).length > 0;
				mw.eventLog.dispatch( CITE_BASELINE_LOGGING_SCHEMA, {
					action: ( isInReferenceBlock ?
						'clickedReferenceContentLink' :
						'clickedFootnote' ),
					// eslint-disable-next-line camelcase
					with_ref_previews: referencePreviewsState
				} );
			}
		);
	} );
} );
