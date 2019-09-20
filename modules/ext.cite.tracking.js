/**
 * @file Temporary tracking to establish a baseline for ReferencePreviews metrics.
 * @see https://phabricator.wikimedia.org/T231529
 * @see https://meta.wikimedia.org/wiki/Schema:ReferencePreviewsBaseline
 */
( function () {
	'use strict';

	if ( navigator.sendBeacon && mw.eventLog.eventInSample( 1000 ) ) {
		$( function () {
			var isReferencePreviewsEnabled = mw.config.get( 'wgPopupsReferencePreviews', false );

			/**
			 * @param {Object} event
			 */
			function logEvent( event ) {
				event.referencePreviewsEnabled = isReferencePreviewsEnabled;
				mw.track( 'event.ReferencePreviewsBaseline', event );
			}

			// eslint-disable-next-line no-jquery/no-global-selector
			$( '#mw-content-text' ).on(
				'click',
				// Footnote links, references block in VisualEditor, and reference content links.
				'.reference a[ href*="#" ], .mw-reference-text a, .reference-text a',
				function () {
					var isInReferenceBlock = $( this ).parents( '.references' ).length > 0;
					logEvent( {
						action: ( isInReferenceBlock ?
							'clickedReferenceContentLink' :
							'clickedFootnote' )
					} );
				}
			);

			logEvent( { action: 'pageview' } );
		} );
	}
}() );
