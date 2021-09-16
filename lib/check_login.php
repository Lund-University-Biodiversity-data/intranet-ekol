<?php
$okConWordPress=false;

if (ENVIRONMENT=="PROD") {
	/*echo "require";
	//require "/usr/share/wordpress/wp-load.php";
	echo "OK";

	global $current_user;
	$current_user = wp_get_current_user();
	echo "<pre>";
	var_dump( $current_user );
	echo "</pre>";*/
	
	if (file_exists(PATH_WORDPRESS_LOGIN)) {
		require PATH_WORDPRESS_LOGIN;

		if ( is_user_logged_in() ) {
			global $current_user;
			get_currentuserinfo();
			echo '<p><small>Hi ' . $current_user->display_name.'</small></p>';
			$okConWordPress=true;

		} else {
			echo 'Please LOG IN first on <a href="'.URL_WEBSITE.'intranet/">the intranet</a>';;
		}
	}
	else {
		echo "Can't check Wordpress login";
	}
}
else {
	$okConWordPress=true;
}


?>