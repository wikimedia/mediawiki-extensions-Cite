'use strict';

/*!
 * @copyright 2026 VisualEditor Team's Cite sub-team and others; see AUTHORS.txt
 * @license MIT
 */

/**
 * A facade providing a safe interface to wrap methods using a reference's listIndex
 * each with a fallback using listKey and listGroup.
 *
 * @constructor
 * @param {ve.dm.InternalList} internalList
 */
ve.dm.MWDataTransitionHelper = function VeDmMWDataTransitionHelper( internalList ) {
	// Properties
	/**
	 * @private
	 * @property {ve.dm.InternalList} internalList
	 */
	this.internalList = internalList;
};

/**
 * @param {string} listKey
 * @param {string} listGroup
 * @param {number} [listIndex]
 * @return {ve.dm.InternalItemNode|undefined}
 */
ve.dm.MWDataTransitionHelper.prototype.getInternalItemNode = function ( listKey, listGroup, listIndex ) {
	let listIndexInternalItem;
	if ( listIndex !== undefined ) {
		listIndexInternalItem = this.internalList.getItemNode( listIndex );
	}

	const nodeGroup = this.internalList.getNodeGroup( listGroup );
	const firstNode = nodeGroup && nodeGroup.getFirstNode( listKey );
	const listKeyInternalItem = firstNode && firstNode.getInternalItem();

	// make sure this works in cases where the deprecated mechanism fails completely
	// but in all other cases fall back to the listKey
	if ( listKeyInternalItem && !ve.compare( listIndexInternalItem, listKeyInternalItem ) ) {
		mw.log.warn(
			'MWDataTransitionHelper.getInternalItemNode: Different result from listIndex.'
		);
		return listKeyInternalItem;
	}
	return listIndexInternalItem;
};

module.exports = ve.dm.MWDataTransitionHelper;
