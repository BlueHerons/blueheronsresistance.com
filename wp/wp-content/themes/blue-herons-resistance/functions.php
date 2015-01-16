<?php
define('BHR_LOGIN_KEY', "");

wp_enqueue_style("bhr", get_stylesheet_directory_uri() . "/blue-herons-resistance.less", array(), time(), "");

function bhr_site_banner() {
	if (function_exists('adrotate_group')) {
		echo adrotate_group(1); // Meetups
	}
}

add_action("responsive_wrapper", "bhr_site_banner");

function bhr_bgmp_shortcode_init() {
	global $post;
    
	if ($post)
		add_filter( 'bgmp_map-shortcode-called', '__return_true' );
}

add_action('wp', 'bhr_bgmp_shortcode_init');

/**
 * Returns the array of forum roles for the site.
 *
 * "Owner" is an Administrator, with no restrictions. "Moderator" is self explanatory. "Agent" is just a regular user. "No Access" is blocked from seeing anything on the forum.
 *
 * @return array Array of forum roles.
 */ 
function bhr_forum_role_names() {
	return array(
		// Keymaster
		bbp_get_keymaster_role() => array(
			'name'         => 'Owner',
			'capabilities' => bbp_get_caps_for_role( bbp_get_keymaster_role() )
		),
		// Moderator
		bbp_get_moderator_role() => array(
			'name'         => 'Moderator',
			'capabilities' => bbp_get_caps_for_role( bbp_get_moderator_role() )
		),
		// Participant
		bbp_get_participant_role() => array(
			'name'         => 'Agent',
			'capabilities' => bbp_get_caps_for_role( bbp_get_participant_role() )
		),
		// Spectator
		//bbp_get_spectator_role() => array(
		//	'name'         => 'My Custom Spectator Role Name',
		//	'capabilities' => bbp_get_caps_for_role( bbp_get_spectator_role() )
		//),
		// Blocked
		bbp_get_blocked_role() => array(
			'name'         => 'No Access',
			'capabilities' => bbp_get_caps_for_role( bbp_get_blocked_role() )
		)
	);
}
add_filter( 'bbp_get_dynamic_roles', 'bhr_forum_role_names' );

function bhr_get_loaded_template_files() {
	$included_files = get_included_files();
	$stylesheet_dir = str_replace( '\\', '/', get_stylesheet_directory() );
	$template_dir   = str_replace( '\\', '/', get_template_directory() );

	foreach ( $included_files as $key => $path ) {
		$path   = str_replace( '\\', '/', $path );
		if ( false === strpos( $path, $stylesheet_dir ) && false === strpos( $path, $template_dir ) ) {
			unset( $included_files[$key] );
		}
	}

	return $included_files;
}

function bhr_is_template_file_loaded($filename) {
	$files = bhr_get_loaded_template_files();
	foreach ($files as $file) {
		if (strpos($file, $filename) !== false)
			return true;
	}
	
	return false;
}

function bhr_wp_nav_menu($menu, $args = array()) {
	if (bhr_is_template_file_loaded("full-page.php")) {
		$menu = "";
	}
	return $menu;
}

add_filter('wp_nav_menu_args', 'bhr_wp_nav_menu');

function bhr_get_event_attendees($event, $rsvp = 'all') {
	$attendees = array();
	if (count($event->get_bookings()) > 0 ) {
		foreach ($event->get_bookings() as $booking) {
			if ($rsvp == "all") {
				$attendees[] = $booking->get_person();
			}
			else if ($rsvp == "yes" && $booking->booking_status == 1) {
				$attendees[] = $booking->get_person();
			}
			else if ($rsvp == "maybe" && $booking->booking_status == 0) {
				$attendees[] = $booking->get_person();
			}
			else if ($rsvp == "no" && $booking->booking_status == 2) {
				$attendees[] = $booking->get_person();
			}
		}
	}
	return $attendees;
}

function bhr_get_booking_for_user($event, $user_id = 0) {
	foreach ($event->get_bookings() as $booking) {
		if ($booking->person->data->ID == $user_id) {
			return $booking;
		}
	}
	return null;
}

?>
