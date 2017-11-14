<?php get_header(); ?>
<div id="main-content">
	<div class="container">
		<div id="content-area" class="clearfix">
			<div class="et_pb_extra_column_main">
				<?php
				do_action( 'et_before_post' );

				if ( have_posts() ) :
					while ( have_posts() ) : the_post(); ?>
						<?php
							$post_category_color = extra_get_post_category_color();
							$et_pb_has_comments_module = has_shortcode( get_the_content(), 'et_pb_comments' );
							$additional_class = $et_pb_has_comments_module ? ' et_pb_no_comments_section' : '';
						?>
						<article id="post-<?php the_ID(); ?>" <?php post_class( 'module single-post-module' . $additional_class ); ?>>
                            <?php do_shortcode("[rBlock name=fix_telephone]"); ?>
							<?php if ( is_post_extra_title_meta_enabled() ) { ?>
							<div class="post-header">
                                <?php
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
                                if( in_array(array(33,22,34), $cat_all) ){
                                    ?>
                                    <div class="social-icons ed-social-share-icons">
                                        <?php
                                        $share = extra_post_share_links( false );

//                                        if( in_array(array(33,22,34), $cat_all) ){
                                        if( false ){
                                            $share = preg_replace('#(et-extra-icon-facebook)#i', '$1 category_facebook ', $share);
                                            $share = preg_replace('#(et-extra-icon-twitter)#i', '$1 category_twitter ', $share);
                                            $share = preg_replace('#(et-extra-icon-googleplus)#i', '$1 category_googleplus ', $share);
                                            $share = preg_replace('#(et-extra-icon-pinterest)#i', '$1 category_pinterest ', $share);
                                            $share = preg_replace('#(et-extra-icon-linkedin)#i', '$1 category_linkedin ', $share);
                                            $share = preg_replace('#(et-extra-icon-buffer)#i', '$1 category_buffer ', $share);
                                            $share = preg_replace('#(et-extra-icon-basic_email)#i', '$1 category_basic_email ', $share);
                                            $share = preg_replace('#(et-extra-icon-basic_print)#i', '$1 category_basic_print ', $share);
                                        }

                                        $facebook = preg_match_all('/http:\/\/www\.facebook\.com\/sharer\.php\?u=(.*)"/U', $share, $f);

                                        $share = preg_replace('/http:\/\/www\.pinterest\.com\/pin\/create\/button\/\?url=(.*);description=(.*);media=(.*)"/',
                                            'https://vk.com/share.php?url=$1&description=$2&image=$3"', $share);
                                        //echo $share;
                                        ?>
                                    </div>
                                    <?php
                                }
                                ?>
                                <?php if (function_exists('breadcrumbs')) breadcrumbs(); ?>
								<h1 class="entry-title"><?php the_title(); ?></h1>
                                <?php
                                if( in_array(array(33,22,34), $cat_all) ){
                                    ?>
                                    <style>
                                        .PRVD_chatWindow{
                                            display: none;
                                        }
                                        .single-top-info{
                                            display: none;
                                        }
                                    </style>
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
                                    <?php
                                }
                                ?>
							</div>
							<?php } ?>

							<?php if ( ( et_has_post_format() && et_has_format_content() ) || ( has_post_thumbnail() && is_post_extra_featured_image_enabled() ) ) { ?>
							<div class="post-thumbnail header">
								<?php
								$score_bar = extra_get_the_post_score_bar();
								$thumb_args = array( 'size' => 'extra-image-single-post' );
								require locate_template( 'post-top-content.php' );
								?>
							</div>
							<?php } ?>

							<?php $post_above_ad = extra_display_ad( 'post_above', false ); ?>
							<?php if ( !empty( $post_above_ad ) ) { ?>
							<div class="et_pb_extra_row etad post_above">
								<?php echo $post_above_ad; ?>
							</div>
							<?php } ?>

							<div class="post-wrap">
							<?php if ( !extra_is_builder_built() ) { ?>
								<div class="post-content entry-content">
									<?php the_content(); ?>
									<?php
										wp_link_pages( array(
											'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'extra' ),
											'after'  => '</div>',
										) );
									?>
								</div>
							<?php } else { ?>
								<?php et_builder_set_post_type(); ?>
								<?php the_content(); ?>
							<?php } ?>
							</div>
							<?php if ( $review = extra_post_review() ) { ?>
							<div class="post-wrap post-wrap-review">
								<div class="review">
									<div class="review-title">
										<h3><?php echo esc_html( $review['title'] ); ?></h3>
									</div>
									<div class="review-content">
										<div class="review-summary clearfix">
											<div class="review-summary-score-box" style="background-color:<?php echo esc_attr( $post_category_color ); ?>">
												<h4><?php printf( et_get_safe_localization( __( '%d%%', 'extra' ) ), absint( $review['score'] ) ); ?></h4>
											</div>
											<div class="review-summary-content">
												<?php if ( !empty( $review['summary'] ) ) { ?>
												<p>
													<?php if ( !empty( $review['summary_title'] ) ) { ?>
														<strong><?php echo esc_html( $review['summary_title'] ); ?></strong>
													<?php } ?>
													<?php echo $review['summary']; ?>
												</p>
												<?php } ?>
											</div>
										</div>
										<div class="review-breakdowns">
											<?php foreach ( $review['breakdowns'] as $breakdown ) { ?>
											<div class="review-breakdown">
												<h5 class="review-breakdown-title"><?php echo esc_html( $breakdown['title'] ); ?></h5>
												<div class="score-bar-bg">
													<span class="score-bar" style="background-color:<?php echo esc_attr( $post_category_color ); ?>; width:<?php printf( '%d%%', max( 4, absint( $breakdown['rating'] ) ) );  ?>">
														<span class="score-text"><?php printf( et_get_safe_localization( __( '%d%%', 'extra' ) ), absint( $breakdown['rating'] ) ); ?></span>
													</span>
												</div>
											</div>
											<?php } ?>
										</div>
									</div>
								</div>
							</div>
							<?php } ?>

								<style type="text/css" id="rating-stars">
									.post-footer .rating-stars #rated-stars img.star-on,
									.post-footer .rating-stars #rating-stars img.star-on {
										background-color: <?php echo esc_html( $post_category_color ); ?>;
									}
								</style>

                            <div align="center" class="rating-stars-down">
                                <div class="post-meta">
                                    <p><?php echo extra_display_single_post_meta(); ?></p>
                                </div>
                            </div>

							<?php $post_below_ad = extra_display_ad( 'post_below', false ); ?>
							<?php if ( !empty( $post_below_ad ) ) { ?>
							<div class="et_pb_extra_row etad post_below">
								<?php echo $post_below_ad; ?>
							</div>
							<?php } ?>
						</article>

						<nav class="post-nav">
							<div class="nav-links clearfix">
								<div class="nav-link nav-link-prev">
									<?php previous_post_link( '%link', et_get_safe_localization( __( '<span class="button">Previous</span><span class="title">%title</span>', 'extra' ) ) ); ?>
								</div>
								<div class="nav-link nav-link-next">
									<?php next_post_link( '%link', et_get_safe_localization( __( '<span class="button">Next</span><span class="title">%title</span>', 'extra' ) ) ); ?>
								</div>
							</div>
						</nav>
						<?php
						if ( extra_is_post_author_box() ) { ?>
						<div class="et_extra_other_module author-box vcard">
							<div class="author-box-header">
								<h3><?php esc_html_e( 'About The Author', 'extra' ); ?></h3>
							</div>
							<div class="author-box-content clearfix">
								<div class="author-box-avatar">
									<?php echo get_avatar( get_the_author_meta( 'user_email' ), 170, 'mystery', esc_attr( get_the_author() ) ); ?>
								</div>
								<div class="author-box-description">
									<h4><a class="author-link url fn" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author" title="<?php printf( et_get_safe_localization( __( 'View all posts by %s', 'extra' ) ), get_the_author() ); ?>"><?php echo get_the_author(); ?></a></h4>
									<p class="note"><?php the_author_meta( 'description' ); ?></p>
									<ul class="social-icons">
										<?php foreach ( extra_get_author_contact_methods() as $method ) { ?>
											<li><a href="<?php echo esc_url( $method['url'] ); ?>" target="_blank"><span class="et-extra-icon et-extra-icon-<?php echo esc_attr( $method['slug'] ); ?> et-extra-icon-color-hover"></span></a></li>
										<?php } ?>
									</ul>
								</div>
							</div>
						</div>
						<?php } ?>

						<?php
/*						$related_posts = extra_get_post_related_posts();

						if ( $related_posts && extra_is_post_related_posts() ) {  */?><!--
						<div class="et_extra_other_module related-posts">
							<div class="related-posts-header">
								<h3><?php /*esc_html_e( 'Related Posts', 'extra' ); */?></h3>
							</div>
							<div class="related-posts-content clearfix">
								<?php /*while ( $related_posts->have_posts() ) : $related_posts->the_post(); */?>
								<div class="related-post">
									<div class="featured-image"><?php
/*									echo et_extra_get_post_thumb( array(
										'size'                       => 'extra-image-small',
										'a_class'                    => array('post-thumbnail'),
										'post_format_thumb_fallback' => true,
										'img_after'                  => '<span class="et_pb_extra_overlay"></span>',
									));
									*/?></div>
									<h4 class="title"><a href="<?php /*the_permalink(); */?>"><?php /*the_title(); */?></a></h4>
									<p class="date"><?php /*extra_the_post_date(); */?></p>
								</div>
								<?php /*endwhile; */?>
								<?php /*wp_reset_postdata(); */?>
							</div>
						</div>
						--><?php /*} */?>
				<?php
					endwhile;
				else :
					?>
					<h2><?php esc_html_e( 'Post not found', 'extra' ); ?></h2>
					<?php
				endif;
				wp_reset_query();

				do_action( 'et_after_post' );
				?>

				<?php do_shortcode("[rBlock name=after_post]"); ?>
                <?php
                if( !in_array(array(33,22,34), $cat_all) ) {
                    echo get_option('author_expert');
                    do_shortcode("[rBlock name=after_post2]");
                }
                ?>

				<?php
				if ( comments_open() && 'on' == et_get_option( 'extra_show_postcomments', 'on' ) && ! $et_pb_has_comments_module ) {
					comments_template( '', true );
				}
				?>
				<?php do_shortcode("[rBlock name=after_comment]"); ?>
			</div><!-- /.et_pb_extra_column.et_pb_extra_column_main -->

			<?php get_sidebar(); ?>

		</div> <!-- #content-area -->
	</div> <!-- .container -->
</div> <!-- #main-content -->

<?php get_footer();
