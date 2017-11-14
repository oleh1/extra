<?php get_header(); ?>

    <div id="main-content">
        <div class="container">
            <div id="content-area" class="<?php extra_sidebar_class(); ?> clearfix">
                <div class="et_pb_extra_column_main category_all_news_g">
                    <?php
                    $u = explode('/', $_SERVER['REQUEST_URI']);
                    if($u[1] == 'news' && empty($u[2]) ){
                        $n_c = get_category_by_slug( $u[1] );
                    }else{
                        $n_c = get_category_by_slug( $u[2] );
                    }
                    ?>
                    <div class="hh">
                        <div class="hh1 ">Рубрика: <?php echo $n_c->name ?></div><div class="social-icons ed-social-share-icons"><?php echo extra_post_share_links( false ); ?></div>
                    </div>
                    <div class="et_pb_row et_pb_row_1">
                        <?php
                        $i = 1;
                        query_posts('cat='.$n_c->term_id.'&posts_per_page=21');
                        while ( have_posts() ) { the_post();
                            ?>
                            <div class="et_pb_column et_pb_column_1_3 et_pb_column_<?php echo $i++; ?>">
                                <a class="one_line_a" href="<?php echo get_permalink() ?>"><?php echo preg_replace('#width="200" height="150"#i', 'width="300px" height="225px"', get_the_post_thumbnail(null, array(200, 150)) ); ?><div class="play_category_all_news"></div></a>
                                <div style="color: black;"><?php echo get_the_time().' '.get_the_date(); ?></div>
                                <a class="one_line_a_t" href="<?php echo get_permalink() ?>"><?php echo get_the_title() ?></a>
                            </div>
                            <?php
                            if($i == 4){ $i = 1; }
                        }
                        ?>
                    </div>
                    <?php
                    global $wp_query;
                    $num = $wp_query->max_num_pages;
                    ?>
                    <div class="paginacia">
                        <?php if($num>1){ ?>
                            <table class="table_of_pagination">
                                <tr>
                                    <!--<td><a href="#" class="a_pag" onclick="page(0, 'a', 1); return false;"><</a></td>-->
                                    <td><a href="#" id="a_pag_1" data-x="0" class="a_pag <?php if(empty($_POST['lim']) || $_POST['lim'] == 0){echo 'a_active';} ?>" onclick="page(0, 1, 1); return false;">1<span class="loader"><?php extra_ajax_loader_img(); ?></span></a></td>
                                    <?php
                                    $x=0;
                                    for($i=2;$i<=$num;$i++){
                                        if($i==2){$x=1;}else{if($i>2){$x+=11;}}
                                        if($_POST['lim'] == $i * 10 + $x){$a = 'a_active';}else{$a = '';}
                                        ?>
                                        <td><a href="#" id="a_pag_<?= $i; ?>" class="a_pag <?= $a; ?>" data-x="<?= $x; ?>" onclick="page(<?=($i * 10 + $x); ?>, <?=$i;?>, <?=$i;?>); return false;"><?=$i?><span class="loader"><?php extra_ajax_loader_img(); ?></span></a>
                                        <?php
                                        $count_p = $i;
                                    }
                                    ?>
                                    <!--<td><a href="#" class="a_pag" onclick="page(<?/*= (($count_p * 10) + $x); */?>, 'b', <?/*= $count_p; */?>); return false;">></a></td>-->
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
