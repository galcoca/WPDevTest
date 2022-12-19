<?php

/*--------------------------------------------------*/
/*          BEGIN PART 2 STYLESHEET ENQUEUE         */

/* FUNCTIONS */

function parentStyles() 
{
	$parenthandle = 'parent-style';
	$theme        = wp_get_theme();
	wp_enqueue_style( $parenthandle,
		get_template_directory_uri() . '/style.css',
		$theme->parent()->get( 'Version' )
	);
	wp_enqueue_style( 'child-style',
		get_stylesheet_uri(),
		array( $parenthandle ),
		$theme->get( 'Version' )
	);
}

/* ACTIONS */

add_action( 'wp_enqueue_scripts', 'parentStyles' );

/*           END PART 2 STYLESHEET ENQUEUE          */
/*--------------------------------------------------*/

/*--------------------------------------------------*/
/*            BEGIN PART 3 USER CREATION            */

/* FUNCTIONS */

function userCreationFunction() {
    $username = 'wp-test';
    $email = 'gabriel.ctest@elementor.com';
    //$email = 'wptest@elementor.com';
    $password = '123456789';

    $user_id = username_exists( $username );
    if (!$user_id && email_exists($email) == false) {
        $user_id = wp_create_user( $username, $password, $email );
        if( !is_wp_error($user_id) ) {
            $user = get_user_by( 'id', $user_id );
            $user->set_role( 'editor' );
        }
    }
}

function adminBarDisableFor() {
    if (is_user_logged_in()) :
        $user = wp_get_current_user();
        $response = false;
        if ($user->user_login == 'wp-test') :
            $response = true;        
        endif;
        return $response;
    endif;
}

if ( adminBarDisableFor() ) :
    add_filter('show_admin_bar', '__return_false');
endif;

/* ACTIONS */
add_action('init', 'userCreationFunction');

/*             END PART 3 USER CREATION             */
/*--------------------------------------------------*/


?>
