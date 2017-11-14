jQuery(document).ready(function () {

    /*
     * действие при нажатии на кнопку загрузки изображения
     * вы также можете привязать это действие к клику по самому изображению
     */
    jQuery('.upload_image_button').click(function(){

        var send_attachment_bkp = wp.media.editor.send.attachment;
        var button = jQuery(this);
        wp.media.editor.send.attachment = function(props, attachment) {
            jQuery(button).parent().prev().attr('src', attachment.url);
            jQuery(button).prev().val(attachment.url);
            wp.media.editor.send.attachment = send_attachment_bkp;
        }
        wp.media.editor.open(button);
        return false;
    });
    /*
     * удаляем значение произвольного поля
     * если быть точным, то мы просто удаляем value у input type="hidden"
     */
    jQuery('.remove_image_button').click(function(){
        var r = confirm("Уверены?");
        if (r == true) {
            jQuery(this).parent().prev().attr('src', '/wp-content/themes/child/images2/no_img.png');
            jQuery(this).prev().prev().val('');
            jQuery(this).next().val('1');
        }
        return false;
    });

});