<?php

//*** Enqueue script and styles for child theme
function child_enqueue_styles() {
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri().'/style.css' );
}
add_action( 'wp_enqueue_scripts', 'child_enqueue_styles' );

//*** Add includes
require_once get_stylesheet_directory().'/lib/inc/custom-posts.php';
require_once get_stylesheet_directory().'/lib/inc/gravityforms.php';
// require_once get_stylesheet_directory().'/lib/inc/mailster.php';
require_once get_stylesheet_directory().'/lib/inc/marketplace.php';
require_once get_stylesheet_directory().'/lib/inc/woocommerce.php';

/*** Hide admin bar - change to shop manager
add_action('after_setup_theme', 'remove_admin_bar');
function remove_admin_bar() {
  if( !is_admin() ) :
    show_admin_bar( false );
  endif;
} */

add_filter( 'body_class', 'custom_body_class' );
function custom_body_class( $classes ) {

	$user = wp_get_current_user();
	$roles = ( array ) $user->roles;

  if( $roles) :
	  $classes[] = implode(', ', $user->roles);
  endif;

  global $post;
	$post_id = $post->ID;
  $coming_soon = ( get_field('coming_soon', $post_id) );
  $guest_author = ( get_field('guest_author', $post_id) );
  $url_query = $_GET['download'];
  $url_query_sanitized = htmlspecialchars( $url_query );

	if( $coming_soon ) :
  		$classes[] = 'product-coming-soon';
	endif;

  if( $guest_author ) :
    $classes[] = 'guest-author';
  endif;

  if( $url_query_sanitized ) :
    $classes[] = 'download-thanks';
  endif;

	return $classes;
}

/*** Custom Login Screen */
add_filter(  'gettext',  'register_text'  );
function register_text( $translating ) {
    $translated = str_ireplace(  'Username or Email Address',  'Email Address',  $translating );
    return $translated;
}

function login_logo_url() {
	return get_bloginfo( 'url' );
}
add_filter( 'login_headerurl', 'login_logo_url' );

function login_logo_url_title() {
	return 'Greener Guest. The Sustainable Choice for Hotel Supplies';
}
add_filter( 'login_headertitle', 'login_logo_url_title' );

function custom_login_stylesheet() {
  wp_enqueue_style( 'custom-login', get_stylesheet_directory_uri() . '/lib/css/login-style.css' );
}
add_action( 'login_enqueue_scripts', 'custom_login_stylesheet' );

//*** Custom login process
function custom_register_url( $register_url ) {
  $register_url = get_permalink( $register_page_id = 27739 );
  return $register_url;
}
add_filter( 'register_url', 'custom_register_url' );

/* Login Redirct  THIS IS STOPPING EVERYTHING LOAD AFTER IT
function login_redirect_by_role( $redirect, $user ){
  $user = wp_get_current_user();
  $roles = ( array ) $user->roles;

  if ( in_array( array( 'wk_marketplace_seller' ), $user_roles ) ) :
		$redirect = get_home_url('seller');
	else :
    $redirect = get_home_url();
	endif;
}
return $redirect;
add_filter( 'woocommerce_login_redirect', 'login_redirect_by_role', 10, 2 ); */

//*** Register Sidebars
function widget_sidebar() {
  register_sidebar( array(
    'name'          => 'Product Filters',
    'id'            => 'product-filters-sidebar',
    'description'   => '',
    'class'         => '',
    'before_widget' => '<li id="%1$s" class="widget %2$s">',
    'after_widget'  => '</li>',
    'before_title'  => '<h2 class="widgettitle">',
    'after_title'   => '</h2>'
  ) );
    register_sidebar( array(
      'name'          => 'Language Switcher',
      'id'            => 'language-switcher-sidebar',
      'description'   => '',
      'class'         => '',
      'before_widget' => '',
      'after_widget'  => '',
      'before_title'  => '',
      'after_title'   => ''
    ) );
}
add_action( 'widgets_init', 'widget_sidebar' );

function resource_query( $query ) {
    $query->set( 'post_type', [ 'custom-post-type1', 'custom-post-type2' ] );
}
add_action( 'elementor/query/multiple_cpts', 'resource_query' );

//*** Adding extra user meta data
function gg_extra_user_profile_fields( $user ) { ?>
  <h2><?php _e("Marketing Information", "blank"); ?></h2>

  <table class="form-table">
    <tr>
      <th><label for="source"><?php _e("Sign Up Source"); ?></label></th>
      <td>
        <input type="text" name="source" id="source" value="<?php echo esc_attr( get_the_author_meta( 'source', $user->ID ) ); ?>" class="regular-text" placeholder="referer url" />
        <br />
      </td>
    </tr>
    <tr>
      <th><label for="sector"><?php _e("Sector"); ?></label></th>
      <td>
        <input type="text" name="sector" id="sector" value="<?php echo esc_attr( get_the_author_meta( 'sector', $user->ID ) ); ?>" class="regular-text" placeholder="sector" />
        <input type="text" name="sector-other" id="sector-other" value="<?php echo esc_attr( get_the_author_meta( 'sector-other', $user->ID ) ); ?>" class="regular-text" placeholder="specify sector" />
        <br />
      </td>
    </tr>
    <tr>
      <th><label for="accommodation-type"><?php _e("Accommodation Type"); ?></label></th>
      <td>
        <input type="text" name="accommodation-type" id="accommodation-type" value="<?php echo esc_attr( get_the_author_meta( 'accommodation-type', $user->ID ) ); ?>" class="regular-text" placeholder="accommodation type" />
        <input type="text" name="accommodation-type-other" id="accommodation-type-other" value="<?php echo esc_attr( get_the_author_meta( 'accommodation-type-other', $user->ID ) ); ?>" class="regular-text" placeholder="specify accommodation type" />
        <br />
      </td>
    </tr>
    <tr>
      <th><label for="job-role"><?php _e("Job Role"); ?></label></th>
      <td>
        <input type="text" name="job-role" id="job-role" value="<?php echo esc_attr( get_the_author_meta( 'job-role', $user->ID ) ); ?>" class="regular-text" placeholder="job role" />
        <input type="text" name="job-role-other" id="job-role-other" value="<?php echo esc_attr( get_the_author_meta( 'job-role-other', $user->ID ) ); ?>" class="regular-text" placeholder="specify job role" />
        <br />
      </td>
    </tr>
    <tr>
      <th><label for="company-name"><?php _e("Company Name"); ?></label></th>
      <td>
        <input type="text" name="company-name" id="company-name" value="<?php echo esc_attr( get_the_author_meta( 'company-name', $user->ID ) ); ?>" class="regular-text" placeholder="company name" />
        <br />
      </td>
    </tr>
    <tr>
      <th><label for="company-size"><?php _e("Company Size"); ?></label></th>
      <td>
        <input type="text" name="company-size" id="company-size" value="<?php echo esc_attr( get_the_author_meta( 'company-size', $user->ID ) ); ?>" class="regular-text" placeholder="company size" />
        <br />
      </td>
    </tr>
  </table> <?php
}
add_action( 'show_user_profile', 'gg_extra_user_profile_fields' );
add_action( 'edit_user_profile', 'gg_extra_user_profile_fields' );

function gg_save_extra_user_profile_fields( $user_id ) {
    if ( !current_user_can( 'edit_user', $user_id ) ) {
        return false;
    }
    update_user_meta( $user_id, 'source', $_POST['source'] );
    update_user_meta( $user_id, 'sector', $_POST['sector'] );
    update_user_meta( $user_id, 'sector-other', $_POST['sector-other'] );
    update_user_meta( $user_id, 'accommodation-type', $_POST['accommodation-type'] );
    update_user_meta( $user_id, 'accommodation-type-other', $_POST['accommodation-type-other'] );
    update_user_meta( $user_id, 'job-role', $_POST['job-role'] );
    update_user_meta( $user_id, 'job-role-other', $_POST['job-role-other'] );
    update_user_meta( $user_id, 'company-name', $_POST['company-name'] );
    update_user_meta( $user_id, 'company-size', $_POST['company-size'] );
}
add_action( 'personal_options_update', 'gg_save_extra_user_profile_fields' );
add_action( 'edit_user_profile_update', 'gg_save_extra_user_profile_fields' );

add_filter('mod_rewrite_rules', 'fix_rewritebase');
function fix_rewritebase($rules){
    $home_root = parse_url(home_url());
    if ( isset( $home_root['path'] ) ) {
        $home_root = trailingslashit($home_root['path']);
    } else {
        $home_root = '/';
    }
 
    $wpml_root = parse_url(get_option('home'));
    if ( isset( $wpml_root['path'] ) ) {
        $wpml_root = trailingslashit($wpml_root['path']);
    } else {
        $wpml_root = '/';
    }
 
    $rules = str_replace("RewriteBase $home_root", "RewriteBase $wpml_root", $rules);
    $rules = str_replace("RewriteRule . $home_root", "RewriteRule . $wpml_root", $rules);
 
    return $rules;
}
