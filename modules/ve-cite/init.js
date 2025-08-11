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

// TODO: Remove after Citoid and ContentTranslation are updated to not use this any more
ve.ui.mwCitationTools = require( './ve.ui.MWCitationTools.json' );
// TODO: We could merge the two init files. Is this worth it?
require( './ve.ui.MWReference.init.js' );

const data = require( './ve.ui.contentLanguage.json' );
for ( const languageCode in data ) {
	mw.language.setData( languageCode, data[ languageCode ] );
}
