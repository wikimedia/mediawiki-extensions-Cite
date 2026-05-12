'use strict';

/**
 * Adding code for analytic instruments
 */
( function () {
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

		if ( !anchor ) {
			return false;
		}

		// Find the TOC link that points to this anchor
		const $tocLink = $( '#vector-toc a[href="#' + CSS.escape( anchor ) + '"]' );
		if ( $tocLink.length === 0 ) {
			return false;
		}

		// Add click handler
		$tocLink.on( 'click', () => {
			experiment.send( 'click-toc-link' );
		} );

		return true;
	}

	/**
	 * @param {jQuery} $content
	 * @param {mw.testKitchen.ExperimentInterface} experiment
	 */
	function addFootnoteTracking( $content, experiment ) {
		const $footnotes = $content.find( 'sup.reference a' );

		$footnotes.on( 'click', () => experiment.send( 'click-footnote-marker' ) );
	}

	/**
	 * Adds temporary tracking for user interactions with footnote content T415904
	 *
	 * @param {jQuery} $content
	 */
	function addFootnoteContentExperiment( $content ) {
		/** @type {mw.testKitchen.ExperimentInterface|undefined} */
		if ( mw.testKitchen ) {
			mw.testKitchen.getExperiment( 'cite-footnote-content-interaction-experiment' )
				.then( ( experiment ) => {
					if ( experiment && experiment.getAssignedGroup() ) {
						experiment.sendExposure();

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
