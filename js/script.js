(function( $ ) {

$(document).ready(function(){
    
    /********************************
    ** EVENT PICKER
    ********************************/
    var endDate;

    // START DATE, END DATE, REPEAT END
    $( '#uep-event-start-date, #uep-event-end-date, #uep-event-end-repeat-date' ).datepick();

    // MANUAL MULTI-SELECT REPEAT DATE
    $( '#uep-event-manual-repeat-dates' ).datepick({
        multiSelect: 999,
        monthsToShow: 2,
    });

    // START TIME, END TIME
    $('#uep-event-start-time, #uep-event-end-time').timepicker();

    // IF REPEAT EVENT IS CHECKED, THEN SHOW REPEAT SECTIONS
    if($('#uep-event-repeat').prop('checked')){
        $('#uep-event-repeat-type-container').show();
        // INITIAL REPEAT TYPE CHECK
        if($('#uep-event-repeat-type-container input:checked').val() == 'auto'){
            $('#uep-event-auto-repeat-container').show();
            $('#uep-event-manual-repeat-container').hide();
        // IF MANUAL REPEAT SELECTED   
        }else{
            $('#uep-event-auto-repeat-container').hide();
            $('#uep-event-manual-repeat-container').show();
        }
    }else{
        $('#uep-event-auto-repeat-container').hide();
        $('#uep-event-manual-repeat-container').hide();
    }

        
    

    // REPEAT OPTIONS VISIBILITY, CHECK REPEAT ON CHANGE
    $( '#uep-event-repeat').change( function(e){
        if($('#uep-event-repeat').prop('checked')){
            $('#uep-event-repeat-type-container, #uep-event-manual-repeat-container').show();
        }else{
            $('#uep-event-repeat-type-container, #uep-event-manual-repeat-container').hide();
        }
    });

    // REPEAT TYPE CHECK ( AUTO OR MANUAL )
    $( '#uep-event-repeat-type-container input' ).on("click", function(){
         // IF AUTO REPEAT SELECTED
        if($('#uep-event-repeat-type-container input:checked').val() == 'auto'){
            $('#uep-event-auto-repeat-container').show();
            $('#uep-event-manual-repeat-container').hide();
         // IF MANUAL REPEAT SELECTED   
        }else{
            $('#uep-event-auto-repeat-container').hide();
            $('#uep-event-manual-repeat-container').show();
        }
    });

});
    
})( jQuery );