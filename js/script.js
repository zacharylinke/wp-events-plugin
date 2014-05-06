(function( $ ) {
    
    /********************************
    ** EVENT PICKER
    ********************************/
    var endDate;
    // START DATE
    $( '#uep-event-start-date' ).datepicker({
        dateFormat: 'MM dd, yy',
        onClose: function( selectedDate ){
            $( '#uep-event-end-date' ).datepicker( 'option', 'minDate', selectedDate );
        }
    });
    // END DATE
    $( '#uep-event-end-date' ).datepicker({
        dateFormat: 'MM dd, yy',
        onClose: function( selectedDate ){
            endDate = selectedDate ;
            $( '#uep-event-start-date' ).datepicker( 'option', 'maxDate', selectedDate );
        }
    });    
    // REPEAT OPTIONS VISIBILITY
    $( '#uep-event-repeat').change( function(e){
        if($('#uep-event-repeat').prop('checked')){
            $('#uep-event-repeat-days').show();
        }else{
            $('#uep-event-repeat-days').hide();
        }
    });
    // REPEAT END PICKER
    $( '#uep-event-end-repeat-date' ).datepicker({
        dateFormat: 'MM dd, yy'
        /*onClose: function(){
            $( '#uep-event-start-date' ).datepicker( 'option', 'maxDate', selectedDate );
        }*/
    });

 
})( jQuery );