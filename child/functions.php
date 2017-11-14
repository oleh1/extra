<?php
// Prevent file from being loaded directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

function widget_area_404() {
 
    register_sidebar( array(
        'name' => 'Page 404',
        'id' => 'page_404',
        'description'  => __( 'Widgets placed here will be shown on the 404 Not Found.' ),
        'before_widget' => '<div class="et_pb_post">',
        'after_widget' => '</div>',
        'before_title' => '<h4 class="widgettitle">',
        'after_title' => '</h4>',
    ) );
}
add_action( 'widgets_init', 'widget_area_404' );

// register URL_widget widget
function register_URL_widget() {
    register_widget( 'URL_widget' );
}
add_action( 'widgets_init', 'register_URL_widget' );

class URL_widget extends WP_Widget {

    function __construct() {
        parent::__construct(
            'url_widget', // Base ID
            esc_html__( 'Похожие запросы', 'related_queries' ), // Name
            array( 'description' => esc_html__( 'Показывает 5 похожих записей введенного в URL', 'related_queries' ), ) // Args
        );
    }

    public function widget( $args, $instance ) {
        echo $args['before_widget'];
        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }
        $Path = $_SERVER['REQUEST_URI'];
        $t = explode('/', $Path );
        $r = array_pop( $t );
        $u = urldecode($r);
        $u = preg_replace('/[^a-z_-]/','',$u);
        $query = new WP_Query( array(
            's' => $u,
            'posts_per_page' => 5,
        ) );
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                echo '<a href="'. get_permalink() .'">'. get_the_title() .'</a><br>';
            }
        }
        else { echo 'По данному запросу нечего не найдено.'; }
        echo $args['after_widget'];
    }

    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Посмотрите похожие материалы по Вашему запросу:', 'related_queries' );
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'related_queries' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

        return $instance;
    }

} // class URL_widget

// add scripts and css
function add_theme_scripts() {
    wp_enqueue_script( 'script', '/wp-content/themes/child/scripts/scripts2.js' );
    wp_enqueue_style( 'media_style', '/wp-content/themes/child/media_style.css' );
}
add_action( 'wp_enqueue_scripts', 'add_theme_scripts' );
// add scripts and css

// Remove Redundant Links
function no_link_current_page( $p ) {
    return preg_replace( '%((current_page_item|current-menu-item)[^<]+)[^>]+>([^<]+)</a>%', '$1<a>$3</a>', $p, 1 );
}
add_filter('wp_nav_menu', 'no_link_current_page');
add_filter( 'cancel_comment_reply_link', '__return_false' );

// register Related_posts_widget widget
function register_RP_widget() {
    register_widget( 'RP_widget' );
}
add_action( 'widgets_init', 'register_RP_widget' );

class RP_widget extends WP_Widget {

    function __construct() {
        parent::__construct(
            'related_p_widget', // Base ID
            esc_html__( 'Related posts +', 'related_posts' ), // Name
            array( 'description' => esc_html__( 'Показывает 5 последних записей', 'related_posts' ), ) // Args
        );
    }

    public function widget( $args, $instance ) {
        echo $args['before_widget'];
        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }
        $args = array( 'posts_per_page' => 5,
						'orderby' => 'post_date',
						'order' => 'DESC');
		$query = new WP_Query( $args );
		?>
		<ul>
			<?php
			while ( $query->have_posts() ) {
				$query->the_post();
				$url  = @( $_SERVER["HTTPS"] != 'on' ) ? 'http://'.$_SERVER["SERVER_NAME"] :  'https://'.$_SERVER["SERVER_NAME"];
				$url .= ( $_SERVER["SERVER_PORT"] != 80 ) ? ":".$_SERVER["SERVER_PORT"] : "";
				$url .= $_SERVER["REQUEST_URI"];
				if ($url != get_permalink()){
					echo '<li><a href="'. get_permalink() .'">'. get_the_title() .'</a></li>';
				}
			}
			wp_reset_postdata();
			?>
		</ul>
		</div>
		<?php
        echo $args['after_widget'];
    }

    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Свежие записи', 'related_posts' );
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'related_posts' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

        return $instance;
    }

} // class RP_widget

// cron auto publish

add_action('wp_head', function (){ 
	global $wpdb;
	
	date_default_timezone_set('Europe/Kiev');
	$sql	=	'SELECT ID FROM '.$wpdb->posts.' WHERE post_status="future" AND post_date<="'.date("Y-m-d H:i:s").'"';
	if($query = $wpdb->get_results($sql)){
		foreach ($query as $data){
			$sql = 'UPDATE '.$wpdb->posts.' SET post_status="publish" WHERE ID='.$data->ID;
			$wpdb->query($sql);
		}
	}
});
function form_cat_desc(){
	if(category_description()){
		return '<h1>'.single_cat_title('', false).'</h1>'.category_description();
	}
}
add_shortcode('cat_desc', form_cat_desc);


add_action('wp_head', function (){
    echo '<meta name="keywords" content="осаго каско расчет страховка компания штраф проверка оплата гибдд действие дтп экспертиза выплата регистрация купля продажа автоновости" />';
}, 2);


// register Related_coments_widget widget
function register_RC_widget() {
    register_widget( 'RC_widget' );
}
add_action( 'widgets_init', 'register_RC_widget' );

class RC_widget extends WP_Widget {

    function __construct() {
        parent::__construct(
            'related_c_widget', // Base ID
            esc_html__( 'Related coments +', 'related_coments' ), // Name
            array( 'description' => esc_html__( 'Показывает 5 последних коментариев', 'related_coments' ), ) // Args
        );
    }

    public function widget( $args, $instance ) {
        echo $args['before_widget'];
        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }
		?>
		<?php
			$comments = get_comments( apply_filters( 'widget_comments_args', array(
				'number'      => 5,
				//'type'        => 'comment',
				'status'      => 'approve',
				'post_status' => 'publish',
			) ) );
			if( $comments ){
				echo '<ul>';
				foreach( $comments as $comment ){
					$comm_link = get_comment_link( $comment->comment_ID );
					$comm_short_txt = mb_substr( strip_tags( $comment->comment_content ), 0, 50 ) .'...';
					if(strlen($comment->comment_content) > 3){
						echo '<li>'. $comment->comment_author .': <a rel="nofollow" href="'. $comm_link .'">'. $comm_short_txt .'</a></li>';
					}
				}
				echo '</ul>';
			}
		?>
		</div>
		<?php
        echo $args['after_widget'];
    }

    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Свежие коментарии', 'related_coments' );
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'related_coments' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

        return $instance;
    }

} // class RC_widget

add_shortcode( 'alco_calc', function(){
	require 'includes/alcocalc/index.html';
});

// Perelink from content
function clear($str){
    return trim(str_replace(array("\r\n", "\r", "\n"), '',  strip_tags($str)));
}

function perelink_replace_title( $items = '' ){
    if( ! empty( $items ) ){
        foreach( $items as $k => $v ){
            if( preg_match('/href=[\'"]([^\'"]+)[\'"]/i', $v, $data) ){
                $_ID		= url_to_postid( $data[1] );
                $title		= get_the_title( $_ID );
                $items[$k] 	= preg_replace('/<span>[^<]*<\/span>/i','<span>'.$title.'</span>',$v);
            }
        }
    }
    return $items;
}

add_filter('the_content', function($content){
    if(is_single() && class_exists('PerelinkPlugin')){
        $plCon	= PerelinkPlugin::getAfterText(array(),true);
        if( preg_match_all('/<div[^>]*class="perelink-horizontal-item">(.+)<\/div>/isU', $plCon, $perelink) ){
            $s 			= 0;
            $c 			= 1;
            $x			= 5;
            $perelink	= $perelink[1];
            $perelink	= perelink_replace_title( $perelink );
            $perelink	= array_map(function($data) {
                preg_match_all( '#<span>(.*)</span>#Us', $data, $text );
                $res = wp_trim_words( $text[1][0], 15, ' ...' );
                $data = preg_replace( '#(<span>).*(</span>)#Us', '$1'.$res.'$2', $data );
                return str_replace('<a', '<a target="_blank"', $data);
            }, $perelink);

            if( ! empty( $perelink ) ){
                $n 			= count($perelink);
                $data		= explode("</p>", $content);

                if( preg_match( '/<(img|blockquote)/isU', $data[$x] ) ) $x++;

                $data[ $x ]  .= '<div class="perelink-from-content"><div class="read-to"><span>Читайте также</span></div><div class="perelink-one">'.$perelink[0].'</div></div>';

                for($i=$x;$i<count($data);$i++){ $p++;
                    $s += iconv_strlen(clear($data[$i]), 'UTF-8');
                    $continue = (preg_match('/<(img|blockquote)/isU',$data[$i]))? false : true;

                    if($s>=2300 && $c<$n && $continue && $p>3){
                        $s=0;
                        $p=0;
                        $data[$i] .= '<div class="perelink-from-content"><div class="read-to"><span>Читайте также</span></div><div class="perelink-one">'.$perelink[$c++].'</div></div>';
                    }
                }
                $content = implode('</p>', $data);
            }
        }
    }
    return $content;
}, 999);

// go top

add_filter('the_content', function($content){
	if(is_single()){
		$goTop = '<!--noindex--><div class="goTop"><a href="#">к содержанию</a></div><!--/noindex-->';
		$content = preg_replace('/<h([2-4])/ie', "\$c++>0? '$goTop<h$1' : '<h$1' " , $content);
	}
	return $content;
}, 99999);

//========================================================================================================================
// mata from author

add_action( 'admin_init', function() {
	add_meta_box( "_author", "Автор текста:", function(){
		global $post;
		echo '<input type="text" name="_author" value="'. get_post_meta($post->ID,'_author',true) .'" placeholder="Иван Петров" style="width:100%" />';
	}, "post", "side", "high" );
}, 0);

add_action( 'save_post', function( $post_id ){
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )	return false; 
	if ( !current_user_can('edit_post', $post_id) )		return false;
	update_post_meta($post_id, "_author", preg_replace('/[^\w ]/iu','',$_POST['_author']) );
}, 0);

add_action('wp_head', function(){
	if( is_single() ){
		global $post;
		if( $author = get_post_meta($post->ID,'_author',true) )	echo "<meta name='author' content='". $author ."' />\n";
	}
});
//========================================================================================================================

/*category_all_news*/
//add_filter('template_include', 'my_template');
//function my_template( $template ) {
//
//    if( is_category( array( 33, 34, 22) ) ){
//        return get_stylesheet_directory() . '/category_all_news.php';
//    }
//
//    return $template;
//
//}

//add_filter( 'post_limits', 'limits' );
//function limits( $limit ) {
//    if ( get_post_type() == 'post' && is_category(array( 33, 34, 22)) ) {
//        if (is_numeric($_POST['lim'])) $lim = $_POST['lim']; else $lim = 0;
//        return 'LIMIT '.$lim.', 21';
//    }
//    return $limit;
//}

add_action("wp_ajax_news_p", "f_news_p");
add_action("wp_ajax_nopriv_news_p", "f_news_p");
function f_news_p()
{
    $lim = $_POST['lim'];
    $cat = $_POST['cat'];

    global $wpdb;

    $prefix = $wpdb->get_blog_prefix();

    $term_relationships = $prefix.'term_relationships';
    $posts = $prefix.'posts';
    $terms = $prefix.'terms';
    $term_taxonomy = $prefix.'term_taxonomy';

    $res_posts = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM $posts p JOIN $term_relationships tr ON p.ID = tr.object_id JOIN $term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id JOIN $terms t ON tt.term_id = t.term_id WHERE t.term_id IN($cat) AND p.post_status = 'publish' ORDER BY p.ID DESC LIMIT %d, 21", $lim
        )
    );

    $i = 1;
    foreach ($res_posts as $p){
        ?>
        <div class="et_pb_column et_pb_column_1_3 et_pb_column_<?php echo $i++; ?>">
            <a class="one_line_a" href="<?php echo get_permalink( $p->ID ) ?>"><?php echo preg_replace('#width="200" height="150"#i', 'width="300px" height="225px"', get_the_post_thumbnail($p->ID, array(200, 150)) ); ?><div class="play_category_all_news"></div></a>
            <div style="color: black;"><?php echo get_the_time('H:i' , $p->ID  ).' '.get_the_date('d.m.Y', $p->ID); ?></div>
            <a class="one_line_a_t" href="<?php echo get_permalink($p->ID) ?>"><?php echo $p->post_title ?></a>
        </div>
        <?php
        if($i == 4){ $i = 1; }
    }
    wp_die();
}

add_filter('the_content', function($content){
    $cat = get_the_category( get_the_ID() );
    $args = array(
        'child_of'     => $cat[0]->parent,
    );
    $categories = get_categories( $args );
    $cat_all = array();
    $cat_all[0][0] = $cat[0]->parent;
    if( $categories ){
        $i = 1;
        foreach( $categories as $cat ){
            $cat_all[0][$i] = $cat->term_id;
            $i++;
        }
    }
    if( in_array(array(33,22,34), $cat_all) && is_single() ){

        $share = '<div class="social-icons_2">
					<div class="share-title_2">ПОДЕЛИТЬСЯ:</div><div class="share-icon_2">';

        $share .= extra_post_share_links( false );

        $share = preg_replace('#(et-extra-icon-facebook)#i', '$1 category_facebook ', $share);
        $share = preg_replace('#(et-extra-icon-twitter)#i', '$1 category_twitter ', $share);
        $share = preg_replace('#(et-extra-icon-googleplus)#i', '$1 category_googleplus ', $share);
        $share = preg_replace('#(et-extra-icon-pinterest)#i', '$1 category_pinterest ', $share);
        $share = preg_replace('#(et-extra-icon-linkedin)#i', '$1 category_linkedin ', $share);
        $share = preg_replace('#(et-extra-icon-buffer)#i', '$1 category_buffer ', $share);
        $share = preg_replace('#(et-extra-icon-basic_email)#i', '$1 category_basic_email ', $share);
        $share = preg_replace('#(et-extra-icon-basic_print)#i', '$1 category_basic_print ', $share);

        $share = preg_replace('/http:\/\/www\.pinterest\.com\/pin\/create\/button\/\?url=(.*);description=(.*);media=(.*)"/',
            'https://vk.com/share.php?url=$1&description=$2&image=$3"', $share);

        $share .= '</div></div>';

        $content .= $share;
    }
    return $content;
});
/*category_all_news*/

//=============================================================================================================
// UTM generator

add_action(	'admin_menu', function(){
	add_options_page('Facebook UTM', 'Настройки UTM', 10, 'utm-settings-data', function(){
		if(isset( $_POST['save-utm-data'] ))
			update_option('_r_utm_chanel', trim( $_POST['_r_utm_chanel'] ) );
		
		$data = get_option('_r_utm_chanel');
	
		echo '<form method="POST">Канал (utm_source)
				<input type="text" name="_r_utm_chanel" value="'.( $data ? $data : '' ).'" />
				<button name="save-utm-data">Save</button></form>';
	});
});

add_action( 'admin_init', function(){
	add_meta_box( "_r_utm2", "Facebook UTM", function(){
		global $post;
		
		if( $source = get_option('_r_utm_chanel') ){
	
			$link	= get_permalink( $post->ID );
			$link	.= '?utm_source='	. $source;
			$link	.= '&utm_medium='	. $source .'-'. $post->post_name;
			$link	.= '&utm_campaign=' . $source .'-'. $post->post_name;
		
			echo $link;
		} else
			echo 'Задайте название канала в <a href="options-general.php?page=utm-settings-data" target="_blank">настройках</a>';
	}
	, "post", "side", "low" );
}, 0);

// add_action( 'save_post', function( $post_id ){
	// if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )	return false; 
	// if ( !current_user_can('edit_post', $post_id) )		return false;
	// global $post;
	
	// update_post_meta($post_id, "_r_utm_data", $_POST['_r_utm_data'] );
// }, 0);

//=============================================================================================================

add_filter('the_content', function($content){

    if(is_single()){

        $r = '';
        $cat = get_the_category( get_the_ID() );
        $args = array(
            'child_of'     => $cat[0]->parent,
        );
        $categories = get_categories( $args );
        $cat_all = array();
        $cat_all[0][0] = $cat[0]->parent;
        if( $categories ){
            $i = 1;
            foreach( $categories as $cat ){
                $cat_all[0][$i] = $cat->term_id;
                $i++;
            }
        }

        $r .= '<div class="post-footer">';
        if ( extra_is_post_rating_enabled() ) {
            $post_id = empty( $post_id ) ? get_the_ID() : $post_id;
            $title = 'НА СКОЛЬКО БЫЛА ПОЛЕЗНА СТАТЬЯ:';
            if ( $rating = extra_get_user_post_rating( $post_id ) ) {
                $output = '<p id="rate-title" class="rate-title">' . $title . '</p>';
                $output .= '<div id="rated-stars">' . extra_make_fixed_stars( $rating ) . '</div>';
            } else {
                $output = '<p id="rate-title" class="rate-title">' . $title . '</p>';
                $output .= '<div id="rating-stars"></div>';
                $output .= '<input type="hidden" id="post_id" value="' . $post_id . '" />';
            }
            $r .= '<div class="rating-stars">'.$output.'</div><div>'.do_shortcode("[rBlock name=after_reiting return=1]").'</div>';
        }

        $reklama2 = '<div>'.do_shortcode("[rBlock name=after_sharing return=1]").'</div>';
        if( !in_array(array(33,22,34), $cat_all) && is_single() ){
            $r .= '<div class="social-icons ed-social-share-icons"><p class="share-title">ПОДЕЛИТЬСЯ:</p>';
            $share = extra_post_share_links(false);
            $share = preg_replace('/http:\/\/www\.pinterest\.com\/pin\/create\/button\/\?url=(.*);description=(.*);media=(.*)"/',
                'https://vk.com/share.php?url=$1&description=$2&image=$3"', $share);
            $r .= $share.'</div></div>';
            $r .= $reklama2;
        }else{
            $r .= '</div>'.$reklama2;
        }

    }

    return $content.$r;
}, 1001);

add_filter('the_content', function($content) {
    if (is_single()) {
         $r = '' . do_shortcode("[rBlock name=after_text return=1]");
    }
    return $content . $r;
}, 1201);

add_filter('the_content', function($content){
    if(is_single()){
        $content = preg_replace('/(<iframe.*src=[\'"](|http(|s):)\/\/(|www\.)(thesame|youtube)[^\'"]*[\'"][^>]*>[^<]*<\/iframe>)/iU', '<div class="viboom-overroll">$1</div>', $content);
    }
    return $content;
});

add_action('wp_footer', function (){
    if( is_attachment() ){
        $r = '
        <style>
        #leadia_science_widget{
            display: none;
        }
        .distr-dinamic-right, distr-dinamic-left{
            display: none;
        }
        </style>
        ';
        echo $r;
    }
});

add_action( 'register_form', 'verification_of_personal_data' );
function verification_of_personal_data() {
    ?>
    <div id="ver"></div>
    <p>
        <input type="checkbox" onclick="checked_data()" id="verification_of_personal_data" required /> <label for="verification_of_personal_data" style="cursor: pointer;"><span style="color:green;">Согласен на обработку моих данных</span></label></br>
        <span style="color:green;">Данные не будут переданы 3-м лицам.</span>
    </p>
    <script>
        window.onload = function() {

            document.getElementById('wp-submit').setAttribute('type', 'button');
            document.getElementById('wp-submit').onclick = function(){
                var element = document.getElementById('ver');
                element.innerHTML = '<div style="color: red">Обязательное поле</div>';
                var element2 = document.getElementById('verification_of_personal_data');
                if(!element2.checked) {
                    document.getElementById('verification_of_personal_data').style.background = 'red';
                }
            }

        };

        function  checked_data() {
            var element = document.getElementById('verification_of_personal_data');

            if (element.checked) {
                document.getElementById('wp-submit').setAttribute('type', 'submit');
                document.getElementById('ver').style.display = 'none';
                document.getElementById('verification_of_personal_data').style.background = '#ffffff';
            }

            if (!element.checked) {
                document.getElementById('wp-submit').setAttribute('type', 'button');
                document.getElementById('ver').style.display = 'block';
                document.getElementById('verification_of_personal_data').style.background = 'red';
            }
        }

    </script>
    <?php
}

add_action( 'comment_form', 'add_checked' );
function add_checked( $post_id ){
    if ( !is_user_logged_in() ) {
        echo '
        <p>
            <input style="width: 20px;height: 20px;position: relative;top: 5px;" type="checkbox" id="verification_of_personal_data_comment" required /> <label style="cursor: pointer;" for="verification_of_personal_data_comment"><span style="font-weight: bold;">Согласен на обработку моих данных. Данные не будут переданы 3-м лицам.</span></label>
        </p>
        ';
    }
}

add_action('wp_footer', function(){
    if( is_single() && get_the_ID() == '7669' || is_single() && get_the_ID() == '7645' || is_single() && get_the_ID() == '7575'){
        echo '
        <script>
        jQuery(document).ready(function(){
            setTimeout(function() {
                var p = jQuery(".fluid-width-video-wrapper").parent().parent();
                var c = jQuery(".fluid-width-video-wrapper").children();
                p.append(c);
                c.width(560);
                c.height(315);
                jQuery(".viboom-overroll").remove();
                jQuery(".view-frame-video").remove();
                jQuery(".fluid-width-video-wrapper").remove();
            }, 1000);
        });
        </script>
        ';
    }
});

/*Изменение ссылки в Facebook*/
function admin_style_script() {
    // дальше у нас идут скрипты и стили загрузчика изображений WordPress
    if ( ! did_action( 'wp_enqueue_media' ) ) {
        wp_enqueue_media();
    }
    wp_enqueue_script( 'admin_js', get_stylesheet_directory_uri() . '/scripts/admin_js.js', array('jquery'), null, false );
    wp_enqueue_style( 'admin_style', get_stylesheet_directory_uri() . '/admin_style.css' );
}
add_action( 'admin_enqueue_scripts', 'admin_style_script' );

function true_image_uploader_field() {
    global $post;
    $title = get_post_meta($post->ID, 'title_facebook_new_meta', true);
    $desc = get_post_meta($post->ID, 'desc_facebook_new_meta', true);
    $img = get_post_meta($post->ID, 'img_facebook_new_meta', true);

    $img_check = get_post_meta($post->ID, 'img_check_facebook', true);

    $title = ($title) ? $title : '';
    $desc = ($desc) ? $desc : '';

    $w = 151;
    $h = 90;
    if( $img ) {
        $src = $img;
        $def = $img;
    } else {
        if($img_check == '1'){
            $src = '';
            $def = get_stylesheet_directory_uri() . '/images2/no_img.png';
        }else{
            preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
            $default = $matches[1][0];
            if($default){
                $src = $default;
                $def = $default;
            }else{
                $src = '';
                $def = get_stylesheet_directory_uri() . '/images2/no_img.png';
            }
        }
    }
    echo '
	
	<table class="fb_meta">
        <tr>
            <td>
                <div><div>Заголовок</div><div><input type="text" name="title_facebook_new_meta" value="'.$title.'"></div></div>
            </td>
            <td>
                <div><div>Описание</div><div><textarea name="desc_facebook_new_meta">'.$desc.'</textarea></div></div>
            </td>
            <td>
                <div>
		            <img src="' . $def . '" width="'.$w.'px" height="'.$h.'px" />
		            <div>
                        <input type="hidden" name="img_facebook_new_meta" value="' . $src . '" />
                        <button type="button" class="upload_image_button button">Добавить</button>
                        <button type="button" class="remove_image_button button">Удалить</button>
                        <input type="hidden" name="img_check_facebook" value="' . $img_check . '" />
		            </div>
	            </div>
            </td>
        </tr>
    </table>
	
	<input type="hidden" name="extra_fields_nonce" value="' . wp_create_nonce(__FILE__) . '" />
	';
}

function true_meta_boxes_u() {
    add_meta_box('facebook_meta_new', 'Изменение вида ссылки Facebook', 'true_image_uploader_field', 'post', 'normal', 'high');
}
add_action('add_meta_boxes', 'true_meta_boxes_u', 1);

function my_extra_fields_update( $post_id ){
    if ( !isset($_POST['extra_fields_nonce']) || !wp_verify_nonce($_POST['extra_fields_nonce'], __FILE__) ) return false; // проверка
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE  ) return false; // если это автосохранение
    if ( !current_user_can('edit_post', $post_id) ) return false; // если юзер не имеет право редактировать запись

    update_post_meta($post_id, 'title_facebook_new_meta', $_POST['title_facebook_new_meta']);
    update_post_meta($post_id, 'desc_facebook_new_meta', $_POST['desc_facebook_new_meta']);
    update_post_meta($post_id, 'img_facebook_new_meta', $_POST['img_facebook_new_meta']);

    $img_check = get_post_meta($post_id, 'img_check_facebook', true);
    if($img_check != '1'){
        update_post_meta($post_id, 'img_check_facebook', $_POST['img_check_facebook']);
    }

    return $post_id;
}
add_action('save_post', 'my_extra_fields_update', 0);

add_action('wp_head', function(){
    global $post;
    $title = get_post_meta($post->ID, 'title_facebook_new_meta', true);
    $desc = get_post_meta($post->ID, 'desc_facebook_new_meta', true);
    ?>
    <meta property="og:title" content="<?php echo $title; ?>" />
    <meta property="og:description" content="<?php echo $desc; ?>" />
    <?php
});
add_action('wp_head', function(){
    global $post;
    $img = get_post_meta($post->ID, 'img_facebook_new_meta', true);
    $img_check = get_post_meta($post->ID, 'img_check_facebook', true);
    if(!$img && $img_check != '1'){
        ob_start();
        ob_end_clean();
        preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
        $img = $matches[1][0];
    }
    ?>
    <meta property="og:image" content="<?php echo $img; ?>" />
    <?php
}, 1);
/*Изменение ссылки в Facebook*/

add_filter('the_content', function($content){
    if(is_single() || is_page()){
        $content = preg_replace('/(<table)/Ui', '<div class="scrol_table">$1', $content);
        $content = preg_replace('/(<\/table>)/Ui', '$1</div>', $content);
    }
    return $content;
}, 99999);

function breadcrumbs() {

    /* === ОПЦИИ === */
    $text['home']     = 'Главная'; // текст ссылки "Главная"
    $text['category'] = '%s'; // текст для страницы рубрики
    $text['search']   = 'Результаты поиска по запросу "%s"'; // текст для страницы с результатами поиска
    $text['tag']      = 'Записи с тегом "%s"'; // текст для страницы тега
    $text['author']   = 'Статьи автора %s'; // текст для страницы автора
    $text['404']      = 'Ошибка 404'; // текст для страницы 404

    $showCurrent = 1; // 1 - показывать название текущей статьи/страницы, 0 - не показывать
    $showOnHome  = 0; // 1 - показывать "хлебные крошки" на главной странице, 0 - не показывать
    $delimiter   = ' » '; // разделить между "крошками"
    $before      = ''; // тег перед текущей "крошкой"
    $after       = ''; // тег после текущей "крошки"
    /* === КОНЕЦ ОПЦИЙ === */

    global $post;
    $homeLink = get_bloginfo('url') . '/';
    $linkBefore = '<span typeof="v:Breadcrumb">';
    $linkAfter = '</span>';
    $linkAttr = ' rel="nofollow" property="v:title" ';
    /*   $link = $linkBefore . '<a' . $linkAttr . ' href="%1$s">%2$s</a>' . $linkAfter;*/
    $link = $linkBefore . '<a' . $linkAttr . ' href="/">%2$s</a>' . $linkAfter;


    if (is_home() || is_front_page()) {

        if ($showOnHome == 1) echo '<div id="crumbs"><a href="' . $homeLink . '">' . $text['home'] . '</a></div>';

    } else {

        echo '<div id="crumbs" xmlns:v="http://rdf.data-vocabulary.org/#">' . sprintf($link, $homeLink, $text['home']) . $delimiter;

        if ( is_category() ) {
            $thisCat = get_category(get_query_var('cat'), false);
            if ($thisCat->parent != 0) {
                $cats = get_category_parents($thisCat->parent, TRUE, $delimiter);
                $cats = str_replace('<a', $linkBefore . '<a' . $linkAttr, $cats);
                $cats = str_replace('</a>', '</a>' . $linkAfter, $cats);
                echo $cats;
            }
            echo $before . sprintf($text['category'], single_cat_title('', false)) . $after;

        } elseif ( is_search() ) {
            echo $before . sprintf($text['search'], get_search_query()) . $after;

        } elseif ( is_day() ) {
            echo sprintf($link, get_year_link(get_the_time('Y')), get_the_time('Y')) . $delimiter;
            echo sprintf($link, get_month_link(get_the_time('Y'),get_the_time('m')), get_the_time('F')) . $delimiter;
            echo $before . get_the_time('d') . $after;

        } elseif ( is_month() ) {
            echo sprintf($link, get_year_link(get_the_time('Y')), get_the_time('Y')) . $delimiter;
            echo $before . get_the_time('F') . $after;

        } elseif ( is_year() ) {
            echo $before . get_the_time('Y') . $after;

        } elseif ( is_single() && !is_attachment() ) {
            if ( get_post_type() != 'post' ) {
                $post_type = get_post_type_object(get_post_type());
                $slug = $post_type->rewrite;
                if(get_post_type()!='post')
                    echo '<a href="/'.str_replace('/%faqcat%', '', $post_type->rewrite['slug']).'">'.$post_type->labels->singular_name.'</a>';
                else
                    printf($link, $homeLink . '/' . $slug['slug'] . '/', $post_type->labels->singular_name);
                if ($showCurrent == 1) echo $delimiter . $before . '<!--noindex-->' .get_the_title() . '<!--/noindex-->' . $after;
            } else {
                $cat = get_the_category(); $cat = $cat[0];
                $cats = get_category_parents($cat, TRUE, $delimiter);
                if ($showCurrent == 0) $cats = preg_replace("#^(.+)$delimiter$#", "$1", $cats);
                $cats = str_replace('<a', $linkBefore . '<a' . $linkAttr, $cats);
                $cats = str_replace('</a>', '</a>' . $linkAfter, $cats);
                echo $cats;
                if ($showCurrent == 1) echo $before . '<!--noindex-->' .get_the_title() . '<!--/noindex-->' .$after;
            }

        } elseif ( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() ) {
            $post_type = get_post_type_object(get_post_type());
            echo $before . $post_type->labels->singular_name . $after;

        } elseif ( is_attachment() ) {
            $parent = get_post($post->post_parent);
            $cat = get_the_category($parent->ID); $cat = $cat[0];
            $cats = get_category_parents($cat, TRUE, $delimiter);
            $cats = str_replace('<a', $linkBefore . '<a' . $linkAttr, $cats);
            $cats = str_replace('</a>', '</a>' . $linkAfter, $cats);
            echo $cats;
            printf($link, get_permalink($parent), $parent->post_title);
            if ($showCurrent == 1) echo $delimiter . $before . get_the_title() . $after;

        } elseif ( is_page() && !$post->post_parent ) {
            if ($showCurrent == 1) echo $before . get_the_title() . $after;

        } elseif ( is_page() && $post->post_parent ) {
            $parent_id  = $post->post_parent;
            $breadcrumbs = array();
            while ($parent_id) {
                $page = get_page($parent_id);
                $breadcrumbs[] = sprintf($link, get_permalink($page->ID), get_the_title($page->ID));
                $parent_id  = $page->post_parent;
            }
            $breadcrumbs = array_reverse($breadcrumbs);
            for ($i = 0; $i < count($breadcrumbs); $i++) {
                echo $breadcrumbs[$i];
                if ($i != count($breadcrumbs)-1) echo $delimiter;
            }
            if ($showCurrent == 1) echo $delimiter . $before . get_the_title() . $after;

        } elseif ( is_tag() ) {
            echo $before . sprintf($text['tag'], single_tag_title('', false)) . $after;

        } elseif ( is_author() ) {
            global $author;
            $userdata = get_userdata($author);
            echo $before . sprintf($text['author'], $userdata->display_name) . $after;

        } elseif ( is_404() ) {
            echo $before . $text['404'] . $after;
        }

        if ( get_query_var('paged') ) {
            if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ' (';
            echo __('страница') . ' ' . get_query_var('paged');
            if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ')';
        }

        echo '</div>';

    }
}

function catch_that_image() {
    global $post, $posts;
    $first_img = '';
    ob_start();
    ob_end_clean();
    $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
    $first_img = $matches [1] [0];
    if(!empty($first_img)) return $first_img;
}

add_shortcode('subcategories', 'subcategories');
function subcategories(){
    $subcat = explode('/', $_SERVER['REQUEST_URI']);
    if( $subcat[1] && empty($subcat[2]) ){
        ?>
        <style>
            .et_pb_section.et_pb_section_0.et_section_regular{
                display: none;
            }
        </style>
        <?php
        $cat = get_category_by_slug( $subcat[1] );
        $args = array(
            'type'         => 'post',
            'child_of'     => $cat->term_id,
            'hide_empty'   => 1,
            'taxonomy'     => 'category',
            'orderby'      => 'ID',
            'order'        => 'ASC'
        );
        $categories = get_categories( $args );
        ?>
        <div id="subcats" class="posts-blog-feed-module post-module et_pb_extra_module standard  et_pb_posts_blog_feed_standard_0 paginated et_pb_extra_module" style="border-color:#8e6ecf;" data-current_page="1" data-et_column_type="" data-show_featured_image="1" data-show_author="1" data-show_categories="1" data-show_date="1" data-show_rating="1" data-show_more="1" data-show_comments="1" data-date_format="M j, Y" data-posts_per_page="5" data-order="desc" data-orderby="date" data-category_id="5" data-content_length="excerpt" data-blog_feed_module_type="standard" data-hover_overlay_icon="" data-use_tax_query="1">
            <?php
            foreach ($categories as $c){
                ?>
                <div class="paginated_content">
                    <div class="paginated_page paginated_page_1 active" data-columns>
                        <article id="post-1368" class="post et-format- post-1368 type-post status-publish format-standard hentry category-oformlenie-imushhestva et-doesnt-have-format-content et_post_format-et-post-format-standard">
                            <div class="header">
                                <!--noindex-->
                                <a href="<?php echo get_category_link( $c->term_id ); ?>" title="<?php echo $c->name; ?>" class="featured-image">
                                    <img rel="nofollow" src="<?php echo get_option('z_taxonomy_image'.$c->term_id); ?>" alt="<?php echo $c->name; ?>" />
                                    <span class="et_pb_extra_overlay"></span></a>
                                <!--/noindex-->
                            </div>
                            <!--noindex-->
                            <div class="post-content">
                                <h2 class="post-title entry-title"><a class="et-accent-color" style="color:#db509f;" href="<?php echo get_category_link( $c->term_id ); ?>"><?php echo $c->name; ?></a></h2>

                                <div class="excerpt entry-summary">
                                    <?php
                                    query_posts('cat='.$c->term_id.'&posts_per_page=3');
                                    while ( have_posts() ) { the_post();
                                        ?>
                                        <div>
                                            <div class="post_cat_img"><img src="<?php echo catch_that_image(); ?>"></div>
                                            <div class="post_cat_head"><a href="<?php echo get_permalink() ?>"><?php echo get_the_title(); ?></a></div>
                                        </div>
                                        <div style="clear: both"></div>
                                        <?php
                                    }
                                    ?>
                                    <br>
                                    <a class="read-more-button" href="<?php echo get_category_link( $c->term_id ); ?>">Подробнее</a>
                                </div>
                            </div>
                            <!--/noindex-->
                        </article>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
        <?php
    }else{
        ?>
        <style>
            #subcats{
                display: none;
            }
        </style>
        <?php
    }
}
?>