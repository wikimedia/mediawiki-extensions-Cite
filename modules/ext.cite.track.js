'use strict';

/**
 * Adding code for analytic instruments
 */
( function () {
	let lastFootnoteClicked;

	/**
	 * @param {jQuery} $content
	 * @param {mw.testKitchen.ExperimentInterface} experiment
	 * @return {boolean}
	 */
	function addTocTracking( $content, experiment ) {
		// Find the references list inside the page content
		const $referencesList = $content.find( '.mw-heading2 ~ .mw-references-wrap, .mw-heading2 ~ ol.references' ).last();
		const $headline = $referencesList.prevAll( '.mw-heading2' ).first().children( 'h2' );
		const anchor = $headline.attr( 'id' );
		const isReferencesListInViewport =
			() => $headline.get( 0 ).getBoundingClientRect().top < window.innerHeight;

		if ( !anchor ) {
			return false;
		}

		// Find the TOC link that points to this anchor
		const $tocLink = $( '#vector-toc a[href="#' + CSS.escape( anchor ) + '"]' );
		if ( $tocLink.length === 0 ) {
			return false;
		}

		if ( isReferencesListInViewport() ) {
			experiment.send( 'initial-pageview-shows-references' );
		}

		// Add click handler
		$tocLink.on( 'click', () => {
			experiment.send( 'click-toc-link' );
		} );

		$( window ).on( 'scroll', mw.util.debounce( () => {
			if ( isReferencesListInViewport() ) {
				experiment.send( 'scrolled-to-references' );
			}
		}, 500 ) );

		return true;
	}

	/**
	 * @param {jQuery} $content
	 * @param {mw.testKitchen.ExperimentInterface} experiment
	 */
	function addFootnoteTracking( $content, experiment ) {
		const $footnotes = $content.find( 'sup.reference a' );

		$footnotes.on( 'click', function ( event ) {
			// Bail out when the event was triggerd by keyboard interaction or touch
			if ( !event.pointerType || event.pointerType === 'touch' ) {
				return;
			}

			if ( this !== lastFootnoteClicked ) {
				// The first click triggers the exposure
				experiment.sendExposure();
				experiment.send( 'click-footnote-marker' );
				lastFootnoteClicked = this;
			} else {
				// eslint-disable-next-line no-jquery/no-global-selector
				const $popupsReflistLink = $( '.mwe-popups-reflist-link-wrapper' );
				// eslint-disable-next-line no-jquery/no-class-state
				if ( $popupsReflistLink.length && !$popupsReflistLink.hasClass( 'mwe-popups-reflist-link-hidden' ) ) {
					// Send this event only if the popup is already in "persistent" state.
					experiment.send( 'click-footnote-marker-twice' );
				}
			}
		} );
	}

	function getExperiment() {
		return mw.loader.using( 'ext.testKitchen' ).then(
			() => mw.testKitchen.getExperiment( 'cite-footnote-content-interaction-experiment' )
		);
	}

	/**
	 * Adds temporary tracking for user interactions with footnote content T415904
	 *
	 * @param {jQuery} $content
	 */
	function addFootnoteContentExperiment( $content ) {
		// Test Kitchen experiment: cite-footnote-content-interaction-experiment (T123456)
		if ( !mw.config.get( 'wgMFMode' ) ) {
			getExperiment().then( ( experiment ) => {
				if ( experiment && experiment.getAssignedGroup() ) {
					experiment.send( 'page_visit' );

					addFootnoteTracking( $content, experiment );

					const foundToc = addTocTracking( $content, experiment );
					if ( !foundToc ) {
						experiment.send( 'no-toc-tracking-attached' );
					}
				}
			} );
		}
	}

	// Adds tracking to content hook
	mw.hook( 'wikipage.content' ).add( addFootnoteContentExperiment );
}() );
