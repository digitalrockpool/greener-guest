<?php
//*** Remove customer support from store setup wizard */
add_filter('wcfmmp_store_setup_steps', 'remove_support_tab');
function remove_support_tab($steps) {
    unset($steps['support']);
    return $steps;
}

add_filter( 'wcfm_store_setup_complete_message',function($txt){
	$txt = __( "Great news, your Greener Guest store has been set up and is ready for you to stock with products.", 'wc-multivendor-marketplace' );
	return $txt;
},10,2);

//*** Hide about field on profile */
add_filter( 'wcfm_profile_fields_about', function( $profile_field, $user_id ) {
	return array();
}, 50, 2 );

//*** Change profile avatar top menu */
add_filter( 'wcfm_defaut_user_avatar', function( $defaut_user_avatar ) {
  $defaut_user_avatar = 'https://secure.gravatar.com/avatar/c75cee65da15dd5c3ce59422157d3eaf?s=90&d=mm&r=g';
	return $defaut_user_avatar;
}, 50 );

//*** Hide elements */
add_filter( 'wcfm_is_allow_profile_complete_bar', '__return_false' );
add_filter( 'wcfm_is_allow_policy_tab_title', '__return_false' );
add_filter( 'wcfm_is_allow_policy_product_settings', '__return_false' );
add_filter( 'wcfm_is_allow_store_email', '__return_false' );
add_filter( 'wcfm_is_allow_store_banner_type', '__return_false' );
add_filter( 'wcfm_is_allow_store_mobile_banner', '__return_false' );
add_filter( 'wcfm_is_allow_store_list_banner', '__return_false' );
add_filter( 'wcfm_is_allow_store_visibility', '__return_false' );


add_filter( 'wcfm_vendor_settings_fields_policies',function($fields, $vendor_id){
	$fields['wcfm_policy_tab_title']['label'] = '';
	$fields['wcfm_policy_tab_title']['type'] = 'hidden';
	$fields['wcfm_policy_tab_title']['value'] = 'Store Policies';
	return $fields;
},10,2);

//*** Add employee rights and/or social and environmental standards  */
add_filter( 'wcfm_vendor_settings_fields_policies', function( $policy_fields, $vendor_id ) {
	$_wcfm_vendor_social_policy = wcfm_get_user_meta( $vendor_id, 'wcfm_vendor_social_policy', true );
	$rich_editor = apply_filters( 'wcfm_is_allow_rich_editor', 'rich_editor' );
	$wpeditor = apply_filters( 'wcfm_is_allow_product_wpeditor', 'wpeditor' );
	if( $wpeditor && $rich_editor ) {
		$rich_editor = 'wcfm_wpeditor';
	} else {
		$wpeditor = 'textarea';
	}
	$policy_fields['social_policy'] = array('label' => __('Employee Rights and/or Social and Environmental Standards', 'wc-frontend-manager'), 'type' => $wpeditor, 'class' => 'wcfm-textarea wcfm_ele wcfm_full_ele wcfm_custom_field_editor ' . $rich_editor, 'label_class' => 'wcfm_title wcfm_full_title', 'value' => $_wcfm_vendor_social_policy );
	return $policy_fields;
}, 50, 2 );
add_action( 'wcfm_vendor_settings_update', function( $vendor_id, $wcfm_settings_form ) {
	if( isset( $wcfm_settings_form['social_policy'] ) ) {
		wcfm_update_user_meta( $vendor_id, 'wcfm_vendor_social_policy', $wcfm_settings_form['social_policy'] );
	}
}, 20, 2 );
add_action( 'wcfmmp_store_after_policies', function( $vendor_id ) {
	$_wcfm_vendor_social_policy = wcfm_get_user_meta( $vendor_id, 'wcfm_vendor_social_policy', true );
	if( !wcfm_empty( $_wcfm_vendor_social_policy ) ) { ?>
		<div class="policies_area wcfm-social-policies">
			<h2 class="wcfm_policies_heading"><?php echo apply_filters('wcfm_social_policies_heading', __('Employee Rights and/or Social and Environmental Standards', 'wc-frontend-manager')); ?></h2>
			<div class="wcfm_policies_description" ><?php echo $_wcfm_vendor_social_policy; ?></div>
		</div>
	<?php }
}, 50 );
add_action( 'wcfm_policy_content_after', function( $product_id ) {
	global $WCFM;
	$vendor_id = $WCFM->wcfm_vendor_support->wcfm_get_vendor_id_from_product( $product_id );
	if( $vendor_id ) {
		$_wcfm_vendor_social_policy = wcfm_get_user_meta( $vendor_id, 'wcfm_vendor_social_policy', true );
		if( !wcfm_empty( $_wcfm_vendor_social_policy ) ) {
			?>
			<div class="wcfm-social-policies">
				<h2 class="wcfm_policies_heading"><?php echo apply_filters('wcfm_social_policies_heading', __('Employee Rights and/or Social and Environmental Standards', 'wc-frontend-manager')); ?></h2>
				<div class="wcfm_policies_description" ><?php echo $_wcfm_vendor_social_policy; ?></div>
			</div>
			<?php
		}
	}
}, 50 );
add_action( 'wcfm_order_details_policy_content_after', function( $vendor_id ) {
	$_wcfm_vendor_social_policy = wcfm_get_user_meta( $vendor_id, 'wcfm_vendor_social_policy', true );
	if( !wcfm_empty( $_wcfm_vendor_social_policy ) ) {
		?>
		<tr>
			<th colspan="3" style="background-color: #eeeeee;padding: 1em 1.41575em;line-height: 1.5;"><strong><?php echo apply_filters('wcfm_social_policies_heading', __('Employee Rights and/or Social and Environmental Standards', 'wc-frontend-manager')); ?></strong></th>
			<td colspan="5" style="background-color: #f8f8f8;padding: 1em;"><?php echo $_wcfm_vendor_social_policy; ?></td>
	  </tr>
		<?php
	}
}, 50 );
add_action( 'wcfm_store_invoice_policy_content_after', function( $product_id ) {
	global $WCFM;
	$vendor_id = $WCFM->wcfm_vendor_support->wcfm_get_vendor_id_from_product( $product_id );
	if( $vendor_id ) {
		$_wcfm_vendor_social_policy = wcfm_get_user_meta( $vendor_id, 'wcfm_vendor_social_policy', true );
		if( !wcfm_empty( $_wcfm_vendor_social_policy ) ) {
			?>
			<tr>
				<th colspan="3" style="background-color: #eeeeee;padding: 1em 1.41575em;line-height: 1.5;"><?php echo apply_filters('wcfm_social_policies_heading', __('Employee Rights and/or Social and Environmental Standards', 'wc-frontend-manager')); ?></th>
				<td colspan="5" style="background-color: #f8f8f8;padding: 1em;"><?php echo $_wcfm_vendor_social_policy; ?></td>
			</tr>
			<?php
		}
	}
}, 50 );


//*** Check and hide  */

add_filter( 'wcfm_product_manage_fields_shipping', function($shipping_fields, $product_id) {
  if( apply_filters( 'wcfm_is_allow_shipping', true ) ) {
  	$shipped_from_options = array(
			"" => "Select country",
			"Afghanistan" => "Afghanistan",
			"Albania" => "Albania",
			"Algeria" => "Algeria",
			"American Samoa" => "American Samoa",
			"Andorra" => "Andorra",
			"Angola" => "Angola",
			"Anguilla" => "Anguilla",
			"Antarctica" => "Antarctica",
			"Antigua and Barbuda" => "Antigua and Barbuda",
			"Argentina" => "Argentina",
			"Armenia" => "Armenia",
			"Aruba" => "Aruba",
			"Australia" => "Australia",
			"Austria" => "Austria",
			"Azerbaijan" => "Azerbaijan",
			"Bahamas" => "Bahamas",
			"Bahrain" => "Bahrain",
			"Bangladesh" => "Bangladesh",
			"Barbados" => "Barbados",
			"Belarus" => "Belarus",
			"Belgium" => "Belgium",
			"Belize" => "Belize",
			"Benin" => "Benin",
			"Bermuda" => "Bermuda",
			"Bhutan" => "Bhutan",
			"Bolivia" => "Bolivia",
			"Bosnia and Herzegovina" => "Bosnia and Herzegovina",
			"Botswana" => "Botswana",
			"Bouvet Island" => "Bouvet Island",
			"Brazil" => "Brazil",
			"British Indian Ocean Territory" => "British Indian Ocean Territory",
			"Brunei Darussalam" => "Brunei Darussalam",
			"Bulgaria" => "Bulgaria",
			"Burkina Faso" => "Burkina Faso",
			"Burundi" => "Burundi",
			"Cambodia" => "Cambodia",
			"Cameroon" => "Cameroon",
			"Canada" => "Canada",
			"Cape Verde" => "Cape Verde",
			"Cayman Islands" => "Cayman Islands",
			"Central African Republic" => "Central African Republic",
			"Chad" => "Chad",
			"Chile" => "Chile",
			"China" => "China",
			"Christmas Island" => "Christmas Island",
			"Cocos (Keeling) Islands" => "Cocos (Keeling) Islands",
			"Colombia" => "Colombia",
			"Comoros" => "Comoros",
			"Congo" => "Congo",
			"Congo, the Democratic Republic of the" => "Congo, the Democratic Republic of the",
			"Cook Islands" => "Cook Islands",
			"Costa Rica" => "Costa Rica",
			"Cote D'Ivoire" => "Cote D'Ivoire",
			"Croatia" => "Croatia",
			"Cuba" => "Cuba",
			"Cyprus" => "Cyprus",
			"Czech Republic" => "Czech Republic",
			"Denmark" => "Denmark",
			"Djibouti" => "Djibouti",
			"Dominica" => "Dominica",
			"Dominican Republic" => "Dominican Republic",
			"Ecuador" => "Ecuador",
			"Egypt" => "Egypt",
			"El Salvador" => "El Salvador",
			"Equatorial Guinea" => "Equatorial Guinea",
			"Eritrea" => "Eritrea",
			"Estonia" => "Estonia",
			"Ethiopia" => "Ethiopia",
			"Falkland Islands (Malvinas)" => "Falkland Islands (Malvinas)",
			"Faroe Islands" => "Faroe Islands",
			"Fiji" => "Fiji",
			"Finland" => "Finland",
			"France" => "France",
			"French Guiana" => "French Guiana",
			"French Polynesia" => "French Polynesia",
			"French Southern Territories" => "French Southern Territories",
			"Gabon" => "Gabon",
			"Gambia" => "Gambia",
			"Georgia" => "Georgia",
			"Germany" => "Germany",
			"Ghana" => "Ghana",
			"Gibraltar" => "Gibraltar",
			"Greece" => "Greece",
			"Greenland" => "Greenland",
			"Grenada" => "Grenada",
			"Guadeloupe" => "Guadeloupe",
			"Guam" => "Guam",
			"Guatemala" => "Guatemala",
			"Guinea" => "Guinea",
			"Guinea-Bissau" => "Guinea-Bissau",
			"Guyana" => "Guyana",
			"Haiti" => "Haiti",
			"Heard Island and Mcdonald Islands" => "Heard Island and Mcdonald Islands",
			"Holy See (Vatican City State)" => "Holy See (Vatican City State)",
			"Honduras" => "Honduras",
			"Hong Kong" => "Hong Kong",
			"Hungary" => "Hungary",
			"Iceland" => "Iceland",
			"India" => "India",
			"Indonesia" => "Indonesia",
			"Iran, Islamic Republic of" => "Iran, Islamic Republic of",
			"Iraq" => "Iraq",
			"Ireland" => "Ireland",
			"Israel" => "Israel",
			"Italy" => "Italy",
			"Jamaica" => "Jamaica",
			"Japan" => "Japan",
			"Jordan" => "Jordan",
			"Kazakhstan" => "Kazakhstan",
			"Kenya" => "Kenya",
			"Kiribati" => "Kiribati",
			"Korea, Democratic People's Republic of" => "Korea, Democratic People's Republic of",
			"Korea, Republic of" => "Korea, Republic of",
			"Kuwait" => "Kuwait",
			"Kyrgyzstan" => "Kyrgyzstan",
			"Lao People's Democratic Republic" => "Lao People's Democratic Republic",
			"Latvia" => "Latvia",
			"Lebanon" => "Lebanon",
			"Lesotho" => "Lesotho",
			"Liberia" => "Liberia",
			"Libyan Arab Jamahiriya" => "Libyan Arab Jamahiriya",
			"Liechtenstein" => "Liechtenstein",
			"Lithuania" => "Lithuania",
			"Luxembourg" => "Luxembourg",
			"Macao" => "Macao",
			"Macedonia, the Former Yugoslav Republic of" => "Macedonia, the Former Yugoslav Republic of",
			"Madagascar" => "Madagascar",
			"Malawi" => "Malawi",
			"Malaysia" => "Malaysia",
			"Maldives" => "Maldives",
			"Mali" => "Mali",
			"Malta" => "Malta",
			"Marshall Islands" => "Marshall Islands",
			"Martinique" => "Martinique",
			"Mauritania" => "Mauritania",
			"Mauritius" => "Mauritius",
			"Mayotte" => "Mayotte",
			"Mexico" => "Mexico",
			"Micronesia, Federated States of" => "Micronesia, Federated States of",
			"Moldova, Republic of" => "Moldova, Republic of",
			"Monaco" => "Monaco",
			"Mongolia" => "Mongolia",
			"Montserrat" => "Montserrat",
			"Morocco" => "Morocco",
			"Mozambique" => "Mozambique",
			"Myanmar" => "Myanmar",
			"Namibia" => "Namibia",
			"Nauru" => "Nauru",
			"Nepal" => "Nepal",
			"Netherlands" => "Netherlands",
			"Netherlands Antilles" => "Netherlands Antilles",
			"New Caledonia" => "New Caledonia",
			"New Zealand" => "New Zealand",
			"Nicaragua" => "Nicaragua",
			"Niger" => "Niger",
			"Nigeria" => "Nigeria",
			"Niue" => "Niue",
			"Norfolk Island" => "Norfolk Island",
			"Northern Mariana Islands" => "Northern Mariana Islands",
			"Norway" => "Norway",
			"Oman" => "Oman",
			"Pakistan" => "Pakistan",
			"Palau" => "Palau",
			"Palestinian Territory, Occupied" => "Palestinian Territory, Occupied",
			"Panama" => "Panama",
			"Papua New Guinea" => "Papua New Guinea",
			"Paraguay" => "Paraguay",
			"Peru" => "Peru",
			"Philippines" => "Philippines",
			"Pitcairn" => "Pitcairn",
			"Poland" => "Poland",
			"Portugal" => "Portugal",
			"Puerto Rico" => "Puerto Rico",
			"Qatar" => "Qatar",
			"Reunion" => "Reunion",
			"Romania" => "Romania",
			"Russian Federation" => "Russian Federation",
			"Rwanda" => "Rwanda",
			"Saint Helena" => "Saint Helena",
			"Saint Kitts and Nevis" => "Saint Kitts and Nevis",
			"Saint Lucia" => "Saint Lucia",
			"Saint Pierre and Miquelon" => "Saint Pierre and Miquelon",
			"Saint Vincent and the Grenadines" => "Saint Vincent and the Grenadines",
			"Samoa" => "Samoa",
			"San Marino" => "San Marino",
			"Sao Tome and Principe" => "Sao Tome and Principe",
			"Saudi Arabia" => "Saudi Arabia",
			"Senegal" => "Senegal",
			"Serbia and Montenegro" => "Serbia and Montenegro",
			"Seychelles" => "Seychelles",
			"Sierra Leone" => "Sierra Leone",
			"Singapore" => "Singapore",
			"Slovakia" => "Slovakia",
			"Slovenia" => "Slovenia",
			"Solomon Islands" => "Solomon Islands",
			"Somalia" => "Somalia",
			"South Africa" => "South Africa",
			"South Georgia and the South Sandwich Islands" => "South Georgia and the South Sandwich Islands",
			"Spain" => "Spain",
			"Sri Lanka" => "Sri Lanka",
			"Sudan" => "Sudan",
			"Suriname" => "Suriname",
			"Svalbard and Jan Mayen" => "Svalbard and Jan Mayen",
			"Swaziland" => "Swaziland",
			"Sweden" => "Sweden",
			"Switzerland" => "Switzerland",
			"Syrian Arab Republic" => "Syrian Arab Republic",
			"Taiwan, Province of China" => "Taiwan, Province of China",
			"Tajikistan" => "Tajikistan",
			"Tanzania, United Republic of" => "Tanzania, United Republic of",
			"Thailand" => "Thailand",
			"Timor-Leste" => "Timor-Leste",
			"Togo" => "Togo",
			"Tokelau" => "Tokelau",
			"Tonga" => "Tonga",
			"Trinidad and Tobago" => "Trinidad and Tobago",
			"Tunisia" => "Tunisia",
			"Turkey" => "Turkey",
			"Turkmenistan" => "Turkmenistan",
			"Turks and Caicos Islands" => "Turks and Caicos Islands",
			"Tuvalu" => "Tuvalu",
			"Uganda" => "Uganda",
			"Ukraine" => "Ukraine",
			"United Arab Emirates" => "United Arab Emirates",
			"United Kingdom" => "United Kingdom",
			"United States" => "United States",
			"United States Minor Outlying Islands" => "United States Minor Outlying Islands",
			"Uruguay" => "Uruguay",
			"Uzbekistan" => "Uzbekistan",
			"Vanuatu" => "Vanuatu",
			"Venezuela" => "Venezuela",
			"Viet Nam" => "Viet Nam",
			"Virgin Islands, British" => "Virgin Islands, British",
			"Virgin Islands, U.s." => "Virgin Islands, U.S.",
			"Wallis and Futuna" => "Wallis and Futuna",
			"Western Sahara" => "Western Sahara",
			"Yemen" => "Yemen",
			"Zambia" => "Zambia",
			"Zimbabwe" => "Zimbabwe"
    );

    $shipped_from = get_post_meta( $product_id, 'wcfm_custom_shipped_from', true );
    $shipping_fields['wcfm_custom_shipped_from'] = array( 'label' => __( 'Shipping From', 'wc-frontend-manager' ), 'type' => 'select', 'options' => $shipped_from_options, 'class' => 'wcfm-select wcfm_ele simple variable booking', 'label_class' => 'wcfm_title', 'value' => $shipped_from );

    }
  return $shipping_fields;
}, 10, 2 );

add_action( 'after_wcfm_products_manage_meta_save', function( $product_id, $form_data ) {
    if ( apply_filters( 'wcfm_is_allow_shipping', true ) ) {
        if ( isset( $form_data['wcfm_custom_shipped_from'] ) ) {
            update_post_meta( $product_id, 'wcfm_custom_shipped_from', $form_data['wcfm_custom_shipped_from'] );
        }
    }
}, 150, 2 );
