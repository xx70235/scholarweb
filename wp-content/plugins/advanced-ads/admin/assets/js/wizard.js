jQuery( document ).ready(function ($) {
   // ad wizard
   advads_wizard.init();
});
var advads_wizard = {
    box_order: [ // selectors of elements in the wizard in the correct order
	'#post-body-content',
	'#ad-main-box',
	'#ad-parameters-box',
	'#ad-output-box',
	'#ad-display-box',
	'#ad-visitor-box',
	// '#advanced_ads_groupsdiv',
	// '#submitdiv'
    ],
    current_box: '#post-body-content', // current active box
    one_column: false, // whether the edit screen is in one-column mode
    init: function(){
	var _this = this;
	jQuery('#advads-wizard-controls-next').click( function( ){ _this.next(); } );
	jQuery('#advads-wizard-controls-prev').click( function( ){ _this.prev(); } );
	jQuery('#advads-wizard-controls-save').click( function( e ){ e.preventDefault(); jQuery('#publish').click(); } ); // save ad
	jQuery( '#advads-start-wizard' ).click( function(){
	    _this.start();
	});
	// end wizard when the button was clicked
	jQuery( '.advads-stop-wizard' ).click( function(){
	    _this.close();
	});
    },
    show_current_box: function(){
	jQuery( this.current_box ).removeClass('advads-hide');
    },
    start: function(){ // do stuff when wizard is started
	// show page in 1-column stype
	if( jQuery( '#post-body').hasClass('columns-1') ){
	    this.one_column = true;
	} else {
	    jQuery( '#post-body').addClass( 'columns-1' ).removeClass( 'columns-2' );
	}
	// hide all boxes, messages and the headline by adding a hide css class
	jQuery('#post-body-content, .postbox-container .postbox, h1 ~ div:not(.advads-admin-notice), h1').addClass( 'advads-hide' );
	// display first box
	this.show_current_box();
	// display close button and controls
	jQuery('#advads-stop-wizard, #advads-wizard-controls').removeClass('hidden')
	this.update_nav();
	this.callbacks();
	// initially hide some elemente
	jQuery( '#advads-ad-description').addClass('advads-hide'); // ad description
	jQuery( '#advads-ad-info').addClass('advads-hide'); // shortcode and php function info
	// hide all elements with 'advads-hide-for-wizard' class
	jQuery( '.advads-hide-in-wizard').hide();
	// remove close-class from ad type box
	jQuery( '#ad-main-box' ).removeClass('closed');
	this.save_hide_wizard( false );
	// jump to next box when ad type is selected
	jQuery('#advanced-ad-type input').change(function(){
	    _this.next();
	});
    },
    close: function(){ // close the wizard by showing all elements again
	jQuery('*').removeClass('advads-hide');
	jQuery('#advads-stop-wizard, #advads-wizard-controls').addClass('hidden');
	if( this.one_column !== true ){
	    jQuery( '#post-body').addClass( 'columns-2' ).removeClass( 'columns-1' );
	}
	jQuery('#advads-wizard-welcome').remove();// close wizard welcome message
	// show all elements with 'advads-hide-for-wizard' class
	jQuery( '.advads-hide-in-wizard').show();
	this.save_hide_wizard( true );
    },
    update_nav: function(){ // update navigation, display only valid buttons
	// display all buttons
	jQuery('#advads-wizard-controls button').removeClass('hidden');
	// hide next button if there is no next widget
	var i = this.box_order.indexOf( this.current_box );
	if( i === this.box_order.length - 1 ){
	    jQuery('#advads-wizard-controls-next').addClass('hidden');
	}
	if( i === 0 ){
	    jQuery('#advads-wizard-controls-prev').addClass('hidden');
	}
	// hide stop wizard button in bottom navi
	if( i !== 0 ){
	    jQuery('#advads-wizard-controls .advads-stop-wizard').addClass('hidden');
	} else {
	    jQuery('#advads-wizard-controls .advads-stop-wizard').removeClass('hidden');
	}
	// hide save button for first boxes
	if( i <= 2 ){
	    jQuery('#advads-wizard-controls-save').addClass('hidden');
	} else {
	    jQuery('#advads-wizard-controls-save').removeClass('hidden');
	}
    },
    next: function(){ // show next box
	// get index of current item in box-array
	var i = this.box_order.indexOf( this.current_box );
	// check if there is a next index
	if( this.box_order[ i + 1 ] === undefined ){
	    return;
	}
	// hide current element
	jQuery( this.current_box ).addClass('advads-hide')
	// load next element into current
	this.current_box = this.box_order[ i + 1 ];
	this.show_current_box();
	this.update_nav();
	this.callbacks();
    },
    prev: function(){ // show previous box
	// get index of current item in box-array
	var i = this.box_order.indexOf( this.current_box );
	// check if there is a previous index
	if( this.box_order[ i - 1 ] === undefined ){
	    return;
	}
	// hide current element
	jQuery( this.current_box ).addClass('advads-hide')
	// load next element into current
	this.current_box = this.box_order[ i - 1 ];
	this.show_current_box();
	this.update_nav();
	this.callbacks();
    },
    callbacks: function(){ // a number of custom settings for some of the boxes
	return;
	switch( this.current_box ){
	    case '#post-body-content' :
		// hide shortcode and php information
		
		break;
	}
    },
    save_hide_wizard: function( hide_wizard ){ // update wizard state (started by default or not?)

	    jQuery.ajax({
		    type: 'POST',
		    url: ajaxurl,
		    data: {
			    action: 'advads-save-hide-wizard-state',
			    hide_wizard: hide_wizard,
		    },
	    });
    }
};