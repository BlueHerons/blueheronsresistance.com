<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
Template Name: Count Up Timer
 */

$all   = get_option("bhr_35th_all_resests", array(array("user"=>64,"duration" => "A REALLY long time", "reset"=>1436598000)));

if (isset($_REQUEST['user']) && isset($_REQUEST['reset']) &&
    is_numeric($_REQUEST['user']) && is_numeric($_REQUEST['reset'])) {
    
    $p = array("user" => $_REQUEST['user'],
                "duration" => $_REQUEST['duration'],
               "reset" => $_REQUEST['reset']);

    $all[] = $p;

    update_option("bhr_35th_all_resests", $all);
    update_option("bhr_35th_last_reset", $_REQUEST['reset']);
}

$start = get_option("bhr_35th_last_reset", time());
?>
<?php get_header(); ?>
<script>
    var start = new Date(<?php echo ($start * 1000); ?>);

    jQuery(document).ready(function() {
        $ = jQuery;

        function tick() {
            n = new Date();
            d = n.getTime() - start.getTime();
            d = Math.floor(d / 1000);
        
            h = Math.floor(d / 3600);
            m = Math.floor((d - (h * 3600)) / 60);
            s = d - (h * 3600) - (m * 60);

            hours   = h < 10 ? "0" + h : h;
            minutes = m < 10 ? "0" + m : m;
            seconds = s < 10 ? "0" + s : s;
            
            str = hours + ":" + minutes + ":" + seconds;

            $(".post-entry .timer").html(str);
            $("form#reset input[name='reset']").val((start.getTime() / 1000)  + d);
            $("form#reset input[name='duration']").val(str);
        }

        setInterval(tick, 1000);
    });
</script>
<div id="content" class="grid col-940">

	<?php if ( have_posts() ) : ?>

		<?php while( have_posts() ) : the_post(); ?>

			<?php responsive_entry_before(); ?>
			<div id="post-<?php the_ID(); ?>" <?php post_class("timer"); ?>>
				<?php responsive_entry_top(); ?>

				<h1 class="post-title"><?php the_title(); ?></h1>
				<div class="post-edit"><?php edit_post_link( __( '[Edit]', 'responsive' ) ); ?></div>

				<div class="post-entry">
                                    <h2 class="timer"></h2>
                                    <form id="reset" method="post">
                                        <input type="hidden" name="user" value="<?php echo wp_get_current_user()->ID; ?>" />
                                        <input type="hidden" name="reset" />
                                        <input type="hidden" name="duration" />
                                        <input type="submit" value="I smashed 35th! Reset the timer!" />
                                    </form>

                                    <div class="past">
                                        <h3>Previous lifetimes of 35th</h3>
                                        <ul><?php
                                        usort($all, function($a, $b) {
                                            return strcmp($b['reset'], $a['reset']);
                                        });
                                        foreach ($all as $reset) {
                                            ?>
                                            <li><span class="duration"><?php echo $reset['duration'];?></span> <span class="details">(Reset by <span class="who"><?php echo get_userdata($reset['user'])->user_login; ?></span> on <span class="when"><?php echo date("Y-m-d H:i T", $reset['reset']);?></span>)</span></li>
<?php
                                        }
?>
                                        </ul>
                                    </div>
				</div>
				<!-- end of .post-entry -->

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
