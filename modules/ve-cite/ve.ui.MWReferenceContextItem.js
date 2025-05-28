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
	this.view = null;
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
 * Get a DOM rendering of the reference.
 *
 * @private
 * @return {jQuery} DOM rendering of reference
 */
ve.ui.MWReferenceContextItem.prototype.getRendering = function () {
	const refNode = this.getReferenceNode();
	if ( !refNode ) {
		return $( '<div>' )
			.addClass( 've-ui-mwReferenceContextItem-muted' )
			.text( ve.msg( 'cite-ve-referenceslist-missingref' ) );
	}

	let editDetails;
	if ( this.model.getAttribute( 'extendsRef' ) ) {
		const buttonLabel = ve.msg( this.isReadOnly() ?
			'visualeditor-contextitemwidget-label-view' :
			'visualeditor-contextitemwidget-label-secondary'
		);
		editDetails = new OO.ui.Layout( {
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
	}

	this.view = new ve.ui.MWPreviewElement( refNode );
	// The $element property may be rendered into asynchronously, update the
	// context's size when the rendering is complete if that's the case
	this.view.once( 'render', this.context.updateDimensions.bind( this.context ) );

	return new OO.ui.Layout( { content: [ editDetails, this.view ] } ).$element;
};

/**
 * Override default edit button, when a subref is present.
 */
ve.ui.MWReferenceContextItem.prototype.onEditButtonClick = function () {
	const extendsRef = this.model.getAttribute( 'extendsRef' );
	if ( !extendsRef ) {
		ve.ui.LinearContextItem.prototype.onEditButtonClick.apply( this );
		return;
	}

	// Edit the main ref--like when editing a list-defined ref!
	// TODO: Make this into a reusable command.
	const groupRefs = ve.dm.MWDocumentReferences.static
		.refsForDoc( this.getFragment().getDocument() )
		.getGroupRefs( this.model.getAttribute( 'listGroup' ) );
	const mainRefNode = groupRefs.getRefNode( extendsRef );
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
			newArgs[ 1 ] = fragmentArgs;
			command.execute( surface, newArgs );
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
	const listKey = this.model.getAttribute( 'extendsRef' ) || this.model.getAttribute( 'listKey' );
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
ve.ui.MWReferenceContextItem.prototype.getDetailsButton = function () {
	if ( !mw.config.get( 'wgCiteSubReferencing' ) || this.model.getAttribute( 'extendsRef' ) ) {
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
			[ 'reference', { createSubRef: ref } ],
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
	return this.model.isEditable() ? this.getRendering().text() : ve.msg( 'cite-ve-referenceslist-missingref' );
};

/**
 * Get the text of the parent reference.
 *
 * @private
 * @return {jQuery|null}
 */
ve.ui.MWReferenceContextItem.prototype.getParentRef = function () {
	const extendsRef = this.model.getAttribute( 'extendsRef' );
	if ( !extendsRef ) {
		return null;
	}
	const parentNode = this.groupRefs.getInternalModelNode( extendsRef );
	return parentNode ? new ve.ui.MWPreviewElement( parentNode, { useView: true } ).$element :
		$( '<div>' )
			.addClass( 've-ui-mwReferenceContextItem-muted' )
			.text( ve.msg( 'cite-ve-dialog-reference-missing-parent-ref' ) );
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
		this.getParentRef(),
		this.getRendering(),
		this.getReuseWarning(),
		this.getDetailsButton()
	);
};

/**
 * @override
 */
ve.ui.MWReferenceContextItem.prototype.teardown = function () {
	if ( this.view ) {
		this.view.destroy();
	}

	// Call parent
	ve.ui.MWReferenceContextItem.super.prototype.teardown.call( this );
};

/* Registration */

ve.ui.contextItemFactory.register( ve.ui.MWReferenceContextItem );
