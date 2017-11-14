<div class="module post-module et_pb_extra_module <?php echo esc_attr( $module_class ); ?>" style="border-top-color:<?php echo esc_attr( $border_top_color ); ?>">
	<div class="module-head">
		<h2 style="color:<?php echo esc_attr( $module_title_color ); ?>"><?php echo esc_html( $title ); ?></h2>
		<span class="module-filter"><?php echo esc_html( $sub_title ); ?></span>
	</div>
	<?php require locate_template( 'module-posts-content.php' ); ?>
</div>
