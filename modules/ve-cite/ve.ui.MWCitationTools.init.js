'use strict';

/*!
 * VisualEditor MediaWiki Cite initialisation code for the citeation tools.
 *
 * @copyright 2026 VisualEditor Team's Cite sub-team and others; see AUTHORS.txt
 * @license MIT
 */

( function () {
	/**
	 * Add reference insertion tools from on-wiki data.
	 *
	 * By adding a definition in JSON to MediaWiki:Cite-tool-definition.json, the cite menu can
	 * be populated with tools that create references containing specific templates. The
	 * content of the definition should be an array containing a series of
	 * objects, one for each tool. Each object must contain a `name`, `icon` and
	 * `template` property. An optional `title` property can also be used to
	 * define the tool title in plain text. The `name` property is a unique
	 * identifier for the tool, and also provides a fallback title for the tool by
	 * being transformed into a message key. The name is prefixed with
	 * `visualeditor-cite-tool-name-`, and messages can be defined on Wiki. Some
	 * common messages are pre-defined for tool names such as `web`, `book`,
	 * `news`, `map` and `journal`.
	 *
	 * Example:
	 * [ { "name": "web", "icon": "browser", "template": "Cite web" }, ... ]
	 */

	const MWCitationContextItem = require( './ve.ui.MWCitationContextItem.js' );
	const MWCitationDialogTool = require( './ve.ui.MWCitationDialogTool.js' );

	// TODO: Replace with require( './ve.ui.MWCitationTools.json' );
	ve.ui.mwCitationTools.forEach( ( item ) => {
		// Generate citation tool
		const name = 'cite-' + item.name;
		if ( !ve.ui.toolFactory.lookup( name ) ) {
			const tool = function GeneratedMWCitationDialogTool() {
				MWCitationDialogTool.apply( this, arguments );
			};
			OO.inheritClass( tool, MWCitationDialogTool );
			tool.static.group = 'cite';
			tool.static.name = name;
			tool.static.icon = item.icon;
			if ( mw.config.get( 'wgCiteVisualEditorOtherGroup' ) ) {
				tool.static.title = mw.msg( 'cite-ve-othergroup-item', item.title );
			} else {
				tool.static.title = item.title;
			}
			tool.static.commandName = name;
			tool.static.template = item.template;
			tool.static.autoAddToCatchall = false;
			tool.static.autoAddToGroup = true;
			tool.static.associatedWindows = [ name ];
			ve.ui.toolFactory.register( tool );
			ve.ui.commandRegistry.register(
				new ve.ui.Command(
					name, 'mwcite', 'open', { args: [ item ], supportedSelections: [ 'linear' ] }
				)
			);
		}

		// Generate citation context item
		if ( !ve.ui.contextItemFactory.lookup( name ) ) {
			const contextItem = function GeneratedMWCitationContextItem() {
				// Parent constructor
				MWCitationContextItem.apply( this, arguments );
			};
			OO.inheritClass( contextItem, MWCitationContextItem );
			contextItem.static.name = name;
			contextItem.static.icon = item.icon;
			contextItem.static.label = item.title;
			contextItem.static.commandName = name;
			contextItem.static.template = item.template;
			// If the grand-parent class (ve.ui.MWReferenceContextItem) is extended
			// and re-registered (e.g. by Citoid), then the inheritance chain is
			// broken, and the generic 'reference' context item would show. Instead
			// manually specify that that context should never show when a more
			// specific context item is shown.
			contextItem.static.suppresses = [ 'reference' ];
			ve.ui.contextItemFactory.register( contextItem );
		}
	} );
}() );
