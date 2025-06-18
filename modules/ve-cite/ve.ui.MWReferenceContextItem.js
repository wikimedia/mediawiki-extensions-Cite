'use strict';

/*!
 * VisualEditor MWReferenceContextItem class.
 *
 * @copyright 2011-2018 VisualEditor Team's Cite sub-team and others; see AUTHORS.txt
 * @license MIT
 */

/**
 * Context item for a MWReference.
 *
 * @constructor
 * @extends ve.ui.LinearContextItem
 * @param {ve.ui.LinearContext} context Context the item is in
 * @param {ve.dm.Model} model Model the item is related to
 * @param {Object} [config]
 */
ve.ui.MWReferenceContextItem = function VeUiMWReferenceContextItem() {
	// Parent constructor
	ve.ui.MWReferenceContextItem.super.apply( this, arguments );
	/** @member {ve.ui.MWPreviewElement} */
	this.view = null;
	/** @member {ve.ui.MWPreviewElement} */
	this.detailsView = null;
	/** @member {ve.dm.MWGroupReferences} */
	this.groupRefs = null;
	// Initialization
	this.$element.addClass( 've-ui-mwReferenceContextItem' );
};

/* Inheritance */

OO.inheritClass( ve.ui.MWReferenceContextItem, ve.ui.LinearContextItem );

/* Static Properties */

ve.ui.MWReferenceContextItem.static.name = 'reference';

ve.ui.MWReferenceContextItem.static.icon = 'reference';

ve.ui.MWReferenceContextItem.static.label = OO.ui.deferMsg( 'cite-ve-dialogbutton-reference-title' );

ve.ui.MWReferenceContextItem.static.modelClasses = [ ve.dm.MWReferenceNode ];

ve.ui.MWReferenceContextItem.static.commandName = 'reference';

/* Methods */

/**
 * Get a DOM rendering of a normal reference, or the main ref for a details
 * reference.
 *
 * @private
 * @return {jQuery} DOM rendering of reference
 */
ve.ui.MWReferenceContextItem.prototype.getMainRefPreview = function () {
	// Render a placeholder for missing refs.
	let refNode = this.getReferenceNode();
	let errorMsgKey = 'cite-ve-referenceslist-missingref';

	// Render main ref if this is a subref, or a placeholder if missing.
	const mainRefKey = this.model.getAttribute( 'mainRefKey' );
	if ( mainRefKey && refNode ) {
		refNode = this.groupRefs.getInternalModelNode( mainRefKey );
		errorMsgKey = 'cite-ve-dialog-reference-missing-parent-ref';
	}

	if ( !refNode ) {
		return $( '<div>' )
			.addClass( 've-ui-mwReferenceContextItem-muted' )
			// The following messages are used here:
			// * cite-ve-referenceslist-missingref
			// * cite-ve-dialog-reference-missing-parent-ref
			.text( ve.msg( errorMsgKey ) );
	}

	// Render normal ref.
	this.view = new ve.ui.MWPreviewElement( refNode, { useView: true } );
	// The $element property may be rendered into asynchronously, update the
	// context's size when the rendering is complete if that's the case
	this.view.once( 'render', this.context.updateDimensions.bind( this.context ) );

	return this.view.$element;
};

/**
 * Get a preview of the reference details.
 *
 * @private
 * @return {jQuery|undefined}
 */
ve.ui.MWReferenceContextItem.prototype.getDetailsPreview = function () {
	if ( !this.model.getAttribute( 'mainRefKey' ) ) {
		return;
	}

	const buttonLabel = ve.msg( this.isReadOnly() ?
		'visualeditor-contextitemwidget-label-view' :
		'visualeditor-contextitemwidget-label-secondary'
	);
	const editDetails = new OO.ui.Layout( {
		classes: [ 've-ui-mwReferenceContextItem-subrefHeader' ],
		content: [
			new OO.ui.LabelWidget( {
				label: ve.msg( 'cite-ve-reference-contextitem-reused-header' )
			} ),
			new OO.ui.ButtonWidget( this.context.isMobile() ?
				{
					framed: false,
					invisibleLabel: true,
					icon: this.isReadOnly() ? 'eye' : 'edit',
					label: buttonLabel,
					classes: [ 've-ui-mwReferenceMobileContextItem-editButton' ]
				} :
				{
					label: buttonLabel,
					classes: [ 've-ui-mwReferenceContextItem-editButton' ]
				}
			).on( 'click', this.onEditSubref.bind( this ) )
		]
	} );

	this.detailsView = new ve.ui.MWPreviewElement( this.getReferenceNode(), { useView: true } );
	// The $element property may be rendered into asynchronously, update the
	// context's size when the rendering is complete if that's the case
	this.detailsView.once( 'render', this.context.updateDimensions.bind( this.context ) );

	return new OO.ui.Layout( { content: [ editDetails, this.detailsView ] } ).$element;
};

/**
 * Override default edit button, when a subref is present.
 */
ve.ui.MWReferenceContextItem.prototype.onEditButtonClick = function () {
	const mainRefKey = this.model.getAttribute( 'mainRefKey' );
	if ( !mainRefKey ) {
		ve.ui.LinearContextItem.prototype.onEditButtonClick.apply( this );
		return;
	}

	// Edit the main ref--like when editing a list-defined ref!
	// TODO: Make this into a reusable command.
	const groupRefs = ve.dm.MWDocumentReferences.static
		.refsForDoc( this.getFragment().getDocument() )
		.getGroupRefs( this.model.getAttribute( 'listGroup' ) );
	const mainRefNode = groupRefs.getRefNode( mainRefKey );
	const mainModelItem = ve.ui.contextItemFactory.getRelatedItems( [ mainRefNode ] )
		.find( ( item ) => item.name !== 'mobileActions' );

	if ( mainModelItem ) {
		const mainContextItem = ve.ui.contextItemFactory.lookup( mainModelItem.name );
		if ( mainContextItem ) {
			const surface = this.context.getSurface();
			const command = surface.commandRegistry.lookup( mainContextItem.static.commandName );
			const fragmentArgs = {
				fragment: surface.getModel().getLinearFragment(
					mainRefNode.getOuterRange(),
					true
				),
				selectFragmentOnClose: false
			};
			const newArgs = ve.copy( command.args );
			if ( command.name === 'reference' ) {
				newArgs[ 1 ] = fragmentArgs;
			} else {
				ve.extendObject( newArgs[ 0 ], fragmentArgs );
			}
			command.execute( surface, newArgs, 'context' );
		}
	}
};

/**
 * @private
 */
ve.ui.MWReferenceContextItem.prototype.onEditSubref = function () {
	ve.ui.LinearContextItem.prototype.onEditButtonClick.apply( this );
};

/**
 * Get a DOM rendering of a warning if this reference is reused.
 *
 * @private
 * @return {jQuery|undefined}
 */
ve.ui.MWReferenceContextItem.prototype.getReuseWarning = function () {
	const listKey = this.model.getAttribute( 'mainRefKey' ) || this.model.getAttribute( 'listKey' );
	const totalUsageCount = this.groupRefs.getTotalUsageCount( listKey );

	if ( totalUsageCount > 1 ) {
		if ( mw.config.get( 'wgCiteSubReferencing' ) ) {
			const label = new OO.ui.LabelWidget( {
				classes: [ 've-ui-mwReferenceContextItem-reuse' ],
				label: ve.msg( 'cite-ve-dialog-reference-editing-reused-short', totalUsageCount )
			} );
			label.$element.prepend( new OO.ui.IconWidget( { icon: 'infoFilled' } ).$element );
			return new OO.ui.Layout( {
				classes: [ 've-ui-mwReferenceContextItem-reuse-layout' ],
				content: [ label ]
			} ).$element;
		} else {
			return $( '<div>' )
				.addClass( 've-ui-mwReferenceContextItem-muted' )
				.text( ve.msg( 'cite-ve-dialog-reference-editing-reused', totalUsageCount ) );
		}
	}
};

/**
 * Get a DOM rendering of a button to add details.
 *
 * @private
 * @return {jQuery|undefined}
 */
ve.ui.MWReferenceContextItem.prototype.getAddDetailsButton = function () {
	if ( !mw.config.get( 'wgCiteSubReferencing' ) || this.model.getAttribute( 'mainRefKey' ) ) {
		return;
	}

	const listKey = this.model.getAttribute( 'listKey' );
	if ( this.groupRefs.getTotalUsageCount( listKey ) < 2 ) {
		return;
	}

	return new OO.ui.ButtonWidget( {
		label: ve.msg( 'cite-ve-dialog-reference-add-details-button' )
	} ).on( 'click', () => {
		const ref = ve.dm.MWReferenceModel.static.newFromReferenceNode( this.model );
		ve.ui.commandRegistry.lookup( 'reference' ).execute(
			this.context.getSurface(),
			// Arguments for calling ve.ui.MWReferenceDialog.getSetupProcess()
			[ 'reference', { createSubRef: ref, addToExisting: true } ],
			'context'
		);
		// TODO: When the dialog closes successfully, the new
		// subref replaces the previously selected main ref and
		// becomes a main+details.
	} ).$element;
};

/**
 * Get the reference node in the containing document (not the internal list document)
 *
 * @return {ve.dm.InternalItemNode|null} Reference item node
 */
ve.ui.MWReferenceContextItem.prototype.getReferenceNode = function () {
	if ( !this.model.isEditable() ) {
		return null;
	}
	if ( !this.referenceNode ) {
		this.referenceNode = this.groupRefs.getInternalModelNode( this.model.getAttribute( 'listKey' ) );
	}
	return this.referenceNode;
};

/**
 * @override
 */
ve.ui.MWReferenceContextItem.prototype.getDescription = function () {
	return this.model.isEditable() ? this.getMainRefPreview().text() : ve.msg( 'cite-ve-referenceslist-missingref' );
};

/**
 * @override
 */
ve.ui.MWReferenceContextItem.prototype.setup = function () {
	this.groupRefs = ve.dm.MWDocumentReferences.static.refsForDoc( this.getFragment().getDocument() )
		.getGroupRefs( this.model.getAttribute( 'listGroup' ) );

	// Parent method
	return ve.ui.MWReferenceContextItem.super.prototype.setup.apply( this, arguments );
};

/**
 * @override
 */
ve.ui.MWReferenceContextItem.prototype.renderBody = function () {
	this.$body.empty().append(
		this.getMainRefPreview(),
		this.getReuseWarning(),
		this.getDetailsPreview(),
		this.getAddDetailsButton()
	);
};

/**
 * @override
 */
ve.ui.MWReferenceContextItem.prototype.teardown = function () {
	if ( this.view ) {
		this.view.destroy();
	}
	if ( this.detailsView ) {
		this.detailsView.destroy();
	}

	// Call parent
	ve.ui.MWReferenceContextItem.super.prototype.teardown.call( this );
};

/* Registration */

ve.ui.contextItemFactory.register( ve.ui.MWReferenceContextItem );
