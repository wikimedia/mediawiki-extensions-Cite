( function($) {
	$( function() {
		$j('.biblio-cite-link,sup.reference a').tooltip({
				bodyHandler: function() {
					return $j( document.getElementById( this.hash.substr(1) ) )
						.html();
				},
				showURL : false
			} );
	} );
	
} )(jQuery);
