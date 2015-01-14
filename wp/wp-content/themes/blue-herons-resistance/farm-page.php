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

<div id="content" class="grid col-620 fit">

	<?php if ( have_posts() ) : ?>

		<?php while( have_posts() ) : the_post(); ?>

			<?php get_responsive_breadcrumb_lists(); ?>

			<?php responsive_entry_before(); ?>
			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<?php responsive_entry_top(); ?>

				<h1 class="post-title"><?php the_title(); ?></h1>

				<?php if ( comments_open() ) : ?>
					<div class="post-meta">
						<?php responsive_post_meta_data(); ?>

						<?php if ( comments_open() ) : ?>
							<span class="comments-link">
                        <span class="mdash">&mdash;</span>
								<?php comments_popup_link( __( 'No Comments &darr;', 'responsive' ), __( '1 Comment &darr;', 'responsive' ), __( '% Comments &darr;', 'responsive' ) ); ?>
                        </span>
						<?php endif; ?>
					</div><!-- end of .post-meta -->
				<?php endif; ?>

				<div class="post-entry">
					<?php the_content( __( 'Read more &#8250;', 'responsive' ) ); ?>
					<?php wp_link_pages( array( 'before' => '<div class="pagination">' . __( 'Pages:', 'responsive' ), 'after' => '</div>' ) ); ?>
					<?php printf( __("Last edited by %s on %s at %s", "bhr"), get_the_modified_author(), get_the_modified_date(), get_the_modified_time()); ?>				</div>
				<!-- end of .post-entry -->

				<?php if ( comments_open() ) : ?>
					<div class="post-data">
						<?php the_tags( __( 'Tagged with:', 'responsive' ) . ' ', ', ', '<br />' ); ?>
						<?php the_category( __( 'Posted in %s', 'responsive' ) . ', ' ); ?>
					</div><!-- end of .post-data -->
				<?php endif; ?>

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

<?php get_sidebar(); ?>
<?php get_footer(); ?>
