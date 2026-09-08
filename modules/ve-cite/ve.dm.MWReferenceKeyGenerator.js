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
	getAutonamePrefix: function ( internalItem ) {
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
		const name = this.extractNameFromListKey( listKey );
		if ( name ) {
			return name;
		}
		if ( !isReused && attributes.mainListIndex === undefined ) {
			return;
		}

		const namePrefix = betterAutonames && this.getAutonamePrefix(
			internalList.getItemNode( attributes.listIndex )
		) || ':';

		return internalList.getNodeGroup( attributes.listGroup ).getUniqueListKey(
			listKey,
			'literal/' + namePrefix
		).slice( 'literal/'.length );
	}
};

module.exports = ve.dm.MWReferenceKeyGenerator;
