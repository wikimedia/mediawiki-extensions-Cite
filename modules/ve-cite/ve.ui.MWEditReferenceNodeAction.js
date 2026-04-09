'use strict';

/*!
 * VisualEditor UserInterface MediaWiki MWEditReferenceNodeAction class.
 *
 * @copyright 2016 VisualEditor Team's Cite sub-team and others; see AUTHORS.txt
 * @license MIT
 */

const MWReferenceModel = require( './ve.dm.MWReferenceModel.js' );
/**
 * Edit reference action. Executes the commands that open the {@link ve.ui.MWReferenceDialog}
 * or {@link ve.ui.MWCitationDialog} to edit a reference.
 *
 * @class
 * @extends ve.ui.Action
 * @constructor
 * @param {ve.ui.Surface} surface Surface to act on
 * @param {string} [source]
 */
ve.ui.MWEditReferenceNodeAction = function VeUiMWEditReferenceNodeAction() {
	// Parent constructor
	ve.ui.MWEditReferenceNodeAction.super.apply( this, arguments );
};

/* Inheritance */

OO.inheritClass( ve.ui.MWEditReferenceNodeAction, ve.ui.Action );

/* Static Properties */

ve.ui.MWEditReferenceNodeAction.static.name = 'editNode';

ve.ui.MWEditReferenceNodeAction.static.methods = [ 'execute' ];

/* Methods */

/**
 * Depending on the type of reference, compose the required arguments for the command and call it.
 *
 * @param {ve.dm.MWReferenceNode} node Node to edit
 */
ve.ui.MWEditReferenceNodeAction.prototype.execute = function ( node ) {
	const commandName = this.getCommandNameFromRef( node );
	if ( !commandName ) {
		return;
	}

	const refCommand = ve.ui.commandRegistry.lookup( commandName );
	const additionalWindowData = {
		refToEdit: MWReferenceModel.static.newFromReferenceNode( node )
	};

	if ( commandName === 'reference' ) {
		refCommand.execute(
			this.surface,
			// Arguments for calling ve.ui.MWReferenceDialog.getSetupProcess()
			[ 'reference', additionalWindowData ],
			this.source
		);
	} else {
		// FIXME: Allow passing refToEdit to the CitationDialog see T413760
		const fragmentArgs = {
			fragment: this.surface.getModel().getLinearFragment(
				node.getOuterRange(),
				true
			),
			selectFragmentOnClose: false
		};

		const newArgs = ve.copy( refCommand.args );
		ve.extendObject( newArgs[ 0 ], fragmentArgs );
		refCommand.execute( this.surface, newArgs, this.source );
	}
};

/**
 * @param {ve.dm.MWReferenceNode} node
 * @return {string}
 * @private
 */
ve.ui.MWEditReferenceNodeAction.prototype.getCommandNameFromRef = function ( node ) {
	const firstItem = ve.ui.contextItemFactory.getRelatedItems( [ node ] )
		.find( ( item ) => item.name !== 'mobileActions' );

	return firstItem && firstItem.name;
};

module.exports = ve.ui.MWEditReferenceNodeAction;
