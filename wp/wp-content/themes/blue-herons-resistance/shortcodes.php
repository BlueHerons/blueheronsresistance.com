<?php
function auth_redirect_url_shortcode( $atts, $content = null) {
	$redirect = ( strpos( $_SERVER['REQUEST_URI'], '/options.php' ) && wp_get_referer() ) ? wp_get_referer() : set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
 
	$login_url = wp_login_url($redirect, true);
	return $login_url;
}
add_shortcode( 'auth_redirect_url', 'auth_redirect_url_shortcode' );

function if_user_logged_in_shortcode( $atts , $content = null ) {
	if (is_user_logged_in()) {
		return do_shortcode($content);
	}
	else {
		return "";
	}
}
add_shortcode( 'if_user_logged_in', 'if_user_logged_in_shortcode' );

function if_user_not_logged_in_shortcode( $atts , $content = null ) {
	if (!is_user_logged_in()) {
		return do_shortcode($content);
	}
	else {
		return "";
	}
}
add_shortcode( 'if_user_not_logged_in', 'if_user_not_logged_in_shortcode' );

function farm_info_shortcode( $atts, $content = null ) {
	$html = "<div id=\"farm-info\">";
	$html .= "<span class=\"title\">Farm Info</span>";
	$html .= do_shortcode($content);
	$html .= "</div>";
	return $html;
}
add_shortcode( "farm_info", "farm_info_shortcode" );
?>
