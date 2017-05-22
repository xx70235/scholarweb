jQuery(document).ready(function($) {

	function runChosen() {
		var found = $("#widgets-right select[name^='widget-agencystrap-icon-text-widget']");
		found.each( function( index,value ) {
			$(value).chosen({
				width: "100%",
			}).change( function() {
				$selectedIcon = $(this).closest('.widget-content');
				$selectedIcon.find('.agencystrap-icon-placeholder').removeClass().addClass('agencystrap-icon-placeholder fa '+$(this).val());
			});
		});
	}
	runChosen();

	function current_icon() {
		$( ".chosen-container-single .chosen-single span" ).each(function(){

			var current_icon = $( this ).html();
			//console.log( current_icon );

			$(this).parent().find( '.agencystrap-icon-placeholder' ).removeClass().addClass('agencystrap-icon-placeholder fa fa-'+current_icon);

			$( this ).on('change',function(){
				alert("changed");
			});
		});
	}
	current_icon();

	$(document).on('widget-updated widget-added', function() {
		runChosen();
		current_icon();
	});
});
