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

module.exports = ve.dm.MWDataTransitionHelper;
