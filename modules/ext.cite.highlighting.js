/**
 * @author Thiemo Kreuz
 */
( function () {
	'use strict';

	mw.hook( 'wikipage.content' ).add( function ( $content ) {
		// We are going to use the ID in the code below, so better be sure one is there.
		$content.find( '.reference[id] > a' ).click( function () {
			var $backlink, $backlinkWrapper, $upArrowLink, textNode, upArrow,
				id = $( this ).parent().attr( 'id' ),
				className = 'mw-cite-targeted-backlink',
				accessibilityLabel;

			$content.find( '.' + className ).removeClass( className );
			// The additional "*" avoids the "↑" (when there is only one backlink) becoming bold.
			$backlink = $content.find( '.mw-cite-backlink * a[href="#' + id + '"]' )
				.addClass( className );

			if ( !$backlink.length ) {
				return;
			}

			$backlinkWrapper = $backlink.closest( '.mw-cite-backlink' );
			$upArrowLink = $backlinkWrapper.find( '.mw-cite-up-arrow-backlink' );

			if ( !$upArrowLink.length ) {
				textNode = $backlinkWrapper[ 0 ].firstChild;

				if ( textNode &&
					textNode.nodeType === Node.TEXT_NODE &&
					textNode.data.trim() !== ''
				) {
					accessibilityLabel = mw.msg( 'cite_references_link_accessibility_back_label' );
					upArrow = textNode.data.trim();
					// The text node typically contains "↑ ", and we need to keep the space.
					textNode.data = textNode.data.replace( upArrow, '' );

					// Create a plain text and a clickable "↑". CSS :target selectors make sure only
					// one is visible at a time.
					$upArrowLink = $( '<a>' )
						.addClass( 'mw-cite-up-arrow-backlink' )
						.attr( 'aria-label', accessibilityLabel )
						.attr( 'title', accessibilityLabel )
						.text( upArrow );
					$backlinkWrapper.prepend(
						$( '<span>' )
							.addClass( 'mw-cite-up-arrow' )
							.text( upArrow ),
						$upArrowLink
					);
				}
			}

			$upArrowLink.attr( 'href', $backlink.attr( 'href' ) );
		} );
	} );
}() );
