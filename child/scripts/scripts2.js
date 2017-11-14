function addLink() {
	var body_element = document.getElementsByTagName('body')[0];
	var selection = document.getSelection();
	var pagelink = "<p>Источник: <a href='"+document.location.href+"'>"+document.location.href+"</a></p>";
	var copytext = selection + pagelink;
	var newdiv = document.createElement('div');
	body_element.appendChild(newdiv);
	newdiv.innerHTML = copytext;
	selection.selectAllChildren(newdiv);
	window.setTimeout( function() {
		body_element.removeChild(newdiv);
	}, 0);
}
document.oncopy = addLink;

function page(lim, lim2, lim3){
    jQuery.ajax({
        type: "POST",
        data: {
            lim: lim
        },
        dataType: "text",
        success: function(result) {
            if (result) {
                jQuery("body").html(result);

            }
            else alert("Error");
        }
    });
}

jQuery(document).ready(function () {
    /*jQuery(window).scroll(function () {
        var a, b;
        a = jQuery('.textwidget').children().children('div').height();

        b = jQuery('.textwidget').children().children('div').offset().top;

        var c = b + a;

        var f = jQuery('#footer').offset().top;

        if(c > f){
            jQuery('.textwidget').children().css({'position':'initial'});
            jQuery('.textwidget').children().children().css({'top':'0'});
            jQuery('div.et_pb_widget').attr('style', 'overflow: auto !important');
        }
    });*/

    if(jQuery('.social-tabs')){
        jQuery('.social-tabs').next().next().next().hide();
    }
});