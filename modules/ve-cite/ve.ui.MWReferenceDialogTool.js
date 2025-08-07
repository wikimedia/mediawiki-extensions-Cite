'use strict';

/*!
 * VisualEditor UserInterface MediaWiki reference dialog tool class.
 *
 * @copyright 2011-2018 VisualEditor Team's Cite sub-team and others; see AUTHORS.txt
 * @license MIT
 */

/**
 * MediaWiki UserInterface reference tool.
 *
 * @constructor
 * @extends ve.ui.FragmentWindowTool
 * @param {OO.ui.ToolGroup} toolGroup
 * @param {Object} [config] Configuration options
 */
ve.ui.MWReferenceDialogTool = function VeUiMWReferenceDialogTool() {
	ve.ui.MWReferenceDialogTool.super.apply( this, arguments );
};

OO.inheritClass( ve.ui.MWReferenceDialogTool, ve.ui.FragmentWindowTool );

ve.ui.MWReferenceDialogTool.static.name = 'reference';

ve.ui.MWReferenceDialogTool.static.group = 'object';

ve.ui.MWReferenceDialogTool.static.icon = 'reference';

// eslint-disable-next-line mediawiki/msg-doc
ve.ui.MWReferenceDialogTool.static.title = OO.ui.deferMsg(
	...mw.config.get( 'wgCiteVisualEditorOtherGroup' ) ?
		[ 'cite-ve-othergroup-item', ve.msg( 'cite-ve-dialogbutton-reference-tooltip' ) ] :
		[ 'cite-ve-dialogbutton-reference-tooltip' ]
);

ve.ui.MWReferenceDialogTool.static.modelClasses = [ ve.dm.MWReferenceNode ];

ve.ui.MWReferenceDialogTool.static.commandName = 'reference';

ve.ui.MWReferenceDialogTool.static.autoAddToCatchall = false;

ve.ui.toolFactory.register( ve.ui.MWReferenceDialogTool );

ve.ui.commandRegistry.register(
	new ve.ui.Command(
		'reference', 'window', 'open',
		{ args: [ 'reference' ], supportedSelections: [ 'linear' ] }
	)
);

/* If Citoid is installed these will be overridden */
ve.ui.sequenceRegistry.register(
	new ve.ui.Sequence( 'wikitextRef', 'reference', '<ref', 4 )
);

ve.ui.triggerRegistry.register(
	'reference', { mac: new ve.ui.Trigger( 'cmd+shift+k' ), pc: new ve.ui.Trigger( 'ctrl+shift+k' ) }
);

ve.ui.commandHelpRegistry.register( 'insert', 'ref', {
	trigger: 'reference',
	sequences: [ 'wikitextRef' ],
	label: OO.ui.deferMsg( 'cite-ve-dialog-reference-title' )
} );

ve.ui.mwWikitextTransferRegistry.register( 'reference', /<ref[^>]*>/ );

ve.ui.HelpCompletionAction.static.toolGroups.cite = { mergeWith: 'insert' };
