( function($) {
	$( function() {
		$('.biblio-cite-link,sup.reference a').tooltip({
				bodyHandler: function() {
					return $( '#' + this.hash.substr(1) )
						.html();
				},
				showURL : false
			} );
	} );
	
} )( jQuery );
