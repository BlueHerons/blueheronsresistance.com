<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

require("vendor/autoload.php");

/**
Template Name: Count Up Timer
 */
define ("GM_TOKEN", "");

$all = unserialize(get_post_meta($wp_query->post->ID, "all_resets", true));

if (isset($_REQUEST['user']) && isset($_REQUEST['reset']) &&
    is_numeric($_REQUEST['user']) && is_numeric($_REQUEST['reset'])) {

    $last = intval(get_post_meta($wp_query->post->ID, "last_reset", true));
    $now = intval($_REQUEST['reset']);

    print_r($_REQUEST);

    echo $last . " < " . $now;

    // Is this reset time after the last reset time?
    if ($last < $now) {
        $p = array("user"     => $_REQUEST['user'],
                   "duration" => $_REQUEST['duration'],
                   "reset"    => $_REQUEST['reset']);
        $all[] = $p;

        // POST message to GM API using token + gm_group_id
        $gm = new GroupMePHP\groupme(GM_TOKEN);
        $room = get_post_meta($wp_query->post->ID, "groupme_group_id", true);
        $message = sprintf("%s smashed 35th! Time since last smash was %s", get_userdata($_REQUEST['user'])->user_login, $_REQUEST['duration']);
        $gm->messages->create($room, array(
            uniqid(),
            $message
        ));

        update_post_meta($wp_query->post->ID, "all_resets", serialize($all));
        update_post_meta($wp_query->post->ID, "last_reset", $_REQUEST['reset']);
    }
}

$start = get_post_meta($wp_query->post->ID, "last_reset", true);
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
                                    <?php if (is_user_logged_in()) { ?>
                                    <?php if (current_user_can("is_verified")) { ?>
                                    <form id="reset" method="post">
                                        <input type="hidden" name="user" value="<?php echo wp_get_current_user()->ID; ?>" />
                                        <input type="hidden" name="reset" />
                                        <input type="hidden" name="duration" />
                                        <input type="submit" value="I smashed 35th! Reset the timer!" />
                                    </form>
                                    <?php } else { ?>
                                    <p style="text-align: center" class="restricted">You have logged in, however You must be verified before you can reset the timer.</p>
                                    <?php } ?>
                                    <?php } else { ?>
                                    <p style="text-align: center" class="restricted">You must <a href="<?php echo wp_login_url(get_permalink());?>">log in</a> to reset the timer.</p>
                                    <?php } ?>

                                    <?php
                                        $smashers = array();
                                        $x = array();
                                        $sumSmash = 0;
                                    ?>
                                    <div class="past">
                                        <h3>Time Between Smashes</h3>
                                        <ul><?php
                                        usort($all, function($a, $b) {
                                            return strcmp($b['reset'], $a['reset']);
                                        });
                                        foreach ($all as $reset) {
                                            // calculate in seconds
                                            $t = explode(":", $reset['duration']);
                                            $sumSmash += (intval($t[0]) * 60 * 60) + (intval($t[1]) * 60) + (intval($t[2]));
                                            // get smasher counts
                                            $smashers[get_userdata($reset['user'])->user_login]++;
                                            ?>
                                            <li><span class="duration"><?php echo $reset['duration'];?></span> <span class="details">(<span class="who"><?php echo get_userdata($reset['user'])->user_login; ?></span> on <span class="when"><?php echo get_date_from_gmt( date( 'Y-m-d H:i:s', $reset['reset']), 'F j \a\t H:i' );?></span>)</span></li>
<?php
                                        }
?>
                                        </ul>
                                    </div>
                                    <div class="avg">
                                        <h3>Average Time Between Smashes</h3>
                                        <div class="time"><?php
                                            $avg = intval($sumSmash / (count($all) - 1));
                                            $h = intval($avg / 3600);
                                            $m = intval(($avg - ($h * 3600)) / 60);
                                            $s = intval(($avg - ($h * 3600) - ($m * 60)));
                                            echo sprintf('%1$02d:%2$02d:%3$02d', $h, $m, $s);
                                        ?></div>
                                    </div>
                                    <div class="top">
                                        <?php arsort($smashers);  ?>
                                        <h3>Top Smashers</h3>
                                            <ul>
                                            <?php foreach ($smashers as $smasher => $count) { ?>
                                                <li><span class="details"><?php echo $count;?> - </span><span class="who"><?php echo $smasher ?></span>
                                            <?php } ?>
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
