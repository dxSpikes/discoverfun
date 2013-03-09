/*
 * jQuery File Upload Plugin JS Example 7.0
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

/*jslint nomen: true, unparam: true, regexp: true */
/*global $, window, document */
(function($){
$(function () {
    'use strict';


    // Initialize the jQuery File Upload widget:
   $('#fileupload').fileupload({
        url: parent.ajaxurl,
        formData: {action: 'post-upload-files'},
        maxFileSize: 5000000,
            acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
            process: [
                {
                    action: 'load',
                    fileTypes: /^image\/(gif|jpeg|png)$/,
                    maxFileSize: 20000000 // 20MB
                },
                {
                    action: 'resize',
                    maxWidth: 1440,
                    maxHeight: 900
                },
                {
                    action: 'save'
                }
            ]
    });



    var ctr = 0;
      $.ajax({
            url: $('#fileupload').fileupload('option', 'url'),
            dataType: 'json',
            context: $('#fileupload')[0]
        }).done(function (result) {
            $(this).fileupload('option', 'completed', function(e, data) {

                var input = $();
                if (data.files) {
                    $.each(data.result.files, function (index, file) {
                        console.log(file);
                        var image_count = parent.jQuery('#image_count');
                        
                        var input_name = $('<input/>');
                        input_name.addClass(file.name);
                        input_name.attr('type', 'hidden');
                        input_name.attr('name', 'filenames[' + ctr + '][name]');
                        input_name.val(file.name);
                        parent.jQuery('#files').append(input_name);
                        
                        var input_size = $('<input/>');
                        input_size.addClass(file.name);
                        input_size.attr('type', 'hidden');
                        input_size.attr('name', 'filenames[' + ctr + '][size]');
                        input_size.val(file.size);
                        parent.jQuery('#files').append(input_size);
                        
                        var input_type = $('<input/>');
                        input_type.addClass(file.name);
                        input_type.attr('type', 'hidden');
                        input_type.attr('name', 'filenames[' + ctr + '][img_type]');
                        input_type.val(file.type);
                        parent.jQuery('#files').append(input_type);

                        /*var input_caption = $('<input/>');
                        input_type.addClass(file.name);
                        input_type.attr('type', 'hidden');
                        input_type.attr('name', 'filenames[' + ctr + '][caption]');
                        input_type.val(file.caption);
                        parent.jQuery('#files').append(input_caption);

                        var input_description = $('<input/>');
                        input_type.addClass(file.name);
                        input_type.attr('type', 'hidden');
                        input_type.attr('name', 'filenames[' + ctr + '][description]');
                        input_type.val(file.description);
                        parent.jQuery('#files').append(input_description);*/

                        
                        image_count.val(parseInt(image_count.val()) + 1);
                        ctr += 1;
                    });
                }
            });
            
            $(this).fileupload('option', 'done').call(this, null, {result: result});
        });

    // Enable iframe cross-domain access via redirect option:
    $('#fileupload').fileupload(
        'option',
        'redirect',
        window.location.href.replace(
            /\/[^\/]*$/,
            '/cors/result.html?%s'
        )
    );

    if (window.location.hostname === 'blueimp.github.com') {
        // Demo settings:
        $('#fileupload').fileupload('option', {
            url: '//jquery-file-upload.appspot.com/',
            maxFileSize: 5000000,
            acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
            process: [
                {
                    action: 'load',
                    fileTypes: /^image\/(gif|jpeg|png)$/,
                    maxFileSize: 20000000 // 20MB
                },
                {
                    action: 'resize',
                    maxWidth: 1440,
                    maxHeight: 900
                },
                {
                    action: 'save'
                }
            ]
        });
        // Upload server status check for browsers with CORS support:
        if ($.support.cors) {
            $.ajax({
                url: '//jquery-file-upload.appspot.com/',
                type: 'HEAD'
            }).fail(function () {
                $('<span class="alert alert-error"/>')
                    .text('Upload server currently unavailable - ' +
                            new Date())
                    .appendTo('#fileupload');
            });
        }
    } else {
        // Load existing files:
        $.ajax({
            // Uncomment the following to send cross-domain cookies:
            //xhrFields: {withCredentials: true},
            url: $('#fileupload').fileupload('option', 'url'),
            dataType: 'json',
            context: $('#fileupload')[0]
        }).done(function (result) {
            $(this).fileupload('option', 'done')
                .call(this, null, {result: result});


        });
    }

});


})(jQuery);
