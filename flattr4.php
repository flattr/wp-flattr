<?php

add_action( 'admin_notices','flattrAdminNotice' );

function flattrAdminNotice() {
	echo '<div id="message" class="error">';
		echo '<p><strong>Warning:</strong> The Flattr plugin requires PHP5. You are currently using '. PHP_VERSION .'</p>';
	echo '</div>';
}

/**
 * returns the Flattr button
 * Use this from your template
 */
function get_the_flattr_permalink()
{
	return '';
}

/**
 * prints the Flattr button
 * Use this from your template
 */
function the_flattr_permalink()
{
}
