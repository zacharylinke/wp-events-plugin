(function( $ ) {
    
    /********************************
    ** EVENT PICKER
    ********************************/
    var endDate;
    // START DATE
    $( '#uep-event-start-date, #uep-event-repeat-single-start-date' ).datepicker({
        dateFormat: 'MM dd, yy',
        onClose: function( selectedDate ){
            $( '#uep-event-end-date, #uep-event-repeat-single-start-date' ).datepicker( 'option', 'minDate', selectedDate );
        }
    });
    // END DATE
    $( '#uep-event-end-date, #uep-event-repeat-single-end-date' ).datepicker({
        dateFormat: 'MM dd, yy',
        onClose: function( selectedDate ){
            endDate = selectedDate ;
            $( '#uep-event-start-date, #uep-event-repeat-single-end-date' ).datepicker( 'option', 'maxDate', selectedDate );
        }
    });    
    // REPEAT OPTIONS VISIBILITY
    $( '#uep-event-repeat').change( function(e){
        if($('#uep-event-repeat').prop('checked')){
            $('#uep-event-single-repeat-container, #uep-event-repeat-single-container').show();
        }else{
            $('#uep-event-single-repeat-container, #uep-event-repeat-single-container').hide();
        }
    });
    // REPEAT SINGLE?
    $( '#uep-event-single-repeat-container input' ).on("click", function(){
        console.log('worked');
         if($('#uep-event-single-repeat-container input:checked').val() == 'multiple'){
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
    $( '#uep-event-end-repeat-date' ).datepicker({
        dateFormat: 'MM dd, yy'
        /*onClose: function(){
            $( '#uep-event-start-date' ).datepicker( 'option', 'maxDate', selectedDate );
        }*/
    });

 
})( jQuery );