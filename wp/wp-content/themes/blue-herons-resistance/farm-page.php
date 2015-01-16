<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sidebar/Content Template
 *
Template Name:  BHR Farm Page
 */
?>
<?php get_header(); ?>

<?php bhr_site_banner(); ?>

<div id="content" class="grid col-940">

	<?php if ( have_posts() ) : ?>

		<?php while( have_posts() ) : the_post(); ?>
			<?php $custom_fields = get_post_meta($post->ID); ?>
			<?php $placemark = get_post_meta($custom_fields['bhr_placemark_id'][0]); ?>

			<?php responsive_entry_before(); ?>
			<div id="post-<?php the_ID(); ?>" <?php post_class("farm farm-page"); ?>>
				<?php responsive_entry_top(); ?>

				<h1 class="post-title"><?php the_title(); ?></h1>
				<div class="post-edit"><?php edit_post_link( __( '[Edit]', 'responsive' ) ); ?></div>

				<div class="post-entry">
					<ul class="quick-info">
						<?php if(isset($custom_fields['bhr_farm_portal_count'])) { ?>
						<li><span class="title">Number of portals:</span> <?php echo $custom_fields['bhr_farm_portal_count'][0]; ?></li>
						<li><span class="title">Typical lap:</span> <?php echo $custom_fields['bhr_farm_lap_time'][0]; ?> minutes</li>
						<li><span class="title">Standing farm?:</span> <?php echo ($custom_fields['bhr_farm_standing'][0] == "true") ? "Yes" : "No"; ?></li>
						<li><span class="title">Walking farm?:</span> <?php echo ($custom_fields['bhr_farm_type'][0] == "walk") ? "Yes" : "No"; ?></li>
						<?php } ?>
						
						<li><a href="//ingress.com/intel?ll=<?php echo $placemark['bgmp_address'][0] . "," . $placemark['bgmp_address'][1];?>" target="_blank">Intel Map Link</a></li>

						<li><a href="//maps.google.com/?q=<?php echo $placemark['bgmp_address'][0] . "," . $placemark['bgmp_address'][1];?>" target="_blank">Google Maps Link</a></li>
					</ul>
					
					<?php the_content( __( 'Read more &#8250;', 'responsive' ) ); ?>
					
					<?php echo do_shortcode("[bgmp-map placemark=\"" . $custom_fields['bhr_placemark_id'][0] . "\" width=\"100%\" zoom=\"16\"]"); ?>

					<?php wp_link_pages( array( 'before' => '<div class="pagination">' . __( 'Pages:', 'responsive' ), 'after' => '</div>' ) ); ?>
					<?php printf( __("Last edited by %s on %s at %s", "bhr"), get_the_modified_author(), get_the_modified_date(), get_the_modified_time()); ?>				</div>
				<!-- end of .post-entry -->

				<div class="post-edit"><?php edit_post_link( __( 'Edit', 'responsive' ) ); ?></div>

				<?php responsive_entry_bottom(); ?>
			</div><!-- end of #post-<?php the_ID(); ?> -->
			<?php responsive_entry_after(); ?>

		<?php
		endwhile;

		get_template_part( 'loop-nav', get_post_type() );

	else :

		get_template_part( 'loop-no-posts', get_post_type() );

	endif;
	?>

</div><!-- end of #content -->

<?php get_footer(); ?>
