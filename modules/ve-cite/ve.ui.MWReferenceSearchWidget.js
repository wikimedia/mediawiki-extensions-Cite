'use strict';

/*!
 * VisualEditor UserInterface MWReferenceSearchWidget class.
 *
 * @copyright 2011-2018 VisualEditor Team's Cite sub-team and others; see AUTHORS.txt
 * @license MIT
 */

/**
 * Creates an ve.ui.MWReferenceSearchWidget object.
 *
 * @constructor
 * @extends OO.ui.SearchWidget
 * @param {Object} [config] Configuration options
 * @property {Object[]|null} index Null when the index needs to be rebuild
 */
ve.ui.MWReferenceSearchWidget = function VeUiMWReferenceSearchWidget( config ) {
	// Configuration initialization
	config = ve.extendObject( {
		placeholder: ve.msg( 'cite-ve-reference-input-placeholder' )
	}, config );

	// Parent constructor
	ve.ui.MWReferenceSearchWidget.super.call( this, config );

	// Properties
	this.index = null;
	this.wasUsedActively = false;

	// Initialization
	this.$element.addClass( 've-ui-mwReferenceSearchWidget' );
	this.$results.on( 'scroll', this.trackActiveUsage.bind( this ) );
};

/* Inheritance */

OO.inheritClass( ve.ui.MWReferenceSearchWidget, OO.ui.SearchWidget );

/* Static Methods */

/**
 * @param {ve.dm.InternalList} internalList
 * @return {boolean}
 */
ve.ui.MWReferenceSearchWidget.static.isIndexEmpty = function ( internalList ) {
	const groups = internalList.getNodeGroups();
	// Doing this live every time is cheap because it stops on the first non-empty group
	for ( const groupName in groups ) {
		if ( groupName.indexOf( 'mwReference/' ) === 0 && groups[ groupName ].indexOrder.length ) {
			// No need to filter subrefs here, as it's impossible to have subrefs without parents
			return false;
		}
	}
	return true;
};

/* Methods */

ve.ui.MWReferenceSearchWidget.prototype.onQueryChange = function () {
	// Parent method
	ve.ui.MWReferenceSearchWidget.super.prototype.onQueryChange.call( this );

	// Populate
	this.getResults().addItems( this.buildSearchResults( this.getQuery().getValue() ) );
};

/**
 * @param {jQuery.Event} e Key down event
 */
ve.ui.MWReferenceSearchWidget.prototype.onQueryKeydown = function ( e ) {
	// Parent method
	ve.ui.MWReferenceSearchWidget.super.prototype.onQueryKeydown.call( this, e );

	this.trackActiveUsage();
};

ve.ui.MWReferenceSearchWidget.prototype.clearSearch = function () {
	this.getQuery().setValue( '' );
	this.wasUsedActively = false;
};

/**
 * Track when the user looks for references to reuse using scrolling or filtering results
 */
ve.ui.MWReferenceSearchWidget.prototype.trackActiveUsage = function () {
	if ( this.wasUsedActively ) {
		return;
	}

	// https://phabricator.wikimedia.org/T362347
	ve.track( 'activity.reference', { action: 'reuse-dialog-use' } );
	this.wasUsedActively = true;
};

/**
 * Set the internal list and check if it contains any references
 *
 * @param {ve.dm.InternalList} internalList Internal list
 */
ve.ui.MWReferenceSearchWidget.prototype.setInternalList = function ( internalList ) {
	this.results.unselectItem();

	this.internalList = internalList;
	this.internalList.connect( this, { update: 'onInternalListUpdate' } );
	this.internalList.getListNode().connect( this, { update: 'onListNodeUpdate' } );
};

/**
 * Handle the updating of the InternalList object.
 *
 * This will occur after a document transaction.
 *
 * @param {string[]} groupsChanged A list of groups which have changed in this transaction
 */
ve.ui.MWReferenceSearchWidget.prototype.onInternalListUpdate = function ( groupsChanged ) {
	if ( groupsChanged.some( ( groupName ) => groupName.indexOf( 'mwReference/' ) === 0 ) ) {
		this.index = null;
	}
};

/**
 * Handle the updating of the InternalListNode.
 *
 * This will occur after changes to any InternalItemNode.
 */
ve.ui.MWReferenceSearchWidget.prototype.onListNodeUpdate = function () {
	this.index = null;
};

/**
 * Manually re-populates the list of search results after {@see setInternalList} was called.
 */
ve.ui.MWReferenceSearchWidget.prototype.buildIndex = function () {
	this.onQueryChange();
};

/**
 * @private
 * @return {Object[]}
 */
ve.ui.MWReferenceSearchWidget.prototype.buildSearchIndex = function () {
	const docRefs = ve.dm.MWDocumentReferences.static.refsForDoc( this.internalList.getDocument() );
	const groups = this.internalList.getNodeGroups();
	const groupNames = Object.keys( groups ).sort();

	// FIXME: Temporary hack, to be removed soon
	// eslint-disable-next-line no-jquery/no-class-state
	const filterExtends = this.$element.hasClass( 've-ui-citoidInspector-extends' );

	let index = [];
	for ( let i = 0; i < groupNames.length; i++ ) {
		const groupName = groupNames[ i ];
		if ( groupName.indexOf( 'mwReference/' ) !== 0 ) {
			// FIXME: Should be impossible to reach
			continue;
		}
		const groupedByParent = docRefs.getGroupRefsByParents( groupName );
		let flatNodes = [];
		if ( filterExtends ) {
			flatNodes = ( groupedByParent[ '' ] || [] );
		} else {
			// flatMap
			( groupedByParent[ '' ] || [] ).forEach( ( parentNode ) => {
				flatNodes.push( parentNode );
				flatNodes = flatNodes.concat( groupedByParent[ parentNode.getAttribute( 'listKey' ) ] || [] );
			} );
		}

		index = index.concat( flatNodes.map( ( node ) => {
			const listKey = node.getAttribute( 'listKey' );
			// remove `mwReference/` prefix
			const group = groupName.slice( 12 );
			const footnoteNumber = docRefs.getIndexNumber( group, listKey );
			const citation = ( group ? group + ' ' : '' ) + footnoteNumber;

			// Use [\s\S]* instead of .* to catch esoteric whitespace (T263698)
			const matches = listKey.match( /^literal\/([\s\S]*)$/ );
			const name = matches && matches[ 1 ] || '';

			let $element;
			// Make visible text, citation and reference name searchable
			let text = ( citation + ' ' + name ).toLowerCase();
			const itemNode = this.internalList.getItemNode( node.getAttribute( 'listIndex' ) );
			if ( itemNode.length ) {
				$element = new ve.ui.MWPreviewElement( itemNode, { useView: true } ).$element;
				text = $element.text().toLowerCase() + ' ' + text;
				// Make URLs searchable
				$element.find( 'a[href]' ).each( ( k, element ) => {
					text += ' ' + element.getAttribute( 'href' );
				} );
			} else {
				$element = $( '<span>' )
					.addClass( 've-ce-mwReferencesListNode-muted' )
					.text( ve.msg( 'cite-ve-referenceslist-missingref-in-list' ) );
			}

			return {
				$element: $element,
				text: text,
				// TODO: return a simple node
				reference: ve.dm.MWReferenceModel.static.newFromReferenceNode( node ),
				citation: citation,
				name: name
			};
		} ) );
	}

	return index;
};

/**
 * Check whether buildIndex will create an empty index based on the current internalList.
 *
 * @return {boolean} Index is empty
 */
ve.ui.MWReferenceSearchWidget.prototype.isIndexEmpty = function () {
	return !this.internalList ||
		ve.ui.MWReferenceSearchWidget.static.isIndexEmpty( this.internalList );
};

/**
 * @private
 * @param {string} query
 * @return {ve.ui.MWReferenceResultWidget[]}
 */
ve.ui.MWReferenceSearchWidget.prototype.buildSearchResults = function ( query ) {
	query = query.trim().toLowerCase();
	const items = [];

	if ( !this.index ) {
		this.index = this.buildSearchIndex();
	}

	for ( let i = 0; i < this.index.length; i++ ) {
		const item = this.index[ i ];
		if ( item.text.indexOf( query ) >= 0 ) {
			const $citation = $( '<div>' )
				.addClass( 've-ui-mwReferenceSearchWidget-citation' )
				.text( '[' + item.citation + ']' );
			const $name = $( '<div>' )
				.addClass( 've-ui-mwReferenceSearchWidget-name' )
				.toggleClass( 've-ui-mwReferenceSearchWidget-name-autogenerated', /^:\d+$/.test( item.name ) )
				.text( item.name );
			items.push(
				new ve.ui.MWReferenceResultWidget( {
					data: item.reference,
					label: $citation.add( $name ).add( item.$element )
				} )
			);
		}
	}

	return items;
};
