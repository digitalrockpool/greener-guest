<?php

//*** Add product revisions
add_filter( 'woocommerce_register_post_type_product', 'modify_product_post_type' );

function modify_product_post_type( $args ) {
     $args['supports'][] = 'revisions';

     return $args;
}

//*** Hide menu items in my account area
add_filter ( 'woocommerce_account_menu_items', 'remove_my_account_menu_items' );
function remove_my_account_menu_items( $menu_links ){

	unset( $menu_links['edit-address'] );

	return $menu_links;

}

//*** Add my courses to my account section
/* Not required as using custom menu

 add_filter ( 'woocommerce_account_menu_items', 'add_custom_menu_item', 40 );
function add_custom_menu_item( $menu_links ){

	$menu_links = array_slice( $menu_links, 0, 2, true )
	+ array( 'courses' => 'Courses' )
	+ array_slice( $menu_links, 2, NULL, true );

	return $menu_links;

} */

add_action( 'init', 'add_courses_endpoint' );
function add_courses_endpoint() {

	add_rewrite_endpoint( 'courses', EP_PAGES );
	add_rewrite_endpoint( 'account-details', EP_PAGES );
	add_rewrite_endpoint( 'manage-preferences', EP_PAGES );

}

add_action( 'woocommerce_account_courses_endpoint', 'courses_endpoint_content' );
function courses_endpoint_content() {

	echo do_shortcode( '[ld_profile]' );

}

add_action( 'woocommerce_account_manage-preferences_endpoint', 'manage-preferences_endpoint_content' );
function manage_preferences_endpoint_content() {

	echo do_shortcode( '[newsletter_signup]Sign up for updates[newsletter_signup_form id=1][/newsletter_signup]' );
	echo do_shortcode( '[newsletter_signup]Signup for the newsletter[newsletter_signup_form id=1][/newsletter_signup]' );
	echo do_shortcode( '[newsletter_signup]Signup for the newsletter[newsletter_signup_form id=1][/newsletter_signup]' );

}

add_action( 'woocommerce_account_account-details_endpoint', 'account_endpoint_content' );
function account_endpoint_content() {

	gravity_form( 4, false, false, false, '', false );

}

/*** Change cart to basket */
add_filter('gettext', function ($translated_text, $text, $domain) {
  if ($domain == 'woocommerce') :
		switch ($translated_text) {
			case 'Cart totals':
				$translated_text = __('Order summary', 'woocommerce');
				break;
			case 'Update cart':
				$translated_text = __('Update basket', 'woocommerce');
			break;
			case 'Add to cart':
				$translated_text = __('Add to basket', 'woocommerce');
			break;
			case 'View cart':
				$translated_text = __('View basket', 'woocommerce');
			break;
		}
	endif;
  return $translated_text;
}, 20, 3);

add_theme_support( 'woocommerce', array(
'thumbnail_image_width' => 200,
'gallery_thumbnail_image_width' => 100,
'single_image_width' => 200,
) );

add_filter( 'woocommerce_get_image_size_gallery_image_size', function( $size ) {
return array(
'width' => 400,
'height' => 100,
'crop' => 0,
);
} );


/*** Add tabs on product pages */
add_filter( 'woocommerce_product_tabs', 'woo_custom_product_tabs' );
function woo_custom_product_tabs( $tabs ) {

  global $post;
	$post_id = $post->ID;

    unset( $tabs['description'] ); // remove description tab
    // unset( $tabs['reviews'] ); // remove the reviews tab
    unset( $tabs['additional_information'] ); // remove the additional information tab


		$tabs['product_specification_tab'] = array(
			'title'     => __( 'Product Specification', 'woocommerce' ),
			'callback'  => 'woo_product_specification_tab_content',
			'priority'  => 5
		);


		$tabs['shipping_returns_tab'] = array(
			'title'     => __( 'Shipping and Returns', 'woocommerce' ),
			'callback'  => 'woo_shipping_returns_tab_content',
			'priority'  => 10
		);

		$tabs['supplier_story_tab'] = array(
			'title'     => __( 'Supplier Story', 'woocommerce' ),
			'callback'  => 'woo_supplier_story_tab_content',
			'priority'  => 15
		);

    return $tabs;

}

add_filter( 'woocommerce_product_tabs', function( $tabs ) { // declared seperatly as different priority
  unset( $tabs['wcfm_policies_tab'] ); // remove store policy tab
  return $tabs;
}, 500 );


function woo_supplier_story_tab_content() {
  global $post, $WCFM, $WCFMmp;

  $store_id = $WCFM->wcfm_vendor_support->wcfm_get_vendor_id_from_product( $post->ID );
  $store_user = wcfmmp_get_store( $store_id );
	$store_info = $store_user->get_shop_info();
	$gravatar = $store_user->get_avatar();
	$banner_type = $store_user->get_list_banner_type();
	if( $banner_type == 'video' ) :
    $banner_video = $store_user->get_list_banner_video();
	else :
		$banner_image = $store_user->get_list_banner();
		if( $banner_image ) :
				$banner = '<img src="'.apply_filters( 'wcfmmp_list_store_default_bannar', $banner_image ).'" alt="supplier image">';
		endif;
	endif;
	$store_name = isset( $store_info['store_name'] ) ? esc_html( $store_info['store_name'] ) : __( 'N/A', 'wc-multivendor-marketplace' );
  $store_name = apply_filters( 'wcfmmp_store_title', $store_name , $store_id );
  $store_description = $store_user->get_shop_description();

  echo '<div class="impact-container"><div class="impact-story"><h3>'.$store_name.'</h3>'.$store_description.'</div><div class="impact-image">'.$banner.'</div></div>';

}

function woo_product_specification_tab_content() {
	global $post, $product;
	$post_id = $post->ID; ?>

	<table class="product-tabs"> <?php

		$quantity_per_pack = wp_strip_all_tags( get_field('quantity_per_pack', $post_id) );
		if( $quantity_per_pack ) : ?>
			<tr>
				<td class="td-title">Items per Pack</td>
				<td><?php	echo $quantity_per_pack; ?>
				</td>
			</tr> <?php
		endif;

		$shelf_life = wp_strip_all_tags( get_field('shelf_life', $post_id) );
		if( $shelf_life ) : ?>
			<tr>
				<td class="td-title">Shelf Life</td>
				<td><?php	echo $shelf_life; ?>
				</td>
			</tr> <?php
		endif;

		$user_guidance_empty = wp_strip_all_tags( get_field('user_guidance', $post_id) );
    $user_guidance = get_field('user_guidance', $post_id);
		if( $user_guidance_empty ) : ?>
			<tr>
				<td class="td-title">User Guidance</td>
				<td><?php	echo $user_guidance; ?>
				</td>
			</tr> <?php
		endif;

		$ink_details_empty = wp_strip_all_tags( get_field('ink_details', $post_id) );
    $ink_details = get_field('ink_details', $post_id);
		if( $ink_details_empty ) : ?>
			<tr>
				<td class="td-title">Ink Details</td>
				<td><?php echo $ink_details; ?>
				</td>
			</tr> <?php
		endif;

		$raw_materials = $product->get_attribute( 'pa_raw-materials' );
		if( $raw_materials ) : ?>

			<tr>
				<td class="td-title">Raw Materials</td>
				<td><?php echo $raw_materials; ?></td>
			</tr> <?php

		endif;

		$ingredients_empty = wp_strip_all_tags( get_field('ingredients', $post_id) );
    $ingredients = get_field('ingredients', $post_id);
		if( $ingredients_empty ) : ?>
			<tr>
				<td class="td-title">Ingredients</td>
				<td><?php echo $ingredients; ?>
				</td>
			</tr> <?php
		endif;

		$nutritional_information_empty = wp_strip_all_tags( get_field('nutritional_information', $post_id) );
    $nutritional_information = get_field('nutritional_information', $post_id);
		if( $nutritional_information_empty ) : ?>
			<tr>
				<td class="td-title">Nutritional Information</td>
				<td><?php echo $nutritional_information; ?>
				</td>
			</tr> <?php
		endif;

		$environmental_impact_empty = wp_strip_all_tags( get_field('environmental_impact', $post_id) );
    $environmental_impact = get_field('environmental_impact', $post_id);
		if( $environmental_impact_empty ) : echo '<tr><td class="td_title">Environmental Impact</td><td>'.$environmental_impact.'</td></tr>'; endif;

		$country_of_origin = $product->get_attribute( 'pa_country-of-origin' );
		if( $country_of_origin ) : ?>

			<tr>
				<td class="td-title">Country of Origin</td>
				<td><?php echo $country_of_origin; ?></td>
			</tr> <?php

		endif;

    $standards_certifications = wc_get_product_terms( $product->id, 'pa_standards-certifications', array( 'fields' => 'all') );
		if( $standards_certifications ) : ?>

			<tr>
				<td class="td-title">Standards and Certifications</td>
				<td> <?php
          foreach( $standards_certifications as $standards_certification ) :
            echo '<p>'.$standards_certification->name.': '.term_description( $standards_certification->term_id ).'</p>';
          endforeach; ?>
        </td>
			</tr> <?php

		endif;

    $packaging_disposal = $product->get_attribute( 'pa_packaging-disposal' );
		$packaging_disposal_description_empty = wp_strip_all_tags( get_field('packaging_disposal_description', $post_id) );
		$packaging_disposal_description = get_field('packaging_disposal_description', $post_id);
		if( $packaging_disposal || $packaging_disposal_description ) : ?>

			<tr>
				<td class="td-title">Packaging Disposal</td>
				<td><?php
          if( $packaging_disposal ) : echo $packaging_disposal.'<br />'; endif;
          if( $packaging_disposal_description ) : echo $packaging_disposal_description.'<br />'; endif; ?>
        </td>
			</tr> <?php

		endif;

    $product_disposal = $product->get_attribute( 'pa_product-disposal' );
		$product_disposal_description_empty = wp_strip_all_tags( get_field('product_disposal_description', $post_id) );
		$product_disposal_description = get_field('product_disposal_description', $post_id);
		if( $product_disposal || $product_disposal_description ) : ?>

			<tr>
				<td class="td-title">Product Disposal</td>
				<td><?php
          if( $product_disposal ) : echo $product_disposal.'<br />'; endif;
          if( $product_disposal_description ) : echo $product_disposal_description.'<br />'; endif; ?>
        </td>
			</tr> <?php

		endif; ?>

	</table> <?php
}

function woo_shipping_returns_tab_content() {
	global $post, $product, $wpdb, $WCFM, $WCFMmp;
	$post_id = $post->ID;
  $store_id = $post->post_author;
  $fake_empty_1 = "<p><br data-mce-bogus=\"1\"></p>";
  $fake_empty_2 = "<p><br></p>";

  $error_check = $wpdb->get_row( "SELECT umeta_id FROM gg_usermeta WHERE meta_key = 'wcfm_policy_vendor_options_en' AND user_id = $store_id" );
  if( $error_check ) :  $policy_vendor_options = get_user_meta( $store_id, 'wcfm_policy_vendor_options_en', true ); endif;
  // $policy_product_options = get_post_meta($post_id,'wcfm_policy_product_options',true); ?>

	<table class="product-tabs">
		<tr>
			<td class="td-title">Shipping</td>
			<td><?php

        $shipped_to = $product->get_attribute( 'pa_shipped-to' );
        if( $shipped_to ) :

					echo '<p>Shipped to '.$shipped_to.'</p>';

				endif;

				$shipped_from =  get_post_meta($post_id,'wcfm_custom_shipped_from',true);
				if( $shipped_from ) :

					echo '<p>Shipped from '.$shipped_from.'</p>';

				endif; ?>

        <!-- <p>Dispatched in </p> --> <?php

				echo $policy_vendor_options['shipping_policy']; ?>

				</td>
			</tr> <?php

		$refund_information_company =  $policy_vendor_options['refund_policy'];
    // $refund_information_product =  $policy_product_options['refund_policy'];
		if( !empty( $refund_information_company ) && $refund_information_company != $fake_empty_1 && $refund_information_company != $fake_empty_2 ) : ?>

			<tr>
				<td class="td-title">Refunds</td>
				<td><?php echo $refund_information_company ?></td>
			</tr> <?php

		endif;

  $returns_information_company =  $policy_vendor_options['cancellation_policy'];
  // $returns_information_product =  $policy_product_options['cancellation_policy'];
  if( !empty( $returns_information_company ) && $returns_information_company != $fake_empty_1 && $returns_information_company != $fake_empty_2 ) : ?>

    <tr>
      <td class="td-title">Cancellation<br />Return<br />Exchange</td>
      <td><?php echo $returns_information_company ?></td>
    </tr> <?php

  endif; ?>

	</table> <?php

}

//*** Change products per row to 3 on product archive
add_filter('loop_shop_columns', 'loop_columns', 999);
if (!function_exists('loop_columns')) {
	function loop_columns() {
		return 3;
	}
}

//*** Remove add to cart on product archive
add_action( 'woocommerce_after_shop_loop_item', 'remove_add_to_cart_buttons', 1 );

    function remove_add_to_cart_buttons() {
      if( is_product_category() || is_shop()) {
        remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart');
      }
    }

//*** Remove tag archive - DOESNT WORK
add_action('template_redirect', 'remove_wp_archives');
function remove_wp_archives(){
  if( is_product_tag() || is_date() || is_author() ) {
    global $wp_query;
    $wp_query->set_404();
  }
}

//*** Add per item price do_shortcode
function unit_price_shortcode() {

  global $product;

  $price_html = $product->get_price();
  $quantity_per_pack = get_field( 'quantity_per_pack' );

  if( $quantity_per_pack > 1 ) :

    $float_price = floatval( $price_html );
    $float_quantity_per_pack = floatval($quantity_per_pack);
    $unit_price = ($float_price / $float_quantity_per_pack);

    $per_unit_price = '(£';
    $per_unit_price .= round( $unit_price, 2 );
    $per_unit_price .= ' per unit)';

    return $per_unit_price;

  endif;
}
add_shortcode('unit-price', 'unit_price_shortcode');

//*** Add plus minus to quantity
add_action( 'woocommerce_after_add_to_cart_quantity', 'woo_quantity_plus_sign' );
function woo_quantity_plus_sign() {
  echo '<button type="button" class="woo-quanitiy-plus" ><i class="fas fa-plus"></i></button>';
}

add_action( 'woocommerce_before_add_to_cart_quantity', 'woo_quantity_minus_sign' );
function woo_quantity_minus_sign() {
  echo '<button type="button" class="woo-quanitiy-minus" ><i class="fas fa-minus"></i></button>';
}

add_action( 'wp_footer', 'woo_quantity_plus_minus' );
function woo_quantity_plus_minus() {

  if( is_product() || is_shop() ) ?>
    <script type="text/javascript">
      jQuery(document).ready(function($){

        $('form.cart').on( 'click', 'button.woo-quanitiy-plus, button.woo-quanitiy-minus', function() {

          // Get current quantity values
          var qty = $( this ).closest( 'form.cart' ).find( '.qty' );
          var val = parseFloat(qty.val());
          var max = parseFloat(qty.attr( 'max' ));
          var min = parseFloat(qty.attr( 'min' ));
          var step = parseFloat(qty.attr( 'step' ));

          // Change the value if plus or minus
          if ( $( this ).is( '.woo-quanitiy-plus' ) ) {
            if ( max && ( max <= val ) ) {
              qty.val( max );
            }
            else {
              qty.val( val + step );
            }
          }
          else {
            if ( min && ( min >= val ) ) {
              qty.val( min );
            }
            else if ( val > 1 ) {
              qty.val( val - step );
            }
          }

        });
      });
  </script> <?php
}

add_filter( 'woocommerce_variable_price_html', 'bbloomer_variation_price_format_min', 9999, 2 );

function bbloomer_variation_price_format_min( $price, $product ) {
   $prices = $product->get_variation_prices( true );
   $min_price = current( $prices['price'] );
   $price = sprintf( __( 'From: %1$s', 'woocommerce' ), wc_price( $min_price ) );
   return $price;
}
