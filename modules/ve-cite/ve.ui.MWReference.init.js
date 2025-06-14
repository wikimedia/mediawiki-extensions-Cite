'use strict';

/*!
 * VisualEditor MediaWiki Cite initialisation code.
 *
 * @copyright 2011-2018 VisualEditor Team's Cite sub-team and others; see AUTHORS.txt
 * @license MIT
 */

( function () {
	const modifiedToolbarGroups = [];

	mw.hook( 've.newTarget' ).add( ( target ) => {

		if ( ![ 'article', 'cx' ].includes( target.constructor.static.name ) ) {
			return;
		}
		const toolbarGroups = target.constructor.static.toolbarGroups;

		if ( modifiedToolbarGroups.includes( toolbarGroups ) ) {
			return;
		}

		if (
			mw.config.get( 'wgCiteVisualEditorOtherGroup' ) ||
			// Mobile doesn't have room for a top-level reference group, so place in the 'insert' menu as well
			( ve.init.mw.MobileArticleTarget && target instanceof ve.init.mw.MobileArticleTarget )
		) {
			// Add to the insert group (demoted)
			const insertGroup = toolbarGroups.find(
				( toolbarGroup ) => toolbarGroup.name === 'insert' ||
				// Name used in CX.
				// TODO: Change this to 'insert'
				toolbarGroup.name === 'extra'
			);
			if ( insertGroup ) {
				insertGroup.demote = [
					...( insertGroup.demote || [] ),
					{ group: 'cite' }, 'reference', 'reference/existing'
				];
			}
		} else {
			// Add after the link group
			const index = toolbarGroups.findIndex( ( toolbarGroup ) => toolbarGroup.name === 'link' );
			if ( index !== -1 ) {
				const group = {
					name: 'cite',
					type: 'list',
					title: OO.ui.deferMsg( 'cite-ve-toolbar-group-label' ),
					label: OO.ui.deferMsg( 'cite-ve-toolbar-group-label' ),
					include: [ { group: 'cite' }, 'reference', 'reference/existing' ],
					demote: [ 'reference', 'reference/existing' ]
				};
				toolbarGroups.splice( index + 1, 0, group );
			} else {
				mw.log.warn( 'No link group find in toolbar to place reference tools next to.' );
			}
		}

		modifiedToolbarGroups.push( toolbarGroups );
	} );

	/**
	 * Add reference insertion tools from on-wiki data.
	 *
	 * By adding a definition in JSON to MediaWiki:Cite-tool-definition, the cite menu can
	 * be populated with tools that create refrences containing a specific templates. The
	 * content of the definition should be an array containing a series of
	 * objects, one for each tool. Each object must contain a `name`, `icon` and
	 * `template` property. An optional `title` property can also be used to
	 * define the tool title in plain text. The `name` property is a unique
	 * identifier for the tool, and also provides a fallback title for the tool by
	 * being transformed into a message key. The name is prefixed with
	 * `visualeditor-cite-tool-name-`, and messages can be defined on Wiki. Some
	 * common messages are pre-defined for tool names such as `web`, `book`,
	 * `news` and `journal`.
	 *
	 * Example:
	 * [ { "name": "web", "icon": "browser", "template": "Cite web" }, ... ]
	 *
	 */
	( function () {
		const deprecatedIcons = {
				'ref-cite-book': 'book',
				'ref-cite-journal': 'journal',
				'ref-cite-news': 'newspaper',
				'ref-cite-web': 'browser',
				'reference-existing': 'referenceExisting'
			},
			defaultIcons = {
				book: 'book',
				journal: 'journal',
				news: 'newspaper',
				web: 'browser'
			};

		// This is assigned server-side by CitationToolDefinition.php, before this file runs.
		// Ensure it has a fallback, just in case.
		ve.ui.mwCitationTools = ve.ui.mwCitationTools || [];

		ve.ui.mwCitationTools.forEach( ( item ) => {
			const hasOwn = Object.prototype.hasOwnProperty;
			const data = { template: item.template, title: item.title };

			if ( !item.icon && hasOwn.call( defaultIcons, item.name ) ) {
				item.icon = defaultIcons[ item.name ];
			}

			if ( hasOwn.call( deprecatedIcons, item.icon ) ) {
				item.icon = deprecatedIcons[ item.icon ];
			}

			// Generate citation tool
			const name = 'cite-' + item.name;
			if ( !ve.ui.toolFactory.lookup( name ) ) {
				const tool = function GeneratedMWCitationDialogTool() {
					ve.ui.MWCitationDialogTool.apply( this, arguments );
				};
				OO.inheritClass( tool, ve.ui.MWCitationDialogTool );
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
						name, 'mwcite', 'open', { args: [ data ], supportedSelections: [ 'linear' ] }
					)
				);
			}

			// Generate citation context item
			if ( !ve.ui.contextItemFactory.lookup( name ) ) {
				const contextItem = function GeneratedMWCitationContextItem() {
					// Parent constructor
					ve.ui.MWCitationContextItem.apply( this, arguments );
				};
				OO.inheritClass( contextItem, ve.ui.MWCitationContextItem );
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

}() );
