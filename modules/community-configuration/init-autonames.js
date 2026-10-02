'use strict';

( function () {
	// Open links in the info section in a new tab
	mw.hook( 'wikipage.content' ).add( ( $content ) => {
		$content.find( '.communityconfiguration-info-section a' ).each( ( _, el ) => {
			el.target = '_blank';
		} );
	} );
}() );
