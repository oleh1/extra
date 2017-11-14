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

// function page(lim, lim2, lim3){
//     jQuery.ajax({
//         type: "POST",
//         data: {
//             lim: lim
//         },
//         dataType: "text",
//         success: function(result) {
//             if (result) {
//                 jQuery("body").html(result);
//
//             }
//             else alert("Error");
//         }
//     });
// }

function page(lim, lim2, lim3, cat){

    jQuery('.et_pb_row.et_pb_row_1').css({
        'opacity':'0.3'
    });

    var x;
    if(lim2 == 'a'){
        if(lim3 != Number(jQuery('.a_active').text())){
            if(Number(jQuery('.a_active').text()) > 2){
                x = Number(jQuery('#a_pag_'+(Number(jQuery('.a_active').text()) - 1)).attr('data-x'));
                lim = ((Number(jQuery('.a_active').text()) - 1) * 10 + x);
            }else{
                x = 0;
                lim = 0;
            }
        }
    }
    if(lim2 == 'b'){
        if(lim3 != Number(jQuery('.a_active').text())){
            if(Number(jQuery('.a_active').text()) >= 2){
                x = Number(jQuery('#a_pag_'+(Number(jQuery('.a_active').text()) + 1)).attr('data-x'));
            }else{
                x = 0
            }
            lim = ((Number(jQuery('.a_active').text()) + 1) * 10 + x);
        }
    }

    var ajaxurl = '/wp-admin/admin-ajax.php';
    jQuery.post(
        ajaxurl,
        {
            'action': 'news_p',
            'lim': lim,
            'cat': cat
        },
        function(result){
            jQuery('.et_pb_row.et_pb_row_1').html(result);

            jQuery('.et_pb_row.et_pb_row_1').css({
                'opacity':'1'
            });

            if(lim2 == 'a'){
                if(lim3 != Number(jQuery('.a_active').text())) {
                    if(lim == 0){
                        a_pag2 = document.getElementById('a_pag_1');
                        var c = 1;
                    }else{
                        a_pag2 = document.getElementById('a_pag_' + ((lim - x) / 10));
                        c = ((lim - x) / 10);
                    }
                    a_pag2.classList.add("a_active");
                    for (var k = 1; k < 30; k++) {
                        if (k != c) {
                            a_pag1 = document.getElementById('a_pag_' + k);
                            a_pag1.classList.remove('a_active');
                        }
                    }
                }
            }else if(lim2 == 'b'){
                if(lim3 != Number(jQuery('.a_active').text())) {
                    a_pag2 = document.getElementById('a_pag_' + ((lim - x) / 10));
                    a_pag2.classList.add("a_active");
                    for (var l = 1; l < 30; l++) {
                        if (l != ((lim - x) / 10)) {
                            a_pag1 = document.getElementById('a_pag_' + l);
                            a_pag1.classList.remove('a_active');
                        }
                    }
                }
            }else{
                a_pag2	=	document.getElementById('a_pag_'+lim2);
                a_pag2.classList.add("a_active");
                for( var i=1; i<30; i++){
                    if(i != lim2){
                        a_pag1	=	document.getElementById('a_pag_'+i);
                        a_pag1.classList.remove('a_active');
                    }
                }
            }
        }
    );

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

    jQuery('.ds-video-b span').click(function () {
        jQuery('.ds-video-b').hide();
    });
    jQuery('.ds-video-b2 span').click(function () {
        jQuery('.ds-video-b2').hide();
    });


    jQuery(document).on( "click", '.highslide-image', function() {
        jQuery('.img_reklama1').css({'display': 'block'});
        jQuery('.img_reklama2').css({'display': 'block'});
    });

    jQuery(document).on( "click", 'img.highslide-image', function() {
        jQuery('.img_reklama1').css({'display': 'none'});
        jQuery('.img_reklama2').css({'display': 'none'});
    });

    jQuery('html').keydown(function(eventObject){
        if (event.keyCode == 27) {
            jQuery('.img_reklama1').css({'display': 'none'});
            jQuery('.img_reklama2').css({'display': 'none'});
        }
    });


    jQuery(".subscribe_share .subscribe").click(function () {
        if ($(".subscribe_share .desc_f").is(":hidden")) {

            jQuery(".subscribe_share .subscribe").css({
                'padding':'4px 10px 6px 10px',
                'border-bottom-left-radius':'0',
                'border-bottom-right-radius':'0'
            });
            jQuery(".subscribe_share .desc_f").css({
                'margin':'0',
                'display':'block'
            });

        } else {

            jQuery(".subscribe_share .subscribe").css({
                'padding':'4px 10px 4px 10px',
                'border-bottom-left-radius':'5px',
                'border-bottom-right-radius':'5px'
            });
            jQuery(".subscribe_share .desc_f").css({
                'margin':'0',
                'display':'none'
            });

        }
        return false;
    });

});