<?php get_header(); ?>

    <div id="main-content">
        <div class="container">
            <div id="content-area" class="<?php extra_sidebar_class(); ?> clearfix">
                <div class="et_pb_extra_column_main category_all_news_g">
                    <?php
                    $u = explode('/', $_SERVER['REQUEST_URI']);
                    if($u[1] == 'news' && empty($u[2]) ){
                        $n_c = get_category_by_slug( $u[1] );
                        $n_c2 = '22,34';
                    }else{
                        $n_c = get_category_by_slug( $u[2] );
                        $n_c2 = $n_c->term_id;
                    }
                    ?>
                    <div class="hh">
                        <div class="hh1 ">Рубрика: <?php echo $n_c->name ?></div><div class="social-icons ed-social-share-icons">
                            <?php
                            $share = extra_post_share_links( false );

                            $facebook = preg_match_all('/http:\/\/www\.facebook\.com\/sharer\.php\?u=(.*)"/U', $share, $f);

                            $share = preg_replace('/http:\/\/www\.pinterest\.com\/pin\/create\/button\/\?url=(.*);description=(.*);media=(.*)"/',
                                'https://vk.com/share.php?url=$1&description=$2&image=$3"', $share);
                            echo $share;
                            ?>
                        </div>
                    </div>
                    <div class="subscribe_share">
                        <div class="subscribe">Подписаться на Avtozakony.ru</div>
                        <a target="_blank" onclick="window.open(this.href,this.target,'width=700,height=400,scrollbars=1');return false;" href="<?php echo $f[0][0]; ?>"><div class="share">Поделиться в Facebook</div></a>
                        <div class="desc_f">
                            Ребята, мы вкладываем душу в Avtozakony.ru. Cпасибо за то,
                            что открываете эту красоту. Спасибо за вдохновение и мурашки.
                            Присоединяйтесь к нам в <span class="f">Facebook</span>

                            <div id="fb-root"></div>
                            <script>(function(d, s, id) {
                                    var js, fjs = d.getElementsByTagName(s)[0];
                                    if (d.getElementById(id)) return;
                                    js = d.createElement(s); js.id = id;
                                    js.src = "//connect.facebook.net/uk_UA/sdk.js#xfbml=1&version=v2.9";
                                    fjs.parentNode.insertBefore(js, fjs);
                                }(document, 'script', 'facebook-jssdk'));</script>

                            <div class="fb-like" data-href="https://www.facebook.com/giznzarulem/" data-layout="button" data-action="like" data-size="large" data-show-faces="true" data-share="true"></div>

                        </div>
                    </div>
                    <div class="et_pb_row et_pb_row_1">
                        <?php
                        $i = 1;

                        global $wpdb;

                        $prefix = $wpdb->get_blog_prefix();

                        $term_relationships = $prefix.'term_relationships';
                        $posts = $prefix.'posts';
                        $terms = $prefix.'terms';
                        $term_taxonomy = $prefix.'term_taxonomy';

                        $res_posts = $wpdb->get_results(
                            $wpdb->prepare(
                                "SELECT * FROM $posts p JOIN $term_relationships tr ON p.ID = tr.object_id JOIN $term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id JOIN $terms t ON tt.term_id = t.term_id WHERE t.term_id IN($n_c2) AND p.post_status = 'publish' ORDER BY p.ID DESC LIMIT %d, 21", 0
                            )
                        );

                        //                        query_posts('cat='.$n_c->term_id.'&posts_per_page=21');
                        //                        while ( have_posts() ) { the_post();
                        foreach ($res_posts as $p){
                            ?>
                            <div class="et_pb_column et_pb_column_1_3 et_pb_column_<?php echo $i++; ?>">
                                <a class="one_line_a" href="<?php echo get_permalink($p->ID) ?>"><?php echo preg_replace('#width="200" height="150"#i', 'width="300px" height="225px"', get_the_post_thumbnail($p->ID, array(200, 150)) ); ?><div class="play_category_all_news"></div></a>
                                <div style="color: black;"><?php echo get_the_time('H:i', $p->ID).' '.get_the_date('d.m.Y' ,$p->ID); ?></div>
                                <a class="one_line_a_t" href="<?php echo get_permalink($p->ID) ?>"><?php echo $p->post_title ?></a>
                            </div>
                            <?php
                            if($i == 4){ $i = 1; }
                        }
                        ?>
                    </div>
                    <?php
                    $num = $wpdb->get_results(
                        "SELECT * FROM $posts p JOIN $term_relationships tr ON p.ID = tr.object_id JOIN $term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id JOIN $terms t ON tt.term_id = t.term_id WHERE t.term_id IN($n_c2) AND p.post_status = 'publish' ORDER BY p.ID DESC"
                    );
                    $num = count($num);
                    $num = $num/21;
                    $num = ceil($num);
                    if($num > 13){
                        ?>
                        <style>
                            .paginacia{
                                overflow-x: scroll;
                            }
                        </style>
                        <?php
                    }
                    ?>
                    <div class="paginacia">
                        <?php if($num>1){ ?>
                            <table class="table_of_pagination">
                                <tr>
                                    <td><a href="#" class="a_pag" onclick="page(0, 'a', 1, '<?= $n_c2 ?>'); return false;"><</a></td>
                                    <td><a href="#" id="a_pag_1" data-x="0" class="a_pag <?php if(empty($_POST['lim']) || $_POST['lim'] == 0){echo 'a_active';} ?>" onclick="page(0, 1, 1, '<?= $n_c2 ?>'); return false;">1<span class="loader"><?php extra_ajax_loader_img(); ?></span></a></td>
                                    <?php
                                    $x=0;
                                    $t = $num - 3;
                                    $t1 = $t + 1;
                                    $t2 = $t + 2;
                                    $t3 = $t + 3;
                                    for($i=2;$i<=$num;$i++){
                                    if($i==2){$x=1;}else{if($i>2){$x+=11;}}
                                    if($_POST['lim'] == $i * 10 + $x){$a = 'a_active';}else{$a = '';}
                                    if( !in_array($i, array(2,3,$t1,$t2,$t3)) ){}else{}
                                    ?>
                                    <td><a href="#" id="a_pag_<?= $i; ?>" class="a_pag <?= $a; ?>" data-x="<?= $x; ?>" onclick="page(<?=($i * 10 + $x); ?>, <?=$i;?>, <?=$i;?>, '<?= $n_c2 ?>'); return false;"><?=$i?><span class="loader"><?php extra_ajax_loader_img(); ?></span></a>
                                        <?php

                                        $count_p = $i;
                                        }
                                        ?>
                                    <td><a href="#" class="a_pag" onclick="page(<?= (($count_p * 10) + $x); ?>, 'b', <?= $count_p; ?>, '<?= $n_c2 ?>'); return false;">></a></td>
                                </tr>
                            </table>
                        <?php } ?>
                    </div>
                </div>
                <?php get_sidebar(); ?>

            </div> <!-- #content-area -->
        </div> <!-- .container -->
    </div> <!-- #main-content -->

<?php get_footer();
