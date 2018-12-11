/**
 * @author Thiemo Kreuz
 */
( function () {
	'use strict';

	/**
	 * @param {jQuery} $backlinkWrapper
	 * @return {jQuery}
	 */
	function makeUpArrowLink( $backlinkWrapper ) {
		var upArrow,
			textNode = $backlinkWrapper[ 0 ].firstChild,
			accessibilityLabel = mw.msg( 'cite_references_link_accessibility_back_label' ),
			$upArrowLink = $( '<a>' )
				.addClass( 'mw-cite-up-arrow-backlink' )
				.attr( 'aria-label', accessibilityLabel )
				.attr( 'title', accessibilityLabel );

		if ( !textNode ) {
			return $upArrowLink;
		}

		// Skip additional, custom HTML wrappers, if any.
		while ( textNode.firstChild ) {
			textNode = textNode.firstChild;
		}

		if ( textNode.nodeType !== Node.TEXT_NODE || textNode.data.trim() === '' ) {
			return $upArrowLink;
		}

		upArrow = textNode.data.trim();
		// The text node typically contains "↑ ", and we need to keep the space.
		textNode.data = textNode.data.replace( upArrow, '' );

		// Create a plain text and a clickable "↑". CSS :target selectors make sure only
		// one is visible at a time.
		$backlinkWrapper.prepend(
			$( '<span>' )
				.addClass( 'mw-cite-up-arrow' )
				.text( upArrow ),
			$upArrowLink
				.text( upArrow )
		);

		return $upArrowLink;
	}

	/**
	 * @param {jQuery} $backlink
	 */
	function updateUpArrowLink( $backlink ) {
		// It's convenient to stop at the class name, but it's not guaranteed to be there.
		var $backlinkWrapper = $backlink.closest( '.mw-cite-backlink, li' ),
			$upArrowLink = $backlinkWrapper.find( '.mw-cite-up-arrow-backlink' );

		if ( !$upArrowLink.length && $backlinkWrapper.length ) {
			$upArrowLink = makeUpArrowLink( $backlinkWrapper );
		}

		$upArrowLink.attr( 'href', $backlink.attr( 'href' ) );
	}

	mw.hook( 'wikipage.content' ).add( function ( $content ) {
		// We are going to use the ID in the code below, so better be sure one is there.
		$content.find( '.reference[id] > a' ).click( function () {
			var $backlink,
				id = $( this ).parent().attr( 'id' ),
				className = 'mw-cite-targeted-backlink';

			$content.find( '.' + className ).removeClass( className );

			// Bail out if there is not at least a second backlink ("cite_references_link_many").
			if ( id.slice( -2 ) === '-0' &&
				!$content.find( '.references a[href="#' + id.slice( 0, -1 ) + '1"]' ).length
			) {
				return;
			}

			// The :not() skips the duplicate link created below. Relevant when double clicking.
			$backlink = $content.find( '.references a[href="#' + id + '"]:not(.mw-cite-up-arrow-backlink)' )
				.first()
				.addClass( className );

			if ( $backlink.length ) {
				updateUpArrowLink( $backlink );
			}
		} );
	} );
}() );
