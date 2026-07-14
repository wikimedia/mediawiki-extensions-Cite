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
	 * @return {boolean} Whether the reference's content is a template transclusion
	 */
	getCitationTypeName: function ( attributes, internalList ) {
		const internalItem = internalList.getItemNode( attributes.listIndex );
		const matchingToolDefinition = ve.ui.mwCitationTools.find( ( toolDefinition ) =>
			// eslint-disable-next-line implicit-arrow-linebreak
			ve.ui.MWCitationDialog.static.getTransclusionNodeWithTemplate(
				internalItem, toolDefinition.template )
		);
		// Uses the tool's title, already resolved via PHP.
		// Potential FIXME: support an "-autoname" override.
		return matchingToolDefinition ? matchingToolDefinition.title : null;
	},

	/**
	 * Generate the name for a given reference
	 *
	 * @param {Object} attributes
	 * @param {ve.dm.InternalList} internalList
	 * @param {boolean} [isReused=false]
	 * @return {string|undefined} literal or auto generated name
	 */
	generateName: function ( attributes, internalList, isReused ) {
		const listKey = attributes.mainListKey || attributes.listKey;
		const name = this.extractNameFromListKey( listKey );
		let namePrefix = ':'; // old behavior
		if ( name ) {
			return name;
		}

		if ( mw.config.get( 'wgCiteCitationTypeAutoNames' ) ) {
			const citationTypeName = this.getCitationTypeName( attributes, internalList );
			namePrefix = citationTypeName || ve.msg( 'cite-ve-dialogbutton-reference-title' );
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
