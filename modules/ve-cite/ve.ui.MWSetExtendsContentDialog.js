'use strict';

/*!
 * VisualEditor UserInterface MWSetExtendsContentDialog class.
 *
 * @copyright 2024 VisualEditor Team's Cite sub-team and others; see AUTHORS.txt
 * @license MIT
 */

/**
 * Dialog for inserting a sub-reference from a main reference
 *
 * @class
 * @extends OO.ui.ProcessDialog
 *
 * @constructor
 * @param {Object} [config] Configuration options
 */
ve.ui.MWSetExtendsContentDialog = function VeUiMWSetExtendsContentDialog( config ) {
	// Parent constructor
	ve.ui.MWSetExtendsContentDialog.super.call( this, config );
};

/* Inheritance */

OO.inheritClass( ve.ui.MWSetExtendsContentDialog, OO.ui.ProcessDialog );

/* Static Properties */

ve.ui.MWSetExtendsContentDialog.static.name = 'setExtendsContent';

ve.ui.MWSetExtendsContentDialog.static.size = 'medium';

// TODO extends i18n
ve.ui.MWSetExtendsContentDialog.static.title = OO.ui.deferMsg( 'cite-ve-dialog-reference-title-add-details' );

ve.ui.MWSetExtendsContentDialog.static.actions = [
	{
		action: 'insert',
		label: OO.ui.deferMsg( 'visualeditor-dialog-action-insert' ),
		flags: [ 'progressive', 'primary' ],
		modes: 'insert'
	},
	{
		label: OO.ui.deferMsg( 'visualeditor-dialog-action-cancel' ),
		flags: [ 'safe', 'close' ],
		modes: [ 'insert' ]
	}
];

/* Methods */

/**
 * @inheritdoc
 */
ve.ui.MWSetExtendsContentDialog.prototype.initialize = function () {
	// Parent method
	ve.ui.MWSetExtendsContentDialog.super.prototype.initialize.apply( this, arguments );

	this.editPanel = new OO.ui.PanelLayout( {
		scrollable: true, padded: true
	} );

	this.$extendsNote = $( '<div>' )
		.addClass( 've-ui-setExtendsContentDialog-note' )
		.text( ve.msg( 'cite-ve-dialog-reference-editing-add-details' ) );

	this.referenceTarget = ve.init.target.createTargetWidget(
		{
			includeCommands: null,
			excludeCommands: ve.ui.MWReferenceEditPanel.static.getExcludeCommands(),
			importRules: ve.ui.MWReferenceEditPanel.static.getImportRules(),
			inDialog: this.constructor.static.name,
			placeholder: ve.msg( 'cite-ve-dialog-reference-editing-add-details-placeholder' )
		}
	);

	this.contentFieldset = new OO.ui.FieldsetLayout();
	this.contentFieldset.$element.append( this.referenceTarget.$element );

	this.editPanel.$element.append( this.$extendsNote, this.contentFieldset.$element );

	this.$body.append( this.editPanel.$element );
};

/**
 * @inheritdoc
 */
ve.ui.MWSetExtendsContentDialog.prototype.getSetupProcess = function ( data ) {
	return ve.ui.MWSetExtendsContentDialog.super.prototype.getSetupProcess.call( this, data )
		.next( () => {
			this.originalRef = data.originalRef;
			this.newRef = data.newRef;

			const parentItemNode = data.internalList.getItemNode( this.originalRef.getListIndex() );
			const $parentRefPreview = new ve.ui.MWPreviewElement( parentItemNode, { useView: true } ).$element;
			this.$extendsNote.find( '.ve-ui-mwPreviewElement' ).remove();
			this.$extendsNote.append( $parentRefPreview );

			this.referenceTarget.setDocument( this.newRef.getDocument() );
		} );
};

/**
 * @override
 */
ve.ui.MWSetExtendsContentDialog.prototype.getActionProcess = function ( action ) {
	if ( action === 'insert' ) {
		return new OO.ui.Process( () => {
			this.close( { action: action } );
		} );
	}
	return ve.ui.MWSetExtendsContentDialog.super.prototype.getActionProcess.call( this, action );
};

/**
 * @inheritdoc
 */
ve.ui.MWSetExtendsContentDialog.prototype.getReadyProcess = function ( data ) {
	return ve.ui.MWSetExtendsContentDialog.super.prototype.getReadyProcess.call( this, data )
		.next( () => {
			this.referenceTarget.focus();
		} );
};

/**
 * @inheritdoc
 */
ve.ui.MWSetExtendsContentDialog.prototype.getTeardownProcess = function ( data ) {
	return ve.ui.MWSetExtendsContentDialog.super.prototype.getTeardownProcess.call( this, data )
		.next( () => {
			if ( data && data.action && data.action === 'insert' ) {
				this.newRef.setDocument( this.referenceTarget.getContent() );
			}
			this.referenceTarget.clear();
		} );
};

/**
 * @inheritdoc
 */
ve.ui.MWSetExtendsContentDialog.prototype.getBodyHeight = function () {
	// Temporarily copy-pasted from ve.ui.MWReferenceDialog.getBodyHeight, keep in sync!
	return Math.min(
		400,
		Math.max(
			300,
			Math.ceil( this.editPanel.$element[ 0 ].scrollHeight )
		)
	);
};

/* Registration */

ve.ui.windowFactory.register( ve.ui.MWSetExtendsContentDialog );
