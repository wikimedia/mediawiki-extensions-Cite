'use strict';

/*!
 * VisualEditor MWCitationNeededContextItem class.
 *
 * @copyright 2011-2018 VisualEditor Team's Cite sub-team and others; see AUTHORS.txt
 * @license MIT
 */

/**
 * Context item for a citation needed template.
 *
 * @constructor
 * @extends ve.ui.MWDefinedTransclusionContextItem
 * @param {ve.ui.LinearContext} context Context the item is in
 * @param {ve.dm.MWTransclusionNode} model Model the item is related to
 * @param {Object} [config]
 */
ve.ui.MWCitationNeededContextItem = function VeUiMWCitationNeededContextItem() {
	// Parent constructor
	ve.ui.MWCitationNeededContextItem.super.apply( this, arguments );

	this.addButton = new OO.ui.ButtonWidget( {
		label: ve.msg( 'cite-ve-citationneeded-button' ),
		flags: [ 'progressive' ]
	} ).on( 'click', this.onAddClick.bind( this ) );

	// Remove progressive flag from edit, as addButton is now the
	// main progressive action in the context.
	this.editButton.setFlags( { progressive: false } );

	// Initialization
	this.$element.addClass( 've-ui-mwCitationNeededContextItem' );
};

/* Inheritance */

OO.inheritClass( ve.ui.MWCitationNeededContextItem, ve.ui.MWDefinedTransclusionContextItem );

/* Static Properties */

ve.ui.MWCitationNeededContextItem.static.name = 'citationNeeded';

ve.ui.MWCitationNeededContextItem.static.icon = 'quotes';

ve.ui.MWCitationNeededContextItem.static.label = OO.ui.deferMsg( 'cite-ve-citationneeded-title' );

/**
 * @inheritdoc
 */
ve.ui.MWCitationNeededContextItem.static.isCompatibleWith = function () {
	// Parent method
	if ( !ve.ui.MWCitationNeededContextItem.super.static.isCompatibleWith.apply( this, arguments ) ) {
		return false;
	}
	// De-conflict with CitationNeededEditCheck
	if (
		ve.init.target.editcheckController &&
		ve.init.target.editcheckController.suggestionsVisible &&
		ve.init.target.editcheckController.canBeShown( 'citationNeeded', true )
	) {
		return false;
	}
	return true;
};

/* Methods */

/**
 * Handle click events on the add button
 *
 * Opens Citoid if it is installed, otherwise the plain reference dialog.
 *
 * @return {jQuery.Promise} Resolves with the closing data of the dialog that finishes the flow.
 *  Rejects if the encapsulated wikitext cannot be parsed or the dialog cannot be opened.
 */
ve.ui.MWCitationNeededContextItem.prototype.onAddClick = function () {
	const surface = this.context.getSurface();
	const encapsulatedWikitext = this.getCanonicalParam( 'encapsulate' );

	let promise;
	if ( encapsulatedWikitext ) {
		this.addButton.setDisabled( true );
		promise = ve.init.target
			.parseWikitextFragment( encapsulatedWikitext, false, this.model.getDocument() )
			.then( ( response ) => {

				if ( ve.getProp( response, 'visualeditor', 'result' ) !== 'success' ) {
					return ve.createDeferred().reject().promise();
				}

				const dmDoc = ve.ui.MWWikitextStringTransferHandler.static
					.createDocumentFromParsoidHtml(
						response.visualeditor.content,
						surface.getModel().getDocument()
					);
				const nodes = dmDoc.getDocumentNode().children.filter( ( node ) => !node.isInternal() );
				let range;

				// Unwrap single content branch nodes to match internal copy/paste behaviour
				// (which wouldn't put the open and close tags in the clipboard to begin with).
				if (
					nodes.length === 1 &&
					nodes[ 0 ].canContainContent()
				) {
					range = nodes[ 0 ].getRange();
				}

				surface.getModel().pushStaging();
				surface.getModel().getFragment()
					.insertDocument( dmDoc, range ).collapseToEnd().select();
				return true;
			} );
		promise.always( () => {
			this.addButton.setDisabled( false );
		} );
	} else {
		promise = ve.createDeferred().resolve( false ).promise();
	}

	ve.track( 'activity.' + this.constructor.static.name, { action: 'context-add-citation' } );

	return promise.then( ( inStaging ) => {
		if ( ve.ui.actionFactory.lookup( 'citoid' ) ) {
			return this.openCitoid( surface, inStaging );
		}
		return this.openReferenceDialog( surface, inStaging );
	} );
};

/**
 * Open Citoid to replace the template
 *
 * @private
 * @param {ve.ui.Surface} surface
 * @param {boolean} inStaging The surface has a staged change that Citoid must resolve
 * @return {jQuery.Promise} Resolves with the closing data of the last dialog
 */
ve.ui.MWCitationNeededContextItem.prototype.openCitoid = function ( surface, inStaging ) {
	const opened = ve.ui.actionFactory.create( 'citoid', surface ).open( { replace: true, inStaging } );
	if ( !opened ) {
		return ve.createDeferred().reject().promise();
	}
	return opened.then( ( instance ) => instance.closing ).then( ( citoidData ) => {
		if ( citoidData && citoidData.action === 'manual-choose' ) {
			// Citoid hands off to a manual citation dialog. Wait for that dialog instead.
			const deferred = ve.createDeferred();
			surface.getDialogs().once( 'closing', ( win, closed, data ) => {
				deferred.resolve( data );
			} );
			return deferred.promise();
		}
		return citoidData;
	} );
};

/**
 * Open the plain reference dialog to replace the template
 *
 * The template is removed in staging. The staging is applied only if a reference is inserted.
 *
 * @private
 * @param {ve.ui.Surface} surface
 * @param {boolean} inStaging The surface already has a staged change that replaces the template
 * @return {jQuery.Promise} Resolves with the closing data of the dialog
 */
ve.ui.MWCitationNeededContextItem.prototype.openReferenceDialog = function ( surface, inStaging ) {
	const surfaceModel = surface.getModel();
	if ( !inStaging ) {
		surfaceModel.pushStaging();
		surfaceModel.getFragment().removeContent().select();
	}

	const opened = ve.ui.actionFactory.create( 'window', surface ).open( 'reference' );
	if ( !opened ) {
		surfaceModel.popStaging();
		return ve.createDeferred().reject().promise();
	}
	// Use the closed promise, as the dialog changes the selection during teardown
	return opened.then( ( instance ) => instance.closed ).then(
		( data ) => {
			if ( data && data.action === 'insert' ) {
				surfaceModel.applyStaging();
			} else {
				surfaceModel.popStaging();
			}
			return data;
		},
		( ...error ) => {
			surfaceModel.popStaging();
			return ve.createDeferred().reject( ...error ).promise();
		}
	);
};

/**
 * Get a description of the citation needed template including date and reason
 *
 * @param {ve.dm.MWTransclusionNode} node Citation needed template node
 * @return {jQuery} Description elements
 */
ve.ui.MWCitationNeededContextItem.static.getDescription = function ( node ) {
	const date = this.getCanonicalParam( node, 'date' );
	let description = ve.msg( 'cite-ve-citationneeded-description' );

	if ( date ) {
		description += ve.msg( 'word-separator' ) + ve.msg( 'parentheses', date );
	}

	const $description = $( '<div>' ).append(
		$( '<p>' ).addClass( 've-ui-mwCitationNeededContextItem-description' ).text( description )
	);

	const reason = this.getCanonicalParam( node, 'reason' );
	if ( reason ) {
		$description.append(
			$( '<p>' ).addClass( 've-ui-mwCitationNeededContextItem-reason' ).append(
				document.createTextNode( ve.msg( 'cite-ve-citationneeded-reason' ) + ve.msg( 'word-separator' ) ),
				// TODO: reason could have HTML entities, but this is rare
				$( '<em>' ).text( reason )
			)
		);
	}

	return $description.contents();
};

/**
 * @override
 */
ve.ui.MWCitationNeededContextItem.prototype.renderBody = function () {
	this.$body.empty().append(
		this.constructor.static.getDescription( this.model ),
		this.addButton.$element
	);
};

module.exports = ve.ui.MWCitationNeededContextItem;
