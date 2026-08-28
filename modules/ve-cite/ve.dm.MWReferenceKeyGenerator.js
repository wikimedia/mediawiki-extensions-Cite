'use strict';

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
	 * @param {Object} attributes
	 * @param {ve.dm.InternalList} internalList
	 * @return {string|undefined} The prefix to use if it's a reference with citation template, otherwise undefined
	 */
	getCitationAutonamePrefix: function ( attributes, internalList ) {
		if ( !ve.ui.mwCitationTools || !ve.ui.mwCitationTools.length ) {
			return;
		}

		const internalItem = internalList.getItemNode( attributes.listIndex );
		const matchingToolDefinition = ve.ui.mwCitationTools.find( ( toolDefinition ) =>
			// eslint-disable-next-line implicit-arrow-linebreak
			ve.ui.MWCitationDialog.static.getTransclusionNodeWithTemplate(
				internalItem, toolDefinition.template )
		);
		// Use the "-autoname" value from PHP if available
		return matchingToolDefinition && ( matchingToolDefinition.autoname || matchingToolDefinition.title );
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

		let namePrefix = ':';
		if ( betterAutonames ) {
			const hasAutonameOverride = mw.message( 'cite-ve-dialogbutton-reference-title-autoname' ).exists();
			const autonameMsgText = ve.msg( hasAutonameOverride ?
				'cite-ve-dialogbutton-reference-title-autoname' :
				'cite-ve-dialogbutton-reference-title'
			);
			const defaultAutonamePrefix = hasAutonameOverride ? autonameMsgText : autonameMsgText + '-';
			const citationAutonamePrefix = this.getCitationAutonamePrefix( attributes, internalList );
			namePrefix = ( citationAutonamePrefix || defaultAutonamePrefix );
		}

		if ( attributes.mainListIndex !== undefined || isReused ) {
			return internalList.getNodeGroup( attributes.listGroup ).getUniqueListKey(
				listKey,
				'literal/' + namePrefix
			).slice( 'literal/'.length );
		}
	}

};

module.exports = ve.dm.MWReferenceKeyGenerator;
