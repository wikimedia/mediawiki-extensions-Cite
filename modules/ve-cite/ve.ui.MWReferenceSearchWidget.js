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
	this.docRefs = null;
	this.index = null;
	this.wasUsedActively = false;

	// Initialization
	this.$element.addClass( 've-ui-mwReferenceSearchWidget' );
	this.$results.on( 'scroll', this.trackActiveUsage.bind( this ) );
};

/* Inheritance */

OO.inheritClass( ve.ui.MWReferenceSearchWidget, OO.ui.SearchWidget );

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
 * @param {ve.dm.MWDocumentReferences} docRefs handle to all refs in the original document
 */
ve.ui.MWReferenceSearchWidget.prototype.setDocumentRefs = function ( docRefs ) {
	this.results.unselectItem();

	this.docRefs = docRefs;
};

/**
 * Set the internal list and check if it contains any references
 *
 * @deprecated use #setDocumentRefs instead.
 * @param {ve.dm.InternalList} internalList
 */
ve.ui.MWReferenceSearchWidget.prototype.setInternalList = function ( internalList ) {
	this.setDocumentRefs( ve.dm.MWDocumentReferences.static.refsForDoc( internalList.getDocument() ) );
};

/**
 * Manually re-build the index and re-populate the list of search results.
 */
ve.ui.MWReferenceSearchWidget.prototype.buildIndex = function () {
	this.index = null;
	this.onQueryChange();
};

/**
 * @private
 * @return {Object[]}
 */
ve.ui.MWReferenceSearchWidget.prototype.buildSearchIndex = function () {
	const groupNames = this.docRefs.getAllGroupNames().sort();

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
		const groupRefs = this.docRefs.getGroupRefs( groupName );
		const flatNodes = groupRefs.getAllRefsInDocumentOrder()
			.filter( ( node ) => !filterExtends || !node.getAttribute( 'extendsRef' ) );

		index = index.concat( flatNodes.map( ( node ) => {
			const listKey = node.getAttribute( 'listKey' );
			// remove `mwReference/` prefix
			const group = groupName.slice( 12 );
			const footnoteNumber = this.docRefs.getIndexLabel( group, listKey );
			const footnoteLabel = ( group ? group + ' ' : '' ) + footnoteNumber;

			// Use [\s\S]* instead of .* to catch esoteric whitespace (T263698)
			const matches = listKey.match( /^literal\/([\s\S]*)$/ );
			const name = matches && matches[ 1 ] || '';

			let $refContent;
			// Make visible text, footnoteLabel and reference name searchable
			let refText = ( footnoteLabel + ' ' + name ).toLowerCase();
			const itemNode = groupRefs.getInternalModelNode( listKey );
			if ( itemNode.length ) {
				$refContent = new ve.ui.MWPreviewElement( itemNode, { useView: true } ).$element;
				refText = $refContent.text().toLowerCase() + ' ' + refText;
				// Make URLs searchable
				$refContent.find( 'a[href]' ).each( ( k, element ) => {
					refText += ' ' + element.getAttribute( 'href' );
				} );
			} else {
				$refContent = $( '<span>' )
					.addClass( 've-ce-mwReferencesListNode-muted' )
					.text( ve.msg( 'cite-ve-referenceslist-missingref-in-list' ) );
			}

			return {
				$refContent: $refContent,
				searchableText: refText,
				// TODO: return a simple node
				reference: ve.dm.MWReferenceModel.static.newFromReferenceNode( node ),
				footnoteLabel: footnoteLabel,
				name: name
			};
		} ) );
	}

	return index;
};

/**
 * Check whether buildIndex will create an empty index based on the current document
 *
 * @deprecated Move logic to caller.
 * @return {boolean} Index is empty
 */
ve.ui.MWReferenceSearchWidget.prototype.isIndexEmpty = function () {
	return !this.docRefs.hasRefs();
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
		if ( item.searchableText.indexOf( query ) >= 0 ) {
			const $footnoteLabel = $( '<div>' )
				.addClass( 've-ui-mwReferenceSearchWidget-footnote' )
				.text( '[' + item.footnoteLabel + ']' );
			if ( item.reference.extendsRef !== undefined ) {
				$footnoteLabel.addClass( 've-ui-mwReferenceSearchWidget-footnote-sub' );
			}
			const $name = $( '<div>' )
				.addClass( 've-ui-mwReferenceSearchWidget-name' )
				.toggleClass( 've-ui-mwReferenceSearchWidget-name-autogenerated', /^:\d+$/.test( item.name ) )
				.text( item.name );
			items.push(
				new ve.ui.MWReferenceResultWidget( {
					data: item.reference,
					label: $footnoteLabel.add( $name ).add( item.$refContent )
				} )
			);
		}
	}

	return items;
};
