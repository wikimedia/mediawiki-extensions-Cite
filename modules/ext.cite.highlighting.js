/**
 * @author Thiemo Kreuz
 */
( function () {
	'use strict';

	mw.hook( 'wikipage.content' ).add( function ( $content ) {
		// We are going to use the ID in the code below, so better be sure one is there.
		$content.find( '.reference[id] > a' ).click( function () {
			var id = $( this ).parent().attr( 'id' ),
				className = 'mw-cite-targeted-backlink';

			$content.find( '.' + className ).removeClass( className );
			// The additional "*" avoids the "↑" (when there is only one backlink) becoming bold.
			$content.find( '.mw-cite-backlink * a[href="#' + id + '"]' ).addClass( className );
		} );
	} );
}() );
