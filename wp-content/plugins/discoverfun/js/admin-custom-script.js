(function($){
    $(function(){
        //use to delete images
        $('#delete-image').click(function(e){
            e.preventDefault();
            var imgs = '';
            $('input[name^="img_id"]:checked').each(function(){
                imgs += $(this).attr('value') + ',';
            });
            if(imgs != '') {
                var y = confirm("Continue Delete?");
                if(y == true) {
                  $.ajax({
                    url: ajaxurl,
                    type:'post',
                    data:'action=delete-post-files&img_id=' + imgs,
                    success:function(data){
                      $('input[name^="img_id"]:checked').each(function(){
                        $(this).closest('tr').remove();
                      });
                    } 
                  });
                }
            }

        });
        //use to update image
        $('.edit-thumb').click(function(e){
            e.preventDefault();
            //reset all input
            var btn = $(this);
             var $img_id         = $(this).closest('tr').find('input[name^="img_id"]'),
                 $caption        = $(this).closest('tr').find('input[name^="caption"]'),
                 $description    = $(this).closest('tr').find('textarea[name^="description"]'),
                 $feat           = $(this).closest('tr').find('input[name^="featured"]');

            $('input[name^="caption"],textarea[name^="description"]').attr('disabled','disabled');
            $('input[name^="img_id"]').removeAttr('checked').removeAttr('disabled');
            //check if class is exists
            
            if ($(this).hasClass("update-thumb") == true){
                var f = $feat.is(':checked') ? 1 : 0;

                var varData = 'action=update-post-file&img_id='+ $img_id.attr('value') +'&caption='+ $caption.attr('value') +'&description='+ $description.attr('value') +'&featured=' + f;
                console.log(varData);
                

                $.ajax({
                    url:ajaxurl,
                    type:'post',
                    data:varData,
                    dataType:'json',
                    success:function(data){
                        console.log(data);
                        if(data.status == true) {
                            $img_id.removeAttr('checked').removeAttr('disabled');

                            $caption.attr('disabled','disabled');
                            $description.attr('disabled','disabled');
                            //add Class
                            btn.removeClass('update-thumb');
                            //change text
                            btn.text('EDIT');
                        }
                    },
                  error: function (xhr, ajaxOptions, thrownError) {
                    /*console.log(xhr);
                    console.log(thrownError);
                    console.log(ajaxOptions);*/
                  }


                });

            } else {
                $caption.removeAttr('disabled');
                $description.removeAttr('disabled');
                btn.addClass('update-thumb');
                btn.text('UPDATE');
            }

            return false;
        });


        //use to select all checkbox
        $('#checkAll').change(function(){
            $('.image-tble').find('input[name^="img_id"]').attr('checked',this.checked);

        });


        $('.action li.edit').click(function(e){
          e.preventDefault();
          var parent = this,
              tdWrapper = $(parent).closest('td'),
              trWrapper = $(parent).closest('tr');

          $('.banner-ul').hide();
          

          if($('a',this).text() == 'edit'){
            $('a',this).text('update');
            tdWrapper.find('.banner-ul').fadeIn();
            tdWrapper.find('.banner-xpyr-date').fadeOut();
            tdWrapper.find('li.cancel').fadeIn();
          } else {
            var post_id = trWrapper.find('input[name^="post"]').attr('value'),
                location = tdWrapper.find('input[name^="location"]').attr('value'),
                from = tdWrapper.find('.from').attr('value'),
                to = tdWrapper.find('.to').attr('value'),
                status = (tdWrapper.find('input[name^="status"]').is(':checked')) ? 1 : 0;

            var myData = 'action=set-ads-xpyr-date&post_id='+ post_id +'&location='+ location +'&from='+ from +'&to='+ to +'&status='+status;
            $.ajax({
              url:ajaxurl,
              type:'post',
              dataType:'json',
              data:myData,
              success:function(e){
                
                var msg = e.response['msg'];
                if(e.status == false && e.response['error'].length > 0) {
                  for(var i=0; i < e.response['error_count']; i++ ){
                    msg += '\n' + e.response['error'][i];
                  }
                } else{
                  tdWrapper.find('li.cancel').fadeIn();
                  $('a',parent).text('edit');
                  window.location.reload(true);
                }
                alert(msg);
              },
              error:function(xhr, ajaxOptions, thrownError) {
                /*console.log(xhr);
                console.log(thrownError);
                console.log(ajaxOptions);*/
              }




            })


           
            
          }
         

          return false;
        })
        $('.action li.cancel').click(function(e){
           var parent = this,
              tdWrapper = $(parent).closest('td'),
              trWrapper = $(parent).closest('tr');

              tdWrapper.find('.edit a').text('edit');
              tdWrapper.find('.banner-ul').fadeOut();
              tdWrapper.find('.banner-xpyr-date').fadeIn();
              $(parent).fadeOut();

        });

    });  

    $(function() {
        //$( ".from,.to" ).datetimepicker();
        var startDateTextBox = $('.from');
        var endDateTextBox = $('.to');

        startDateTextBox.datetimepicker({ 
          onClose: function(dateText, inst) {
            if (endDateTextBox.val() != '') {
              var testStartDate = startDateTextBox.datetimepicker('getDate');
              var testEndDate = endDateTextBox.datetimepicker('getDate');
              if (testStartDate > testEndDate)
                endDateTextBox.datetimepicker('setDate', testStartDate);
            }
            else {
              endDateTextBox.val(dateText);
            }
          },
          onSelect: function (selectedDateTime){
            endDateTextBox.datetimepicker('option', 'minDate', startDateTextBox.datetimepicker('getDate') );
          }
        });
        endDateTextBox.datetimepicker({ 
          onClose: function(dateText, inst) {
            if (startDateTextBox.val() != '') {
              var testStartDate = startDateTextBox.datetimepicker('getDate');
              var testEndDate = endDateTextBox.datetimepicker('getDate');
              if (testStartDate > testEndDate)
                startDateTextBox.datetimepicker('setDate', testEndDate);
            }
            else {
              startDateTextBox.val(dateText);
            }
          },
          onSelect: function (selectedDateTime){
            startDateTextBox.datetimepicker('option', 'maxDate', endDateTextBox.datetimepicker('getDate') );
          }
        });
        /*$( ".home_to" ).datetimepicker({
          defaultDate: "+1w",
          changeMonth: true,
          numberOfMonths: 1,
          onClose: function( selectedDate ) {
            $( ".home_from" ).datetimepicker( "option", "maxDate", selectedDate );
          }
        });

        $( ".hotel_from" ).datetimepicker({
          defaultDate: "+1w",
          changeMonth: true,
          numberOfMonths: 1,
          onClose: function( selectedDate ) {
            $( ".hotel_to" ).datetimepicker( "option", "minDate", selectedDate );
          }
        });
        $( ".hotel_to" ).datetimepicker({
          defaultDate: "+1w",
          changeMonth: true,
          numberOfMonths: 1,
          onClose: function( selectedDate ) {
            $( ".hotel_from" ).datetimepicker( "option", "maxDate", selectedDate );
          }
        });

        $( ".rest_from" ).datetimepicker({
          defaultDate: "+1w",
          changeMonth: true,
          numberOfMonths: 1,
          onClose: function( selectedDate ) {
            $( ".rest_to" ).datetimepicker( "option", "minDate", selectedDate );
          }
        });
        $( ".rest_to" ).datetimepicker({
          defaultDate: "+1w",
          changeMonth: true,
          numberOfMonths: 1,
          onClose: function( selectedDate ) {
            $( ".rest_from" ).datetimepicker( "option", "maxDate", selectedDate );
          }
        });

        $( ".resort_from" ).datetimepicker({
          defaultDate: "+1w",
          changeMonth: true,
          numberOfMonths: 1,
          onClose: function( selectedDate ) {
            $( ".resort_to" ).datetimepicker( "option", "minDate", selectedDate );
          }
        });
        $( ".resort_to" ).datetimepicker({
          defaultDate: "+1w",
          changeMonth: true,
          numberOfMonths: 1,
          onClose: function( selectedDate ) {
            $( ".resort_from" ).datetimepicker( "option", "maxDate", selectedDate );
          }
        });*/
  });





})(jQuery);