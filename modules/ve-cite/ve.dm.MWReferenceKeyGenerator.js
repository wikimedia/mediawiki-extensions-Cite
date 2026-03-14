'use strict';

/**
 * Helper class to manage name and listKey generation.
 *
 * @class
 */
ve.dm.MWReferenceKeyGenerator = {

	/**
	 * Regular expression for parsing the listKey attribute
	 *
	 * Use [\s\S]* instead of .* to catch esoteric whitespace (T263698)
	 *
	 * @property {RegExp}
	 */
	listKeyRegex: /^(auto|literal)\/([\s\S]*)$/,

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
	 * Generate the name for a given reference
	 *
	 * @param {Object} attributes
	 * @param {ve.dm.InternalList} internalList
	 * @param {boolean} [isReused=false]
	 * @return {string|undefined} literal or auto generated name
	 */
	generateName: function ( attributes, internalList, isReused ) {
		const listKey = attributes.mainRefKey || attributes.listKey;
		const keyParts = this.listKeyRegex.exec( listKey );

		// use literal name
		if ( keyParts && keyParts[ 1 ] === 'literal' ) {
			return keyParts[ 2 ];
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
