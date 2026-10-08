'use strict';

const { citeAutonameTemplateMap } = require( './ve.ui.citeAutonameTemplates.json' );
const { referenceAutonamePrefix } = require( './ve.ui.referenceNameMessages.json' );

/**
 * Helper class to manage name and listKey generation.
 *
 * @class
 */
ve.dm.MWReferenceKeyGenerator = {

	/**
	 * @param {ve.dm.InternalList} internalList
	 * @param {string|null} [name] The reference's plain name without any prefix, if known
	 * @return {string}
	 */
	makeListKey: function ( internalList, name ) {
		return name ?
			'literal/' + name :
			'auto/' + internalList.getNextUniqueNumber();
	},

	/**
	 * @param {ve.dm.InternalList} internalList
	 * @param {string} listGroup Group to check for duplicates
	 * @param {string} listKey Possibly conflicting addition to the group
	 * @return {string} Original listKey if there was no conflict, an auto-generated one otherwise
	 */
	deduplicateListKey: function ( internalList, listGroup, listKey ) {
		const group = internalList.getNodeGroup( listGroup );
		// Note: This is currently the cheapest method to check if the listKey is known
		if ( group && group.getAllReuses( listKey ) ) {
			return this.makeListKey( internalList );
		}
		return listKey;
	},

	/**
	 * @param {string} listKey
	 * @return {boolean}
	 */
	isLiteralListKey: function ( listKey ) {
		return !!this.extractNameFromListKey( listKey );
	},

	/**
	 * Inverse function of {@link #makeListKey}. Returns an empty string for unnamed references.
	 *
	 * @param {string|undefined} listKey
	 * @return {string}
	 */
	extractNameFromListKey: function ( listKey ) {
		return listKey && listKey.startsWith( 'literal/' ) ? listKey.slice( 8 ) : '';
	},

	/**
	 * A wrapper around the imported variable (generated in PHP, loaded with ResourceLoader)
	 * to make this easier to test.
	 *
	 * @return {string|null}
	 */
	getReferenceAutonamePrefix() {
		return referenceAutonamePrefix;
	},

	/**
	 * Map a Cite template to its autoname template, using a fallback if available
	 *
	 * @param {string|undefined} citeTemplate
	 * @param {Object} templateMap Cite template to "Autoname for Cite template" map. Only pass a parameter for testing
	 * @return {string|undefined}
	 */
	getCitationAutonameTemplate: function ( citeTemplate, templateMap = citeAutonameTemplateMap ) {
		if ( citeTemplate in templateMap ) {
			return templateMap[ citeTemplate ];
		} else if ( '*' in templateMap ) {
			return templateMap[ '*' ];
		}
	},

	/**
	 * Get an autoname prefix for a listKey from the documents permanent store.
	 *
	 * @param {ve.dm.Document} doc
	 * @param {string} listIndex
	 * @return {string|undefined}
	 */
	getStoredAutonamePrefix: function ( doc, listIndex ) {
		const storageKey = 'autonamePrefix-' + listIndex;
		return this.normalizeName( doc.getStorage( storageKey ) );
	},

	/**
	 * Set an autoname prefix for a listKey from the documents permanent store.
	 *
	 * @param {string|undefined} prefix
	 * @param {ve.dm.Document} doc
	 * @param {string} listIndex
	 */
	setStoredAutonamePrefix: function ( prefix, doc, listIndex ) {
		const storageKey = 'autonamePrefix-' + listIndex;
		doc.setStorage( storageKey, prefix );
	},

	/**
	 * Get an autoname prefix by applying an autoname template on a references template
	 * content.  Only works on references with a single template transclusion.
	 *
	 * @async
	 * @param {ve.dm.MWTransclusionNode} transclusionNode
	 * @param {ve.dm.Document} doc
	 * @param {string|undefined} autonameTemplate
	 * @return {string|undefined}
	 */
	getAutonamePrefixFromTemplate: async function ( transclusionNode, doc, autonameTemplate ) {
		if ( !autonameTemplate ) {
			return;
		}

		// Clone the transclusion node to avoid manipulating the actual one
		const clonedNode = ve.dm.nodeFactory.createFromElement(
			ve.copy( transclusionNode.getElement() )
		);
		clonedNode.setDocument( doc );

		// Get raw the transclusion data from 'mw'
		const transclusionMW = clonedNode.getAttribute( 'mw' );
		const transclusionPart = transclusionMW && transclusionMW.parts && transclusionMW.parts[ 0 ];
		const params = ve.getProp( transclusionPart, 'template', 'params' );

		if ( !params ) {
			return;
		}

		// Overwrite the transclusion data using a different template name
		transclusionMW.parts[ 0 ] = {
			template: {
				target: {
					wt: autonameTemplate,
					href: './Template:' + autonameTemplate
				},
				// Keep the original params and values
				params: params
			}
		};

		// Write back to the node
		clonedNode.element.attributes.mw = transclusionMW;

		const response = await ve.init.target.parseWikitextFragment(
			clonedNode.getWikitext(),
			true,
			doc
		);

		if ( ve.getProp( response, 'visualeditor', 'result' ) !== 'success' ) {
			return;
		}

		return this.normalizeName( $( response.visualeditor.content ).text() );
	},

	/**
	 * @param {ve.dm.InternalItemNode} internalItem
	 * @return {string|undefined} The citation type's autoname,
	 * or undefined if it isn't a recognized template transclusion
	 */
	getCitationAutonamePrefix: function ( internalItem ) {
		const matchingToolDefinition = ve.ui.MWCitationDialog.static.getToolDefinitionFromInternalItem( internalItem );
		// Use the "-autoname" value from PHP if available
		return matchingToolDefinition && (
			this.normalizeName( matchingToolDefinition.autoname )
		);
	},

	/**
	 * Normalize an auto-generated reference name.
	 *
	 * @param {string|null|undefined} rawName The raw name to normalize
	 * @return {string} The normalized name
	 */
	normalizeName: function ( rawName ) {
		let name = rawName || '';

		// Remove disallowed characters: < > " ' / \ =
		name = name.replace( /[<>"'/\\=]+/g, '' );

		// Normalize multiple whitespaces to only one simple space
		name = name.replace( /\s+/g, ' ' );

		// Remove leading and trailing whitespace
		name = name.trim();

		return name;
	},

	/**
	 * @param {ve.dm.InternalItemNode} internalItem
	 * @return {string|undefined} The autoname prefix if a valid one was found, or undefined otherwise
	 */
	getAutonamePrefixFromInternalItem: function ( internalItem ) {
		// try to build citation type autoname prefix
		const citationAutonamePrefix = this.getCitationAutonamePrefix( internalItem );

		if ( citationAutonamePrefix ) {
			return citationAutonamePrefix;
		}

		return this.normalizeName( this.getReferenceAutonamePrefix() );
	},

	/**
	 * Generate the name for a given reference
	 *
	 * @param {Object} attributes
	 * @param {ve.dm.InternalList} internalList
	 * @param {boolean} [isReused=false]
	 * @param {boolean} [betterAutonames=false] // feature flag if better autonames should be used
	 * @return {string|undefined} literal or auto generated name
	 */
	generateName: function ( attributes, internalList, isReused, betterAutonames ) {
		const listKey = attributes.mainListKey || attributes.listKey;
		const listIndex = attributes.mainListIndex || attributes.listIndex;
		const name = this.extractNameFromListKey( listKey );
		if ( name ) {
			return name;
		}
		if ( !isReused && attributes.mainListIndex === undefined ) {
			return;
		}

		const namePrefix = betterAutonames && (
			this.getStoredAutonamePrefix( internalList.getDocument(), listIndex ) ||
				this.getAutonamePrefixFromInternalItem( internalList.getItemNode( listIndex ) )
		) ||
			':';

		return internalList.getNodeGroup( attributes.listGroup ).getUniqueListKey(
			listKey,
			'literal/' + namePrefix
		).slice( 'literal/'.length );
	}
};

module.exports = ve.dm.MWReferenceKeyGenerator;
