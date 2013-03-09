(function($){
    $(function(){
        /**=======================================
         * Use in accomodation check-in and out date
         * Author:serg
         *========================================*/
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

        /**=======================================
         * Use in accomodation travel from and to date
         * Author:serg
         *========================================*/

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