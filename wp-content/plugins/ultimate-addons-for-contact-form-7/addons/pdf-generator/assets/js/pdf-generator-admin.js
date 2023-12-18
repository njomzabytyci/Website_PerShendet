;(function ($) {
    'use strict';
    $ ( document ).ready(function() {  
        wp.codeEditor.initialize($('#custom_pdf_css'), pdf_settings);

    });

    $('#upload_image_button').click(function(e) {
        e.preventDefault();
        var image = wp.media({
            title: 'Upload Image',
            // mutiple: true if you want to upload multiple files at once
            multiple: false
        }).open()
        .on('select', function(e){
            // This will return the selected image from the Media Uploader, the result is an object
            var uploaded_image = image.state().get('selection').first();
            // We convert uploaded_image to a JSON object to make accessing it easier
            // Output to the console uploaded_image
            console.log(uploaded_image);
            var image_url = uploaded_image.toJSON().url;
            // Let's assign the url value to the input field
            $('#upload_image').val(image_url);
        }).on('close',function() {
            var data = {
                action: 'reset_upload_dir'
            };
            jQuery.post(ajaxurl, data, function(response) {
                alert('Got this from the server: ' + response);
            });
        });
    });
    $('#upload_pdf_image_button').click(function(e) {
        e.preventDefault();
        var image = wp.media({
            title: 'Upload Image',
            // mutiple: true if you want to upload multiple files at once
            multiple: false
        }).open()
        .on('select', function(e){
            // This will return the selected image from the Media Uploader, the result is an object
            var uploaded_image = image.state().get('selection').first();
            // We convert uploaded_image to a JSON object to make accessing it easier
            // Output to the console uploaded_image
            console.log(uploaded_image);
            var image_url = uploaded_image.toJSON().url;
            // Let's assign the url value to the input field
            $('#pdf_bg_upload_image').val(image_url);
        }).on('close',function() {
            var data = {
                action: 'reset_upload_dir'
            };
            jQuery.post(ajaxurl, data, function(response) {
                alert('Got this from the server: ' + response);
            });
        });
    });
    
    $('.uacf7-db-pdf').click(function(e) {
        e.preventDefault();
        var $this = $(this);
        var form_id = $this.attr('data-form-id');
        var  id = $this.attr('data-id');
        var old_button_text = $this.html();
        $this.html('<img src="'+database_admin_url.plugin_dir_url+'assets/images/loader.gif" alt="">');
        jQuery.ajax({
            url: pdf_settings.ajaxurl,
            type: 'post',
            data: {
                action: 'uacf7_get_generated_pdf',
                form_id: form_id,
                id: id,
                ajax_nonce: pdf_settings.nonce,
            },
            success: function (data) {
                $this.html(old_button_text);
                if(data.status == 'success'){ 
                    // window.location.href = data.url; 
                    window.open(data.url, '_blank');
                }else{
                    alert(data.message);
                }
            
            }
    }); 
       
    });

  

})(jQuery);
