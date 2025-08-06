/* Registration */

// TODO: Move all the register calls into this file!
require( './ve.dm.MWReferenceNode.js' );
require( './ve.dm.MWReferencesListNode.js' );
require( './ve.ce.MWReferenceNode.js' );
require( './ve.ce.MWReferencesListNode.js' );
require( './ve.ui.MWUseExistingReferenceCommand.js' );
require( './ve.ui.MWCitationDialog.js' );
require( './ve.ui.MWReferencesListCommand.js' );
require( './ve.ui.MWReferencesListDialog.js' );
require( './ve.ui.MWReferenceDialog.js' );
require( './ve.ui.MWReferenceDialogTool.js' );
require( './ve.ui.MWUseExistingReferenceDialogTool.js' );
require( './ve.ui.MWReferencesListDialogTool.js' );
require( './ve.ui.MWReferenceContextItem.js' );
require( './ve.ui.MWReferencesListContextItem.js' );
require( './ve.ui.MWCitationAction.js' );
require( './ve.ui.MWCitationNeededContextItem.js' );

/* Initialization */

// TODO: We could merge the two init files. Is this worth it?
require( './ve.ui.MWReference.init.js' );

// TODO: This script should return plain JSON, and the mw.language.setData() call moved here
require( './ve.ui.contentLanguage.js' );
