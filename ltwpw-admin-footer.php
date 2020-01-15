<?php

/**
 * LearnTheWPWay Admin Footer
 *
 * @package           LTWPW_Admin_Footer
 * @author            Rich Tape
 * @copyright         2020 Rich Tape
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       LearnTheWPWay Admin Footer
 * Plugin URI:        https://richardtape.com/ltwpw-admin-footer
 * Description:       Adjust the footer in the WordPress dashboard to show when the next submission deadline is.
 * Version:           0.1.0
 * Requires at least: 5.2
 * Requires PHP:      5.6
 * Author:            Rich Tape
 * Author URI:        https://richardtape.com
 * Text Domain:       ltwpw-admin-footer
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

add_filter( 'admin_footer_text', 'ltwpw_change_admin_footer_text' );

/**
 * Change the admin footer text to let newspaper reporters know when their deadline is.
 * Only change this message for newspaper reporters which are 'author' role.
 *
 * @param string $text The content that will be printed.
 * @return string $text Text saying the deadline is Friday at 3pm.
 */
function ltwpw_change_admin_footer_text( $text ) {

	// If they are NOT an author, just show them the default admin message.
	if ( ! ltwpw_user_logged_in_is_author() ) {
		return $text;
	}

	return __( '<span id="footer-thankyou">Deadline is Friday at 3pm!</span>', 'ltwpw-admin-footer' );

}//end ltwpw_change_admin_footer_text()


add_filter( 'update_footer', 'ltwpw_change_update_footer', 11 );

/**
 * Update the text in the admin footer which normally displays the WordPress version number.
 *
 * @param string $content The content that will be printed.
 * @return string Text to output where the version number is normally.
 */
function ltwpw_change_update_footer( $content ) {

	// If they are NOT an author, just show them the version number.
	if ( ! ltwpw_user_logged_in_is_author() ) {
		return $content;
	}

	$hours = ltwpw_number_of_hours_until_friday_at_three_pm();

	$text = sprintf(
		/* translators: %d: Number of whole hours until deadline */
		__( 'You have %d hour(s) until deadline.', 'ltwpw-admin-footer' ),
		absint( $hours )
	);

	return $text;

}//end ltwpw_change_update_footer()

/**
 * Determine if the currently logged in user has the author role.
 *
 * @return bool True if the currently logged in user has an author role. False otherwise.
 */
function ltwpw_user_logged_in_is_author() {

	// Get the current logged in user and check if they are an author.
	$user = wp_get_current_user();

	// Ensure that we have a WP_User object so that we can assume it has a roles property.
	if ( ! is_a( $user, 'WP_User' ) ) {
		return false;
	}

	// If they are NOT an author, just show them the default admin message.
	if ( ! in_array( 'author', (array) $user->roles, true ) ) {
		return false;
	}

	return true;

}//end ltwpw_user_logged_in_is_author()

/**
 * Calculate the number of hours until Friday at 3pm.
 *
 * @return int Number of whole hours until Friday at 3pm.
 */
function ltwpw_number_of_hours_until_friday_at_three_pm() {

	$timestamp_now      = strtotime( 'now' );
	$timestamp_deadline = strtotime( 'This friday + 15 hours' );

	// How many hours between now and the deadline.
	$num_hours_until_deadline = ( $timestamp_deadline - $timestamp_now ) / 60 / 60;

	// Round to nearest hour.
	$usable_hours = round( $num_hours_until_deadline );

	return absint( $usable_hours );

}//end ltwpw_number_of_hours_until_friday_at_three_pm()
