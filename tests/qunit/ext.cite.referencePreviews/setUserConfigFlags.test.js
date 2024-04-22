( mw.loader.getModuleNames().indexOf( 'ext.popups.main' ) !== -1 ?
	QUnit.module :
	QUnit.module.skip )( 'ext.cite.referencePreviews#setUserConfigFlags' );

QUnit.test( 'reference preview config settings are successfully set from bitmask', ( assert ) => {
	const config = new Map();

	config.set( 'wgPopupsFlags', '7' );
	require( 'ext.cite.referencePreviews' ).private.setUserConfigFlags( config );

	assert.deepEqual(
		[
			config.get( 'wgCiteReferencePreviewsConflictsWithRefTooltipsGadget' ),
			config.get( 'wgCiteReferencePreviews' )
		],
		[ true, true ]
	);

	config.set( 'wgPopupsFlags', '2' );
	require( 'ext.cite.referencePreviews' ).private.setUserConfigFlags( config );

	assert.deepEqual(
		[
			config.get( 'wgCiteReferencePreviewsConflictsWithRefTooltipsGadget' ),
			config.get( 'wgCiteReferencePreviews' )
		],
		[ true, false ]
	);

	config.set( 'wgPopupsFlags', '5' );
	require( 'ext.cite.referencePreviews' ).private.setUserConfigFlags( config );

	assert.deepEqual(
		[
			config.get( 'wgCiteReferencePreviewsConflictsWithRefTooltipsGadget' ),
			config.get( 'wgCiteReferencePreviews' )
		],
		[ false, true ]
	);

	config.set( 'wgPopupsFlags', '0' );
	require( 'ext.cite.referencePreviews' ).private.setUserConfigFlags( config );

	assert.deepEqual(
		[
			config.get( 'wgCiteReferencePreviewsConflictsWithRefTooltipsGadget' ),
			config.get( 'wgCiteReferencePreviews' )
		],
		[ false, false ]
	);
} );
