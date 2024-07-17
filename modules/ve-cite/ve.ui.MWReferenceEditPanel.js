'use strict';

/*!
 * VisualEditor UserInterface MWReferenceResultWidget class.
 *
 * @copyright 2011-2018 VisualEditor Team's Cite sub-team and others; see AUTHORS.txt
 * @license MIT
 */

/**
 * Creates a ve.ui.MWReferenceEditPanel object.
 *
 * @constructor
 * @extends OO.ui.PanelLayout
 * @param {Object} config
 * @param {jQuery} config.$overlay Layer to render options dropdown outside of the parent dialog
 */
ve.ui.MWReferenceEditPanel = function VeUiMWReferenceEditPanel( config ) {
	// Configuration initialization
	config = Object.assign( {
		scrollable: true,
		padded: true
	}, config );

	// Parent constructor
	ve.ui.MWReferenceEditPanel.super.call( this, { scrollable: true, padded: true } );

	// Initialization
	this.$element.addClass( 've-ui-mwReferenceEditPanel' );

	// Properties

	// Create content editor
	this.referenceTarget = ve.init.target.createTargetWidget(
		{
			includeCommands: null,
			excludeCommands: this.constructor.static.getExcludeCommands(),
			importRules: this.constructor.static.getImportRules(),
			inDialog: 'reference',
			placeholder: ve.msg( 'cite-ve-dialog-reference-placeholder' )
		}
	);
	this.contentFieldset = new OO.ui.FieldsetLayout();
	this.contentFieldset.$element.append(
		this.referenceTarget.$element
	);

	// Create group edit
	this.optionsFieldset = new OO.ui.FieldsetLayout( {
		label: ve.msg( 'cite-ve-dialog-reference-options-section' ),
		icon: 'settings'
	} );
	this.referenceGroupInput = new ve.ui.MWReferenceGroupInputWidget( {
		$overlay: config.$overlay,
		emptyGroupName: ve.msg( 'cite-ve-dialog-reference-options-group-placeholder' )
	} );
	this.referenceGroupField = new OO.ui.FieldLayout( this.referenceGroupInput, {
		align: 'top',
		label: ve.msg( 'cite-ve-dialog-reference-options-group-label' )
	} );
	this.optionsFieldset.addItems( [ this.referenceGroupField ] );

	// Create warning messages
	this.reuseWarning = new OO.ui.MessageWidget( {
		icon: 'alert',
		inline: true,
		classes: [ 've-ui-mwReferenceDialog-reuseWarning' ]
	} );

	this.extendsWarning = new OO.ui.MessageWidget( {
		icon: 'alert',
		inline: true,
		classes: [ 've-ui-mwReferenceDialog-extendsWarning' ]
	} );

	// Append to panel element
	this.$element.append(
		this.reuseWarning.$element,
		this.extendsWarning.$element,
		this.contentFieldset.$element,
		this.optionsFieldset.$element
	);
};

/* Inheritance */

OO.inheritClass( ve.ui.MWReferenceEditPanel, OO.ui.PanelLayout );

/* Static Properties */
ve.ui.MWReferenceEditPanel.static.excludeCommands = [
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
 * Get the list of disallowed commands for the surface widget to edit the content. This includes
 * all Cite related commands to disencourage nesting of references.
 *
 * @see ve.dm.ElementLinearData#sanitize
 * @return {Object} Import rules
 */
ve.ui.MWReferenceEditPanel.static.getExcludeCommands = function () {
	const citeCommands = Object.keys( ve.init.target.getSurface().commandRegistry.registry )
		.filter( ( command ) => command.indexOf( 'cite-' ) !== -1 );

	return ve.ui.MWReferenceEditPanel.static.excludeCommands.concat( citeCommands );
};

/**
 * Get the import rules for the surface widget to edit the content
 *
 * @see ve.dm.ElementLinearData#sanitize
 * @return {Object} Import rules
 */
ve.ui.MWReferenceEditPanel.static.getImportRules = function () {
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
