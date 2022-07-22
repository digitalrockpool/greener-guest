<?php

/* Includes: Gravity Forms */

//*** Account Registration
add_action( 'gform_user_registered', 'account_registration', 10, 4 );
function account_registration( $user_id, $feed, $entry, $password ) {

	global $wpdb;

	$form_id = rgar( $feed, 'form_id' );
	$user = get_userdata( $user_id );
	$entry_date = date( 'Y-m-d H:i:s' );

	if ( $form_id == 2 ) : /* customer registration form */

    wp_signon( array(
			'user_login' => $user->user_login,
			'user_password' =>  $password,
			'remember' => false
    ) );

	elseif( $form_id == 3 ) : /* seller registration form */

		$wpdb->insert( 'gg_wcfm_messages',
			array(
				'message' => 'Vendor application waiting for approval',
				'author_id' => $user->ID,
				'reply_to' => 0,
				'message_to' => 0,
				'author_is_admin' => 0,
				'author_is_vendor' => 1,
				'author_is_customer' => 0,
				'is_notice' => 0,
				'is_direct_message' => 1,
				'is_pined' => 0,
				'message_type' => 'vendor_approval'
			)
		);

	endif;
}

//*** Form Submission - Marketplace Account Details
add_action( 'gform_after_submission_5', 'submit_marketplace_account_details', 10, 2 );
function submit_marketplace_account_details( $entry, $form ) {

	global $wpdb;

	$user_id = get_current_user_id();

	$seller_info = $wpdb->get_row( "SELECT * FROM gg_seller_info WHERE user_id=$user_id ORDER BY id DESC" );
	$name_first = $entry['11.3'];
	$name_last = $entry['11.6'];
	$email = $entry[12];
	$telephone = $entry[6];
	$alternative_telephone = $entry[7];
	$entry_date = date( 'Y-m-d H:i:s' );

	$wpdb->insert( 'gg_seller_info',
		array(
			'name_first' => $name_first,
			'name_last' => $name_last,
			'email' => $email,
			'telephone' => $telephone,
			'alternative_telephone' => $alternative_telephone,
			'company_name' => $seller_info->company_name,
			'vat_number' => $seller_info->vat_number,
			'address_street' => $seller_info->address_street,
			'address_line2' => $seller_info->address_line2,
			'address_city' => $seller_info->address_city,
			'address_state' => $seller_info->address_state,
			'address_postcode' => $seller_info->address_postcode,
			'address_country' => $seller_info->address_country,
			'operation_country' => $seller_info->operation_country,
			'import_export' => $seller_info->import_export,
			'website' => $seller_info->website,
			'company_standard' => $seller_info->company_standard,
			'company_impact' => $seller_info->company_impact,
			'company_policies' => $seller_info->company_policies,
			'marketing_source' => $seller_info->marketing_source,
			'user_id' => $user_id,
			'parent_id' => $seller_info->parent_id,
			'entry_date' => $entry_date
			)
	);
}

//*** Form Submission - Marketplace Company Profile
add_action( 'gform_after_submission_7', 'submit_marketplace_company_profile', 10, 2 );
function submit_marketplace_company_profile( $entry, $form ) {

	global $wpdb;

	$user_id = get_current_user_id();

	$seller_info = $wpdb->get_row( "SELECT * FROM gg_seller_info WHERE user_id=$user_id ORDER BY id DESC" );
	$company_name = $entry[9];
	$vat_number = $entry[33];
	$address_street = $entry['14.1'];
	$address_line2 = $entry['14.2'];
	$address_city = $entry['14.3'];
	$address_state = $entry['14.4'];
	$address_postcode = $entry['14.5'];
	$address_country = $entry['14.6'];
	$operation_country = $entry[29];
	$import_export = $entry[44];
	$website_entry = $entry[45];
	$company_standard_entry = $entry[41];
	$company_standard_other = $entry[42];
	$company_impact = $entry[17];
	$company_policies = $entry[19];
	$entry_date = date( 'Y-m-d H:i:s' );

	if( empty( $website_entry ) ) : $website = NULL; else : $website = $website_entry; endif;

	if( empty( $company_standard_entry ) ) :
		$company_standard = NULL;
	elseif( !empty( $company_standard_entry ) && !empty( $company_standard_other ) ) :
		$company_standard = $company_standard_entry.' | '.$company_standard_other;
	else:
		$company_standard = $company_standard_entry;
	endif;

	$wpdb->insert( 'gg_seller_info',
		array(
			'name_first' => $seller_info->name_first,
			'name_last' => $seller_info->name_last,
			'email' => $seller_info->email,
			'telephone' => $seller_info->telephone,
			'alternative_telephone' => $seller_info->alternative_telephone,
			'company_name' => $company_name,
			'vat_number' => $vat_number,
			'address_street' => $address_street,
			'address_line2' => $address_line2,
			'address_city' => $address_city,
			'address_state' => $address_state,
			'address_postcode' => $address_postcode,
			'address_country' => $address_country,
			'operation_country' => $operation_country,
			'import_export' => $import_export,
			'website' => $website,
			'company_standard' => $company_standard,
			'company_impact' => $company_impact,
			'company_policies' => $company_policies,
			'marketing_source' => $seller_info->marketing_source,
			'user_id' => $user_id,
			'parent_id' => $seller_info->parent_id,
			'entry_date' => $entry_date
			)
	);
}

//*** Form Submission - Marketplace Shipping & Returns Info
add_action( 'gform_after_submission_12', 'submit_marketplace_shipping_return', 10, 2 );
function submit_marketplace_shipping_return( $entry, $form ) {

	global $wpdb;

	$user_id = get_current_user_id();

	$product_info = $wpdb->get_row( "SELECT parent_id FROM gg_product_info WHERE user_id=$user_id ORDER BY id DESC" );

	if( empty( $product_info ) ) : $parent_id = 0; else : $parent_id = $product_info->parent_id; endif;

	$product_standard_entry = NULL;
	$product_standard_other = NULL;
	$product_environment_entry = NULL;
	$product_disposal_entry = NULL;
	$packaging_disposal_entry = $entry[25];
	$shipped_from_entry = $entry[7];
	$shipped_to_entry = $entry[8];
	$dispatched_in_entry = $entry[9];
	$shipping_method_entry = $entry[19];
	$shipping_method_other = $entry[20];
	$return_info_entry = $entry[11];
	$entry_date = date( 'Y-m-d H:i:s' );

	if( empty( $product_standard_entry ) ) :
		$product_standard = NULL;
	elseif( !empty( $product_standard_entry ) && !empty( $product_standard_other ) ) :
		$product_standard = $product_standard_entry.' | '.$product_standard_other;
	else:
			$product_standard = $product_standard_entry;
	endif;

	if( empty( $product_environment_entry ) ) : $product_environment = NULL; else : $product_environment = $product_environment_entry; endif;
	if( empty( $product_disposal_entry ) ) : $product_disposal = NULL; else : $product_disposal = $product_disposal_entry; endif;
	if( empty( $packaging_disposal_entry ) ) : $packaging_disposal = NULL; else : $packaging_disposal = $packaging_disposal_entry; endif;
	if( empty( $shipped_from_entry ) ) : $shipped_from = NULL; else : $shipped_from = $shipped_from_entry; endif;
	if( empty( $shipped_to_entry ) ) : $shipped_to = NULL; else : $shipped_to = $shipped_to_entry; endif;
	if( empty( $dispatched_in_entry ) ) : $dispatched_in = NULL; else : $dispatched_in = $dispatched_in_entry; endif;

	if( empty( $shipping_method_entry ) ) :
		$shipping_method = NULL;
	elseif( !empty( $shipping_method_entry ) && !empty( $shipping_method_other ) ) :
		$shipping_method = $shipping_method_entry.' | '.$shipping_method_other;
	else:
			$shipping_method = $shipping_method_entry;
	endif;

	if( empty( $return_info_entry ) ) : $return_info = NULL; else : $return_info = $return_info_entry; endif;

	$wpdb->insert( 'gg_product_info',
		array(
			'product_standard' => $product_standard,
			'product_environment' => $product_environment,
			'product_disposal' => $product_disposal,
			'packaging_disposal' => $packaging_disposal,
			'shipped_from' => $shipped_from,
			'shipped_to' => $shipped_to,
			'dispatched_in' => $dispatched_in,
			'shipping_method' => $shipping_method,
			'return_info' => $return_info,
			'user_id' => $user_id,
			'parent_id' => $parent_id,
			'entry_date' => $entry_date
			)
	);

	if( $parent_id == 0 ) :

		$new_parent_id = $wpdb->insert_id;

		$wpdb->update( 'gg_product_info',
			array(
				'parent_id' => $new_parent_id,
			),
			array(
				'id' => $new_parent_id
			)
		);

	endif;
}

/*** Form Submission - Marketplace Default Product Info - CURRENTLY ARCHIVED
add_action( 'gform_after_submission_8', 'submit_marketplace_default_product_info', 10, 2 );
function submit_marketplace_default_product_info( $entry, $form ) {

	global $wpdb;

	$user_id = get_current_user_id();

	$product_info = $wpdb->get_row( "SELECT parent_id FROM gg_product_info WHERE user_id=$user_id ORDER BY id DESC" );

	if( empty( $product_info ) ) : $parent_id = 0; else : $parent_id = $product_info->parent_id; endif;

	$product_standard_entry = $entry[1];
	$product_standard_other = $entry[18];
	$product_environment_entry = $entry[2];
	$product_disposal_entry = $entry[3];
	$packaging_disposal_entry = $entry[4];
	$shipped_from_entry = $entry[7];
	$shipped_to_entry = $entry[8];
	$dispatched_in_entry = $entry[9];
	$shipping_method_entry = $entry[19];
	$shipping_method_other = $entry[20];
	$return_info_entry = $entry[11];
	$entry_date = date( 'Y-m-d H:i:s' );

	if( empty( $product_standard_entry ) ) :
		$product_standard = NULL;
	elseif( !empty( $product_standard_entry ) && !empty( $product_standard_other ) ) :
		$product_standard = $product_standard_entry.' | '.$product_standard_other;
	else:
			$product_standard = $product_standard_entry;
	endif;

	if( empty( $product_environment_entry ) ) : $product_environment = NULL; else : $product_environment = $product_environment_entry; endif;
	if( empty( $product_disposal_entry ) ) : $product_disposal = NULL; else : $product_disposal = $product_disposal_entry; endif;
	if( empty( $packaging_disposal_entry ) ) : $packaging_disposal = NULL; else : $packaging_disposal = $packaging_disposal_entry; endif;
	if( empty( $shipped_from_entry ) ) : $shipped_from = NULL; else : $shipped_from = $shipped_from_entry; endif;
	if( empty( $shipped_to_entry ) ) : $shipped_to = NULL; else : $shipped_to = $shipped_to_entry; endif;
	if( empty( $dispatched_in_entry ) ) : $dispatched_in = NULL; else : $dispatched_in = $dispatched_in_entry; endif;

	if( empty( $shipping_method_entry ) ) :
		$shipping_method = NULL;
	elseif( !empty( $shipping_method_entry ) && !empty( $shipping_method_other ) ) :
		$shipping_method = $shipping_method_entry.' | '.$shipping_method_other;
	else:
			$shipping_method = $shipping_method_entry;
	endif;

	if( empty( $return_info_entry ) ) : $return_info = NULL; else : $return_info = $return_info_entry; endif;

	$wpdb->insert( 'gg_product_info',
		array(
			'product_standard' => $product_standard,
			'product_environment' => $product_environment,
			'product_disposal' => $product_disposal,
			'packaging_disposal' => $packaging_disposal,
			'shipped_from' => $shipped_from,
			'shipped_to' => $shipped_to,
			'dispatched_in' => $dispatched_in,
			'shipping_method' => $shipping_method,
			'return_info' => $return_info,
			'user_id' => $user_id,
			'parent_id' => $parent_id,
			'entry_date' => $entry_date
			)
	);

	if( $parent_id == 0 ) :

		$new_parent_id = $wpdb->insert_id;

		$wpdb->update( 'gg_product_info',
			array(
				'parent_id' => $new_parent_id,
			),
			array(
				'id' => $new_parent_id
			)
		);

	endif;
} */

//*** Dynamic Population
add_filter( 'gform_field_value', 'gravity_dynamic_population', 10, 3 );
function gravity_dynamic_population( $value, $field, $name ) {

	global $wpdb;
	$user_id = get_current_user_id();

	$seller_info = $wpdb->get_row( "SELECT * FROM gg_seller_info WHERE user_id=$user_id ORDER BY id DESC" );
	$product_info = $wpdb->get_row( "SELECT * FROM gg_product_info WHERE user_id=$user_id ORDER BY id DESC" );

	$split_standards = $seller_info->company_standard;
	$split_standard = explode( "|", $split_standards );
	$company_standard_set = $split_standard[0];
	$company_standard_other = $split_standard[1];

	$split_products = $product_info->product_standard;
	$split_product = explode( "|", $split_products );
	$product_standard_set = $split_product[0];
	$product_standard_other = $split_product[1];

	$split_shipping_methods = $product_info->shipping_method;
	$split_shipping_method = explode( "|", $split_shipping_methods );
	$shipping_method_set = $split_shipping_method[0];
	$shipping_method_other = $split_shipping_method[1];

	$copyright_year = date('Y');

	$values = array(
		'copyright_year' => $copyright_year,
		'name_first' => $seller_info->name_first,
		'name_last' => $seller_info->name_last,
		'email' => $seller_info->email,
		'telephone' => $seller_info->telephone,
		'alternative_telephone' => $seller_info->alternative_telephone,
		'company_name' => $seller_info->company_name,
		'vat_number' => $seller_info->vat_number,
		'address_street' => $seller_info->address_street,
		'address_line2' => $seller_info->address_line2,
		'address_city' => $seller_info->address_city,
		'address_state' => $seller_info->address_state,
		'address_postcode' => $seller_info->address_postcode,
		'address_country' => $seller_info->address_country,
		'operation_country' => $seller_info->operation_country,
		'import_export' => $seller_info->import_export,
		'website' => $seller_info->website,
		'company_impact' => $seller_info->company_impact,
		'company_standard' => $company_standard_set,
		'company_standard_other' => $company_standard_other,
		'company_policies' => $seller_info->company_policies,
		'product_standard' => $product_standard_set,
		'product_standard_other' => $product_standard_other,
		'product_environment' => $product_info->product_environment,
		'product_disposal' => $product_info->product_disposal,
		'packaging_disposal' => $product_info->packaging_disposal,
		'shipped_from' => $product_info->shipped_from,
		'shipped_to' => $product_info->shipped_to,
		'dispatched_in' => $product_info->dispatched_in,
		'shipping_method' => $shipping_method_set,
		'shipping_method_other' => $shipping_method_other,
		'return_info' => $product_info->return_info
	);

	return isset( $values[ $name ] ) ? $values[ $name ] : $value;
}

//*** Read Only Fields
add_filter('gform_pre_render', 'add_readonly_script');
function add_readonly_script($form){
    ?>

    <script type="text/javascript">
        jQuery(document).ready(function(){
            jQuery("li.readonly input").attr("readonly","readonly");
        });
    </script>

    <?php
    return $form;
}

//*** Dynamic Populate Resource URL
add_filter( 'gform_field_value_resource_url', 'population_resource_url' );
function population_resource_url( $value ) {

	global $post;
	$post_id = $post->ID;

	$path = esc_url( get_field('resource_file', $post_id) );

  return $path;
}
