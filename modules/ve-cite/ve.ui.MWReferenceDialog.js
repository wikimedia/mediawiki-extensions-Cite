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

ve.ui.MWReferenceDialog.static.includeCommands = null;

ve.ui.MWReferenceDialog.static.excludeCommands = [
	// No formatting
	'paragraph',
	'heading1',
	'heading2',
	'heading3',
	'heading4',
	'heading5',
	'heading6',
	'preformatted',
	'blockquote',
	// No tables
	'insertTable',
	'deleteTable',
	'mergeCells',
	'tableCaption',
	'tableCellHeader',
	'tableCellData',
	// No structure
	'bullet',
	'bulletWrapOnce',
	'number',
	'numberWrapOnce',
	'indent',
	'outdent',
	// References
	'reference',
	'reference/existing',
	'citoid',
	'referencesList'
];

/**
 * Get the import rules for the surface widget in the dialog.
 *
 * @see ve.dm.ElementLinearData#sanitize
 * @return {Object} Import rules
 */
ve.ui.MWReferenceDialog.static.getImportRules = function () {
	const rules = ve.copy( ve.init.target.constructor.static.importRules );
	return ve.extendObject(
		rules,
		{
			all: {
				blacklist: ve.extendObject(
					{
						// Nested references are impossible
						mwReference: true,
						mwReferencesList: true,
						// Lists and tables are actually possible in wikitext with a leading
						// line break but we prevent creating these with the UI
						list: true,
						listItem: true,
						definitionList: true,
						definitionListItem: true,
						table: true,
						tableCaption: true,
						tableSection: true,
						tableRow: true,
						tableCell: true,
						mwTable: true,
						mwTransclusionTableCell: true
					},
					ve.getProp( rules, 'all', 'blacklist' )
				),
				// Headings are not possible in wikitext without HTML
				conversions: ve.extendObject(
					{
						mwHeading: 'paragraph'
					},
					ve.getProp( rules, 'all', 'conversions' )
				)
			}
		}
	);
};

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

/*
 * Determine whether any changes have been made (and haven't been undone).
 *
 * @return {boolean} Changes have been made
 */
ve.ui.MWReferenceDialog.prototype.isModified = function () {
	return this.documentHasContent() &&
		( this.referenceTarget.hasBeenModified() ||
		this.referenceGroupInput.getValue() !== this.originalGroup );
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

	this.setFormFieldsFromRef( ref );
	this.referenceModel = ref;
	this.executeAction( 'insert' );

	ve.track( 'activity.' + this.constructor.static.name, { action: 'reuse-choose' } );
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
				this.referenceTarget.focus();
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
 * Work on a specific reference.
 *
 * @param {ve.dm.MWReferenceModel} ref
 * @return {ve.ui.MWReferenceDialog}
 * @chainable
 */
ve.ui.MWReferenceDialog.prototype.setReferenceForEditing = function ( ref ) {
	this.referenceModel = ref;

	this.setFormFieldsFromRef( this.referenceModel );
	this.updateReuseWarningFromRef( this.referenceModel );
	this.updateExtendsWarningFromRef( this.referenceModel );

	return this;
};

/**
 * @private
 * @param {ve.dm.MWReferenceModel} ref
 */
ve.ui.MWReferenceDialog.prototype.setFormFieldsFromRef = function ( ref ) {
	this.referenceTarget.setDocument( ref.getDocument() );

	this.originalGroup = ref.getGroup();
	// Set the group input while it's disabled, so this doesn't pop up the group-picker menu
	this.referenceGroupInput.setDisabled( true );
	this.referenceGroupInput.setValue( this.originalGroup );
	this.referenceGroupInput.setDisabled( false );
};

/**
 * @private
 * @param {ve.dm.MWReferenceModel} ref
 */
ve.ui.MWReferenceDialog.prototype.updateReuseWarningFromRef = function ( ref ) {
	const group = this.getFragment().getDocument().getInternalList()
		.getNodeGroup( ref.getListGroup() );
	const nodes = ve.getProp( group, 'keyedNodes', ref.getListKey() );
	const usages = nodes ? nodes.filter(
		( node ) => !node.findParent( ve.dm.MWReferencesListNode )
	).length : 0;

	this.reuseWarning
		.toggle( usages > 1 )
		.setLabel( mw.msg( 'cite-ve-dialog-reference-editing-reused-long', usages ) );
};

/**
 * @private
 * @param {ve.dm.MWReferenceModel} ref
 */
ve.ui.MWReferenceDialog.prototype.updateExtendsWarningFromRef = function ( ref ) {
	if ( ref.extendsRef ) {
		const list = this.getFragment().getDocument().getInternalList();
		const itemNode = list.getItemNode( list.keys.indexOf( ref.extendsRef ) );
		const parentRefText = new ve.ui.MWPreviewElement( itemNode, { useView: true } ).$element.text();
		// TODO extends i18n
		this.extendsWarning.setLabel( new OO.ui.HtmlSnippet(
			`${ mw.msg( 'cite-ve-dialog-reference-editing-extends' ) }</br>${ parentRefText }`
		) );
	}

	this.extendsWarning.toggle( !!ref.extendsRef );
};

/**
 * @override
 */
ve.ui.MWReferenceDialog.prototype.initialize = function () {
	// Parent method
	ve.ui.MWReferenceDialog.super.prototype.initialize.call( this );

	// Properties
	this.panels = new OO.ui.StackLayout();
	this.editPanel = new OO.ui.PanelLayout( {
		scrollable: true, padded: true
	} );
	this.reuseSearchPanel = new OO.ui.PanelLayout();

	this.reuseWarning = new OO.ui.MessageWidget( {
		inline: true,
		icon: 'alert',
		classes: [ 've-ui-mwReferenceDialog-reuseWarning' ]
	} );

	// Icon message widget
	this.extendsWarning = new OO.ui.MessageWidget( {
		icon: 'alert',
		inline: true,
		classes: [ 've-ui-mwReferenceDialog-extendsWarning' ]
	} );

	const citeCommands = Object.keys( ve.init.target.getSurface().commandRegistry.registry )
		.filter( ( command ) => command.indexOf( 'cite-' ) !== -1 );

	this.referenceTarget = ve.init.target.createTargetWidget(
		{
			includeCommands: this.constructor.static.includeCommands,
			excludeCommands: this.constructor.static.excludeCommands.concat( citeCommands ),
			importRules: this.constructor.static.getImportRules(),
			inDialog: this.constructor.static.name,
			placeholder: ve.msg( 'cite-ve-dialog-reference-placeholder' )
		}
	);

	this.contentFieldset = new OO.ui.FieldsetLayout();
	this.optionsFieldset = new OO.ui.FieldsetLayout( {
		label: ve.msg( 'cite-ve-dialog-reference-options-section' ),
		icon: 'settings'
	} );
	this.contentFieldset.$element.append( this.referenceTarget.$element );

	this.referenceGroupInput = new ve.ui.MWReferenceGroupInputWidget( {
		$overlay: this.$overlay,
		emptyGroupName: ve.msg( 'cite-ve-dialog-reference-options-group-placeholder' )
	} );
	this.referenceGroupInput.connect( this, { change: 'onReferenceGroupInputChange' } );
	this.referenceGroupField = new OO.ui.FieldLayout( this.referenceGroupInput, {
		align: 'top',
		label: ve.msg( 'cite-ve-dialog-reference-options-group-label' )
	} );
	this.reuseSearch = new ve.ui.MWReferenceSearchWidget();

	// Events
	this.reuseSearch.getResults().connect( this, { choose: 'onReuseSearchResultsChoose' } );
	this.referenceTarget.connect( this, { change: 'onTargetChange' } );

	// Initialization
	this.$content.addClass( 've-ui-mwReferenceDialog' );

	this.panels.addItems( [ this.editPanel, this.reuseSearchPanel ] );
	this.editPanel.$element.append(
		this.reuseWarning.$element, this.extendsWarning.$element, this.contentFieldset.$element, this.optionsFieldset.$element );
	this.optionsFieldset.addItems( [ this.referenceGroupField ] );
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
 * @override
 */
ve.ui.MWReferenceDialog.prototype.getActionProcess = function ( action ) {
	if ( action === 'insert' || action === 'done' ) {
		return new OO.ui.Process( () => {
			const surfaceModel = this.getFragment().getSurface();

			this.referenceModel.setGroup( this.referenceGroupInput.getValue() );

			// Insert reference (will auto-create an internal item if needed)
			if ( !( this.selectedNode instanceof ve.dm.MWReferenceNode ) ) {
				if ( !this.referenceModel.findInternalItem( surfaceModel ) ) {
					this.referenceModel.insertInternalItem( surfaceModel );
				}
				// Collapse returns a new fragment, so update this.fragment
				this.fragment = this.getFragment().collapseToEnd();
				this.referenceModel.insertReferenceNode( this.getFragment() );
			}

			// Update internal item
			this.referenceModel.updateInternalItem( surfaceModel );

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
			if ( this.selectedNode instanceof ve.dm.MWReferenceNode ) {
				this.setReferenceForEditing(
					ve.dm.MWReferenceModel.static.newFromReferenceNode( this.selectedNode )
				);
			} else {
				this.setReferenceForEditing( new ve.dm.MWReferenceModel( this.getFragment().getDocument() ) );
				this.actions.setAbilities( { done: false, insert: false } );
			}

			this.reuseSearch.setInternalList( this.getFragment().getDocument().getInternalList() );

			const isReadOnly = this.isReadOnly();
			this.referenceTarget.setReadOnly( isReadOnly );
			this.referenceGroupInput.setReadOnly( isReadOnly );

			this.reuseReference = !!data.reuseReference;
			if ( this.reuseReference ) {
				this.openReusePanel();
			}
			this.actions.setAbilities( {
				done: false
			} );

			this.referenceGroupInput.populateMenu(
				this.getFragment().getDocument().getInternalList() );

			this.trackedInputChange = false;
		} );
};

/**
 * @override
 */
ve.ui.MWReferenceDialog.prototype.getTeardownProcess = function ( data ) {
	return ve.ui.MWReferenceDialog.super.prototype.getTeardownProcess.call( this, data )
		.first( () => {
			this.referenceTarget.getSurface().getModel().disconnect( this );
			this.reuseSearch.getQuery().setValue( '' );
			this.referenceTarget.clear();
			this.referenceModel = null;
		} );
};

/* Registration */

ve.ui.windowFactory.register( ve.ui.MWReferenceDialog );
