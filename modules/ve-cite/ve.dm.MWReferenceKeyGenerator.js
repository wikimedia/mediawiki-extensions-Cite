'use strict';

/**
 * Helper class to manage name and listKey generation.
 *
 * @class
 */
ve.dm.MWReferenceKeyGenerator = {

	/**
	 * @param {ve.dm.InternalList} internalList
	 * @param {string|null} [name]
	 * @return {string}
	 */
	makeListKey: function ( internalList, name ) {
		return name ?
			'literal/' + name :
			'auto/' + internalList.getNextUniqueNumber();
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
	 * Generate the name for a given reference
	 *
	 * @param {Object} attributes
	 * @param {ve.dm.InternalList} internalList
	 * @param {boolean} [isReused=false]
	 * @return {string|undefined} literal or auto generated name
	 */
	generateName: function ( attributes, internalList, isReused ) {
		const listKey = attributes.mainRefKey || attributes.listKey;
		const name = this.extractNameFromListKey( listKey );

		// use literal name
		if ( name ) {
			return name;
		}

		// Auto-generate a new name when it's a sub-ref (i.e. it's linked to a main ref's listIndex)
		// or it's a reused main ref that's either reused as is or has sub-refs
		if ( attributes.mainListIndex !== undefined || isReused ) {
			return internalList.getNodeGroup( attributes.listGroup ).getUniqueListKey(
				listKey,
				'literal/:'
			).slice( 'literal/'.length );
		}
	}

};

module.exports = ve.dm.MWReferenceKeyGenerator;
