(function($){
    $(function(){

        /**=========================================
         * User for accomodation check-in and 
         * check-out date
         *=========================================*/

        $('input[name^="check_in"]').datepicker({
          defaultDate: "+1w",
          changeMonth: true,
          numberOfMonths: 1,
          onClose: function( selectedDate ) {
            $( 'input[name^="check_out"]').datepicker( "option", "minDate", selectedDate );
          }
        });
        $( 'input[name^="check_out"]' ).datepicker({
          defaultDate: "+1w",
          changeMonth: true,
          numberOfMonths: 1,
          onClose: function( selectedDate ) {
            $( 'input[name^="check_in"]' ).datepicker( "option", "maxDate", selectedDate );
          }
        });

        /**=========================================
         * User for travel date from and 
         * to date
         *=========================================*/

        $('input[name^="travel_date_from"]').datepicker({
          defaultDate: "+1w",
          changeMonth: true,
          numberOfMonths: 1,
          onClose: function( selectedDate ) {
            $( 'input[name^="travel_date_to"]').datepicker( "option", "minDate", selectedDate );
          }
        });
        $( 'input[name^="travel_date_to"]' ).datepicker({
          defaultDate: "+1w",
          changeMonth: true,
          numberOfMonths: 1,
          onClose: function( selectedDate ) {
            $( 'input[name^="travel_date_from"]' ).datepicker( "option", "maxDate", selectedDate );
          }
        });


    })

})(jQuery);