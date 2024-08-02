'use strict';

/*!
 * VisualEditor UserInterface MediaWiki MWReferenceDialog class.
 *
 * @copyright 2011-2018 VisualEditor Team's Cite sub-team and others; see AUTHORS.txt
 * @license MIT
 */

/**
 * Dialog for inserting, editing and re-using MediaWiki references.
 *
 * @constructor
 * @extends ve.ui.NodeDialog
 * @param {Object} [config] Configuration options
 */
ve.ui.MWReferenceDialog = function VeUiMWReferenceDialog( config ) {
	// Parent constructor
	ve.ui.MWReferenceDialog.super.call( this, config );

	// Properties
	this.referenceModel = null;
	this.reuseReference = false;
};

/* Inheritance */

OO.inheritClass( ve.ui.MWReferenceDialog, ve.ui.NodeDialog );

/* Static Properties */

ve.ui.MWReferenceDialog.static.name = 'reference';

ve.ui.MWReferenceDialog.static.title =
	OO.ui.deferMsg( 'cite-ve-dialog-reference-title' );

ve.ui.MWReferenceDialog.static.actions = [
	{
		action: 'done',
		label: OO.ui.deferMsg( 'visualeditor-dialog-action-apply' ),
		flags: [ 'progressive', 'primary' ],
		modes: 'edit'
	},
	{
		action: 'insert',
		label: OO.ui.deferMsg( 'visualeditor-dialog-action-insert' ),
		flags: [ 'progressive', 'primary' ],
		modes: 'insert'
	},
	{
		label: OO.ui.deferMsg( 'visualeditor-dialog-action-cancel' ),
		flags: [ 'safe', 'close' ],
		modes: [ 'readonly', 'insert', 'edit', 'insert-select' ]
	}
];

ve.ui.MWReferenceDialog.static.modelClasses = [ ve.dm.MWReferenceNode ];

/* Methods */

/**
 * Determine whether the reference document we're editing has any content.
 *
 * @return {boolean} Document has content
 */
ve.ui.MWReferenceDialog.prototype.documentHasContent = function () {
	// TODO: Check for other types of empty, e.g. only whitespace?
	return this.referenceModel && this.referenceModel.getDocument().data.hasContent();
};

/**
 * Determine whether any changes have been made (and haven't been undone).
 *
 * @return {boolean} Changes have been made
 */
ve.ui.MWReferenceDialog.prototype.isModified = function () {
	return this.documentHasContent() && this.editPanel.isModified();
};

/**
 * Handle reference target widget change events
 */
ve.ui.MWReferenceDialog.prototype.onTargetChange = function () {
	const hasContent = this.documentHasContent();

	this.actions.setAbilities( {
		done: this.isModified(),
		insert: hasContent
	} );

	if ( !this.trackedInputChange ) {
		ve.track( 'activity.' + this.constructor.static.name, { action: 'input' } );
		this.trackedInputChange = true;
	}
};

/**
 * Handle reference group input change events.
 */
ve.ui.MWReferenceDialog.prototype.onReferenceGroupInputChange = function () {
	this.actions.setAbilities( {
		done: this.isModified()
	} );

	if ( !this.trackedInputChange ) {
		ve.track( 'activity.' + this.constructor.static.name, { action: 'input' } );
		this.trackedInputChange = true;
	}
};

/**
 * Handle search results choose events.
 *
 * @param {ve.ui.MWReferenceResultWidget} item Chosen item
 */
ve.ui.MWReferenceDialog.prototype.onReuseSearchResultsChoose = function ( item ) {
	const ref = item.getData();

	if ( this.selectedNode instanceof ve.dm.MWReferenceNode ) {
		this.getFragment().removeContent();
		this.selectedNode = null;
	}

	this.insertReference( ref );

	ve.track( 'activity.' + this.constructor.static.name, { action: 'reuse-choose' } );

	this.close( { action: 'insert' } );
};

/**
 * @override
 */
ve.ui.MWReferenceDialog.prototype.getReadyProcess = function ( data ) {
	return ve.ui.MWReferenceDialog.super.prototype.getReadyProcess.call( this, data )
		.next( () => {
			if ( this.reuseReference ) {
				this.reuseSearch.getQuery().focus().select();
			} else {
				this.editPanel.referenceTarget.focus();
			}
		} );
};

/**
 * @override
 */
ve.ui.MWReferenceDialog.prototype.getBodyHeight = function () {
	// Clamp value to between 300 and 400px height, preferring the actual height if available
	return Math.min(
		400,
		Math.max(
			300,
			Math.ceil( this.panels.getCurrentItem().$element[ 0 ].scrollHeight )
		)
	);
};

/**
 * @override
 */
ve.ui.MWReferenceDialog.prototype.initialize = function () {
	// Parent method
	ve.ui.MWReferenceDialog.super.prototype.initialize.call( this );

	// Properties
	this.panels = new OO.ui.StackLayout();
	this.editPanel = new ve.ui.MWReferenceEditPanel( { $overlay: this.$overlay } );
	this.reuseSearchPanel = new OO.ui.PanelLayout();

	this.reuseSearch = new ve.ui.MWReferenceSearchWidget();

	// Events
	this.reuseSearch.getResults().connect( this, { choose: 'onReuseSearchResultsChoose' } );
	this.editPanel.referenceTarget.connect( this, { change: 'onTargetChange' } );
	this.editPanel.referenceGroupInput.connect( this, { change: 'onReferenceGroupInputChange' } );

	// Initialization
	this.$content.addClass( 've-ui-mwReferenceDialog' );

	this.panels.addItems( [ this.editPanel, this.reuseSearchPanel ] );
	this.reuseSearchPanel.$element.append( this.reuseSearch.$element );
	this.$body.append( this.panels.$element );
};

/**
 * Switches dialog to use existing reference mode.
 */
ve.ui.MWReferenceDialog.prototype.openReusePanel = function () {
	this.actions.setMode( 'insert-select' );
	this.reuseSearch.buildIndex();
	this.panels.setItem( this.reuseSearchPanel );
	this.reuseSearch.getQuery().focus().select();

	// https://phabricator.wikimedia.org/T362347
	ve.track( 'activity.' + this.constructor.static.name, { action: 'dialog-open-reuse' } );
};

/**
 * Insert a reference at the end of the selection, could also be a reuse of an exising reference
 *
 * @private
 * @param {ve.dm.MWReferenceModel} ref
 */
ve.ui.MWReferenceDialog.prototype.insertReference = function ( ref ) {
	const surfaceModel = this.getFragment().getSurface();

	if ( !ref.findInternalItem( surfaceModel ) ) {
		ref.insertInternalItem( surfaceModel );
	}
	// Collapse returns a new fragment, so update this.fragment
	this.fragment = this.getFragment().collapseToEnd();
	ref.insertReferenceNode( this.getFragment() );
};

/**
 * @override
 */
ve.ui.MWReferenceDialog.prototype.getActionProcess = function ( action ) {
	if ( action === 'insert' || action === 'done' ) {
		return new OO.ui.Process( () => {
			this.referenceModel.setGroup( this.editPanel.referenceGroupInput.getValue() );

			if ( !( this.selectedNode instanceof ve.dm.MWReferenceNode ) ) {
				this.insertReference( this.referenceModel );
			}

			this.referenceModel.updateInternalItem( this.getFragment().getSurface() );

			this.close( { action: action } );
		} );
	}
	return ve.ui.MWReferenceDialog.super.prototype.getActionProcess.call( this, action );
};

/**
 * @override
 * @param {Object} [data] Setup data
 * @param {boolean} [data.reuseReference=false] Open the dialog in "use existing reference" mode
 */
ve.ui.MWReferenceDialog.prototype.getSetupProcess = function ( data ) {
	data = data || {};
	return ve.ui.MWReferenceDialog.super.prototype.getSetupProcess.call( this, data )
		.next( () => {
			this.panels.setItem( this.editPanel );
			this.editPanel.setInternalList( this.getFragment().getDocument().getInternalList() );

			if ( this.selectedNode instanceof ve.dm.MWReferenceNode ) {
				this.referenceModel = ve.dm.MWReferenceModel.static.newFromReferenceNode( this.selectedNode );
			} else {
				this.referenceModel = new ve.dm.MWReferenceModel( this.getFragment().getDocument() );
				this.actions.setAbilities( { done: false, insert: false } );
			}
			this.editPanel.setReferenceForEditing( this.referenceModel );
			this.editPanel.setReadOnly( this.isReadOnly() );

			this.reuseSearch.setInternalList( this.getFragment().getDocument().getInternalList() );

			this.reuseReference = !!data.reuseReference;
			if ( this.reuseReference ) {
				this.openReusePanel();
			}
			this.actions.setAbilities( {
				done: false
			} );

			this.trackedInputChange = false;
		} );
};

/**
 * @override
 */
ve.ui.MWReferenceDialog.prototype.getTeardownProcess = function ( data ) {
	return ve.ui.MWReferenceDialog.super.prototype.getTeardownProcess.call( this, data )
		.first( () => {
			this.editPanel.referenceTarget.getSurface().getModel().disconnect( this );
			this.editPanel.clear();
			this.reuseSearch.clearSearch();
			this.referenceModel = null;
		} );
};

/* Registration */

ve.ui.windowFactory.register( ve.ui.MWReferenceDialog );
