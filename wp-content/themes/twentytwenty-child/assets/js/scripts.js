jQuery(function($) {
    var counter = 0

    jQuery('body').on('click', '.multiImageUploadButton', function(e) {
        e.preventDefault();
            var controllerID = parseInt(jQuery('.maxUploadsController').attr('value'));
            var button = jQuery(this),
            custom_uploader = wp.media({
                title: 'Insert image',
                button: { text: 'Use this image' },
                multiple: true 
            }).on('select', function() {
                var attech_ids = '';
                var max = 5;
                attachments
                var attachments = custom_uploader.state().get('selection'),
                attachment_ids = new Array(),
                i = 0;
                attachments.each(function(attachment) {
                    if (i > max || controllerID > max) {
                        console.log("CUMPLIO");
                        jQuery('.maxImageUploadFailed').show();
                        jQuery('.multiImageUploadButton').hide();
                        return false;
                    } else {
                        jQuery('.maxImageUploadFailed').hide();
                        attachment_ids[i] = attachment['id'];
                        attech_ids += ',' + attachment['id'];
                        if (attachment.attributes.type == 'image') {
                            jQuery(button).siblings('ul').append('<li data-attechment-id="' + attachment['id'] + '"><a href="' + attachment.attributes.url + '" target="_blank"><img class="true_pre_image" src="' + attachment.attributes.url + '" /></a><i class=" dashicons dashicons-no delete-img"></i></li>');
                        } else {
                            jQuery(button).siblings('ul').append('<li data-attechment-id="' + attachment['id'] + '"><a href="' + attachment.attributes.url + '" target="_blank"><img class="true_pre_image" src="' + attachment.attributes.icon + '" /></a><i class=" dashicons dashicons-no delete-img"></i></li>');
                        }
                        controllerID++
                        jQuery('.maxUploadsController').attr('value', controllerID);
                    }
                    i++;
                });
                var ids = jQuery(button).siblings('.attechments-ids').attr('value');
                if (ids) {
                    if (i < max || controllerID < max) { var ids = ids + attech_ids;	}
                    jQuery(button).siblings('.attechments-ids').attr('value', ids);
                } else {
                    jQuery(button).siblings('.attechments-ids').attr('value', attachment_ids);
                }
                jQuery(button).siblings('.multiImageUploadRemove').show();
            })
            .open();
    });

    jQuery('body').on('click', '.multiImageUploadRemove', function() {
        jQuery(this).hide().prev().val('').prev().addClass('button').html('Add Media');
        jQuery(this).parent().find('ul').empty();
        jQuery('.multiImageUploadButton').show();
        jQuery('.maxImageUploadFailed').hide();
        jQuery('.maxUploadsController').attr('value', 0);
        jQuery('.attechments-ids').attr('value', '');
        return false;
    });

});

jQuery(document).ready(function() {
    jQuery(document).on('click', '.multiImageUpload ul li i.delete-img', function() {
        var newUploadValue = parseInt(jQuery('.maxUploadsController').attr('value'))-1;
        console.log(newUploadValue);
        var ids = [];
        var this_c = jQuery(this);
        jQuery(this).parent().remove();
        jQuery('.multiImageUpload ul li').each(function() {
            ids.push(jQuery(this).attr('data-attechment-id'));
        });
        jQuery('.attechments-ids').attr('value', ids);
        jQuery('.multiImageUploadButton').show();
        jQuery('.maxImageUploadFailed').hide();
        jQuery('.maxUploadsController').attr('value', newUploadValue);
    });
})