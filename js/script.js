(function( $ ) {

$(document).ready(function(){
    
    /********************************
    ** EVENT PICKER
    ********************************/
    var endDate;

    // START DATE
    $( '#uep-event-start-date' ).datepick({
        multiSelect: 999,
        monthsToShow: 2,
    });


    $('#uep-event-start-time, #uep-event-end-time').timepicker();




    if($('#uep-event-repeat').prop('checked')){
        $('#uep-event-repeat-type-container, #uep-event-repeat-single-container').show();
    }

    // REPEAT OPTIONS VISIBILITY
    $( '#uep-event-repeat').change( function(e){
        if($('#uep-event-repeat').prop('checked')){
            $('#uep-event-repeat-type-container, #uep-event-repeat-single-container').show();
        }else{
            $('#uep-event-repeat-type-container, #uep-event-repeat-single-container').hide();
        }
    });
    // REPEAT SINGLE?
    $( '#uep-event-repeat-type-container input' ).on("click", function(){
        
         if($('#uep-event-repeat-type-container input:checked').val() == 'multiple'){
            $('#uep-event-repeat-days').show();
            $('#uep-event-repeat-single-container').hide();
        }else{
            $('#uep-event-repeat-days').hide();
            $('#uep-event-repeat-single-container').show();
        }
    });

    // REPEAT AMOUNT
    $( '#uep-event-repeat-amount' ).stepper();
    // REPEAT END PICKER
    /*$( '#uep-event-end-repeat-date' ).multiDatesPicker({
        /*dateFormat: 'MM dd, yy'
        onClose: function(){
            $( '#uep-event-start-date' ).datepicker( 'option', 'maxDate', selectedDate );
        }
    });*/

});
    
})( jQuery );