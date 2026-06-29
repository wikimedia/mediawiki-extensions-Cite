const createReferenceGateway = require( './createReferenceGateway.js' );
const createReferencePreview = require( './createReferencePreview.js' );
const TYPE_REFERENCE = 'reference';

/**
 * Given the global state of the application, creates a function that gets
 * whether or not the user should have Reference Previews enabled.
 *
 * @param {mw.user} user The `mw.user` singleton instance
 * @param {Function} isPreviewTypeEnabled check whether preview has been disabled or enabled.
 * @param {mw.Map} config
 *
 * @return {boolean|null} Null when there is no way the popup type can be enabled at run-time.
 * @memberof module:ext.cite.referencePreviews
 */
function isReferencePreviewsEnabled( user, isPreviewTypeEnabled, config ) {
	if ( !config.get( 'wgCiteReferencePreviewsActive' ) ) {
		return null;
	}

	if ( user.isAnon() ) {
		return isPreviewTypeEnabled( TYPE_REFERENCE );
	}

	return true;
}

const referencePreviewsState = isReferencePreviewsEnabled(
	mw.user,
	mw.popups.isEnabled,
	mw.config
);

/**
 * Create the relevant config to register the preview type in the Popups extension.
 *
 * @see mw.popups.register()
 * @return {Object}
 * @memberof module:ext.cite.referencePreviews
 */
function createReferencePreviewsType() {
	return {
		type: TYPE_REFERENCE,
		selector: '#mw-content-text .reference a[ href*="#" ]',
		delay: 150,
		gateway: createReferenceGateway(),
		renderFn: createReferencePreview
	};
}

function getExperiment() {
	return mw.loader.using( 'ext.testKitchen' ).then(
		() => mw.testKitchen.getExperiment( 'cite-footnote-content-interaction-experiment' )
	);
}

// Test Kitchen experiment: cite-footnote-content-interaction-experiment (T123456)
if ( !mw.config.get( 'wgMFMode' ) ) {
	getExperiment().then( ( experiment ) => {
		if ( experiment && experiment.getAssignedGroup( 'treatment' ) ) {
			// eslint-disable-next-line no-jquery/no-global-selector
			$( '#mw-content-text .reference a[ href*="#" ]' ).on( 'click', ( event ) => {
				// Bail out when the event was triggerd by keyboard interaction or touch
				if ( !event.pointerType || event.pointerType === 'touch' ) {
					return;
				}

				// Prevent default jump to reference list
				event.preventDefault();
				// Click on footnote marker triggers open popup with jump to refList link
				// eslint-disable-next-line no-jquery/no-global-selector
				$( '.mwe-popups-reflist-link-hidden' )
					.removeClass( 'mwe-popups-reflist-link-hidden' );
			} );
		}
	} );
}

module.exports = referencePreviewsState !== null ? createReferencePreviewsType() : null;

if ( window.QUnit ) {
	module.exports = {
		test: {
			createReferenceGateway: require( './createReferenceGateway.js' ),
			createReferencePreview: require( './createReferencePreview.js' ),
			isReferencePreviewsEnabled
		}
	};
}
