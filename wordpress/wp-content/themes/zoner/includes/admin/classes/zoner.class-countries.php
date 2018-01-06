<?php
/**
 * Zoner countries
*/
 
class zoner_сountries {

	public $countries;
	public $states;
		
	public function __construct() {
		global $zoner, $states;
		
		$this->countries = apply_filters( 'zoner_countries', array(
		'AF' => __( 'Afghanistan', 'zoner' ),
		'AX' => __( '&#197;land Islands', 'zoner' ),
		'AL' => __( 'Albania', 'zoner' ),
		'DZ' => __( 'Algeria', 'zoner' ),
		'AD' => __( 'Andorra', 'zoner' ),
		'AO' => __( 'Angola', 'zoner' ),
		'AI' => __( 'Anguilla', 'zoner' ),
		'AQ' => __( 'Antarctica', 'zoner' ),
		'AG' => __( 'Antigua and Barbuda', 'zoner' ),
		'AR' => __( 'Argentina', 'zoner' ),
		'AM' => __( 'Armenia', 'zoner' ),
		'AW' => __( 'Aruba', 'zoner' ),
		'AU' => __( 'Australia', 'zoner' ),
		'AT' => __( 'Austria', 'zoner' ),
		'AZ' => __( 'Azerbaijan', 'zoner' ),
		'BS' => __( 'Bahamas', 'zoner' ),
		'BH' => __( 'Bahrain', 'zoner' ),
		'BD' => __( 'Bangladesh', 'zoner' ),
		'BB' => __( 'Barbados', 'zoner' ),
		'BY' => __( 'Belarus', 'zoner' ),
		'BE' => __( 'Belgium', 'zoner' ),
		'PW' => __( 'Belau', 'zoner' ),
		'BZ' => __( 'Belize', 'zoner' ),
		'BJ' => __( 'Benin', 'zoner' ),
		'BM' => __( 'Bermuda', 'zoner' ),
		'BT' => __( 'Bhutan', 'zoner' ),
		'BO' => __( 'Bolivia', 'zoner' ),
		'BQ' => __( 'Bonaire, Saint Eustatius and Saba', 'zoner' ),
		'BA' => __( 'Bosnia and Herzegovina', 'zoner' ),
		'BW' => __( 'Botswana', 'zoner' ),
		'BV' => __( 'Bouvet Island', 'zoner' ),
		'BR' => __( 'Brazil', 'zoner' ),
		'IO' => __( 'British Indian Ocean Territory', 'zoner' ),
		'VG' => __( 'British Virgin Islands', 'zoner' ),
		'BN' => __( 'Brunei', 'zoner' ),
		'BG' => __( 'Bulgaria', 'zoner' ),
		'BF' => __( 'Burkina Faso', 'zoner' ),
		'BI' => __( 'Burundi', 'zoner' ),
		'KH' => __( 'Cambodia', 'zoner' ),
		'CM' => __( 'Cameroon', 'zoner' ),
		'CA' => __( 'Canada', 'zoner' ),
		'CV' => __( 'Cape Verde', 'zoner' ),
		'KY' => __( 'Cayman Islands', 'zoner' ),
		'CF' => __( 'Central African Republic', 'zoner' ),
		'TD' => __( 'Chad', 'zoner' ),
		'CL' => __( 'Chile', 'zoner' ),
		'CN' => __( 'China', 'zoner' ),
		'CX' => __( 'Christmas Island', 'zoner' ),
		'CC' => __( 'Cocos (Keeling) Islands', 'zoner' ),
		'CO' => __( 'Colombia', 'zoner' ),
		'KM' => __( 'Comoros', 'zoner' ),
		'CG' => __( 'Congo (Brazzaville)', 'zoner' ),
		'CD' => __( 'Congo (Kinshasa)', 'zoner' ),
		'CK' => __( 'Cook Islands', 'zoner' ),
		'CR' => __( 'Costa Rica', 'zoner' ),
		'HR' => __( 'Croatia', 'zoner' ),
		'CU' => __( 'Cuba', 'zoner' ),
		'CW' => __( 'Cura&Ccedil;ao', 'zoner' ),
		'CY' => __( 'Cyprus', 'zoner' ),
		'CZ' => __( 'Czech Republic', 'zoner' ),
		'DK' => __( 'Denmark', 'zoner' ),
		'DJ' => __( 'Djibouti', 'zoner' ),
		'DM' => __( 'Dominica', 'zoner' ),
		'DO' => __( 'Dominican Republic', 'zoner' ),
		'EC' => __( 'Ecuador', 'zoner' ),
		'EG' => __( 'Egypt', 'zoner' ),
		'SV' => __( 'El Salvador', 'zoner' ),
		'GQ' => __( 'Equatorial Guinea', 'zoner' ),
		'ER' => __( 'Eritrea', 'zoner' ),
		'EE' => __( 'Estonia', 'zoner' ),
		'ET' => __( 'Ethiopia', 'zoner' ),
		'FK' => __( 'Falkland Islands', 'zoner' ),
		'FO' => __( 'Faroe Islands', 'zoner' ),
		'FJ' => __( 'Fiji', 'zoner' ),
		'FI' => __( 'Finland', 'zoner' ),
		'FR' => __( 'France', 'zoner' ),
		'GF' => __( 'French Guiana', 'zoner' ),
		'PF' => __( 'French Polynesia', 'zoner' ),
		'TF' => __( 'French Southern Territories', 'zoner' ),
		'GA' => __( 'Gabon', 'zoner' ),
		'GM' => __( 'Gambia', 'zoner' ),
		'GE' => __( 'Georgia', 'zoner' ),
		'DE' => __( 'Germany', 'zoner' ),
		'GH' => __( 'Ghana', 'zoner' ),
		'GI' => __( 'Gibraltar', 'zoner' ),
		'GR' => __( 'Greece', 'zoner' ),
		'GL' => __( 'Greenland', 'zoner' ),
		'GD' => __( 'Grenada', 'zoner' ),
		'GP' => __( 'Guadeloupe', 'zoner' ),
		'GT' => __( 'Guatemala', 'zoner' ),
		'GG' => __( 'Guernsey', 'zoner' ),
		'GN' => __( 'Guinea', 'zoner' ),
		'GW' => __( 'Guinea-Bissau', 'zoner' ),
		'GY' => __( 'Guyana', 'zoner' ),
		'HT' => __( 'Haiti', 'zoner' ),
		'HM' => __( 'Heard Island and McDonald Islands', 'zoner' ),
		'HN' => __( 'Honduras', 'zoner' ),
		'HK' => __( 'Hong Kong', 'zoner' ),
		'HU' => __( 'Hungary', 'zoner' ),
		'IS' => __( 'Iceland', 'zoner' ),
		'IN' => __( 'India', 'zoner' ),
		'ID' => __( 'Indonesia', 'zoner' ),
		'IR' => __( 'Iran', 'zoner' ),
		'IQ' => __( 'Iraq', 'zoner' ),
		'IE' => __( 'Republic of Ireland', 'zoner' ),
		'IM' => __( 'Isle of Man', 'zoner' ),
		'IL' => __( 'Israel', 'zoner' ),
		'IT' => __( 'Italy', 'zoner' ),
		'CI' => __( 'Ivory Coast', 'zoner' ),
		'JM' => __( 'Jamaica', 'zoner' ),
		'JP' => __( 'Japan', 'zoner' ),
		'JE' => __( 'Jersey', 'zoner' ),
		'JO' => __( 'Jordan', 'zoner' ),
		'KZ' => __( 'Kazakhstan', 'zoner' ),
		'KE' => __( 'Kenya', 'zoner' ),
		'KI' => __( 'Kiribati', 'zoner' ),
		'KW' => __( 'Kuwait', 'zoner' ),
		'KG' => __( 'Kyrgyzstan', 'zoner' ),
		'LA' => __( 'Laos', 'zoner' ),
		'LV' => __( 'Latvia', 'zoner' ),
		'LB' => __( 'Lebanon', 'zoner' ),
		'LS' => __( 'Lesotho', 'zoner' ),
		'LR' => __( 'Liberia', 'zoner' ),
		'LY' => __( 'Libya', 'zoner' ),
		'LI' => __( 'Liechtenstein', 'zoner' ),
		'LT' => __( 'Lithuania', 'zoner' ),
		'LU' => __( 'Luxembourg', 'zoner' ),
		'MO' => __( 'Macao S.A.R., China', 'zoner' ),
		'MK' => __( 'Macedonia', 'zoner' ),
		'MG' => __( 'Madagascar', 'zoner' ),
		'MW' => __( 'Malawi', 'zoner' ),
		'MY' => __( 'Malaysia', 'zoner' ),
		'MV' => __( 'Maldives', 'zoner' ),
		'ML' => __( 'Mali', 'zoner' ),
		'MT' => __( 'Malta', 'zoner' ),
		'MH' => __( 'Marshall Islands', 'zoner' ),
		'MQ' => __( 'Martinique', 'zoner' ),
		'MR' => __( 'Mauritania', 'zoner' ),
		'MU' => __( 'Mauritius', 'zoner' ),
		'YT' => __( 'Mayotte', 'zoner' ),
		'MX' => __( 'Mexico', 'zoner' ),
		'FM' => __( 'Micronesia', 'zoner' ),
		'MD' => __( 'Moldova', 'zoner' ),
		'MC' => __( 'Monaco', 'zoner' ),
		'MN' => __( 'Mongolia', 'zoner' ),
		'ME' => __( 'Montenegro', 'zoner' ),
		'MS' => __( 'Montserrat', 'zoner' ),
		'MA' => __( 'Morocco', 'zoner' ),
		'MZ' => __( 'Mozambique', 'zoner' ),
		'MM' => __( 'Myanmar', 'zoner' ),
		'NA' => __( 'Namibia', 'zoner' ),
		'NR' => __( 'Nauru', 'zoner' ),
		'NP' => __( 'Nepal', 'zoner' ),
		'NL' => __( 'Netherlands', 'zoner' ),
		'AN' => __( 'Netherlands Antilles', 'zoner' ),
		'NC' => __( 'New Caledonia', 'zoner' ),
		'NZ' => __( 'New Zealand', 'zoner' ),
		'NI' => __( 'Nicaragua', 'zoner' ),
		'NE' => __( 'Niger', 'zoner' ),
		'NG' => __( 'Nigeria', 'zoner' ),
		'NU' => __( 'Niue', 'zoner' ),
		'NF' => __( 'Norfolk Island', 'zoner' ),
		'KP' => __( 'North Korea', 'zoner' ),
		'NO' => __( 'Norway', 'zoner' ),
		'OM' => __( 'Oman', 'zoner' ),
		'PK' => __( 'Pakistan', 'zoner' ),
		'PS' => __( 'Palestinian Territory', 'zoner' ),
		'PA' => __( 'Panama', 'zoner' ),
		'PG' => __( 'Papua New Guinea', 'zoner' ),
		'PY' => __( 'Paraguay', 'zoner' ),
		'PE' => __( 'Peru', 'zoner' ),
		'PH' => __( 'Philippines', 'zoner' ),
		'PN' => __( 'Pitcairn', 'zoner' ),
		'PL' => __( 'Poland', 'zoner' ),
		'PT' => __( 'Portugal', 'zoner' ),
		'QA' => __( 'Qatar', 'zoner' ),
		'RE' => __( 'Reunion', 'zoner' ),
		'RO' => __( 'Romania', 'zoner' ),
		'RU' => __( 'Russia', 'zoner' ),
		'RW' => __( 'Rwanda', 'zoner' ),
		'BL' => __( 'Saint Barth&eacute;lemy', 'zoner' ),
		'SH' => __( 'Saint Helena', 'zoner' ),
		'KN' => __( 'Saint Kitts and Nevis', 'zoner' ),
		'LC' => __( 'Saint Lucia', 'zoner' ),
		'MF' => __( 'Saint Martin (French part)', 'zoner' ),
		'SX' => __( 'Saint Martin (Dutch part)', 'zoner' ),
		'PM' => __( 'Saint Pierre and Miquelon', 'zoner' ),
		'VC' => __( 'Saint Vincent and the Grenadines', 'zoner' ),
		'SM' => __( 'San Marino', 'zoner' ),
		'ST' => __( 'S&atilde;o Tom&eacute; and Pr&iacute;ncipe', 'zoner' ),
		'SA' => __( 'Saudi Arabia', 'zoner' ),
		'SN' => __( 'Senegal', 'zoner' ),
		'RS' => __( 'Serbia', 'zoner' ),
		'SC' => __( 'Seychelles', 'zoner' ),
		'SL' => __( 'Sierra Leone', 'zoner' ),
		'SG' => __( 'Singapore', 'zoner' ),
		'SK' => __( 'Slovakia', 'zoner' ),
		'SI' => __( 'Slovenia', 'zoner' ),
		'SB' => __( 'Solomon Islands', 'zoner' ),
		'SO' => __( 'Somalia', 'zoner' ),
		'ZA' => __( 'South Africa', 'zoner' ),
		'GS' => __( 'South Georgia/Sandwich Islands', 'zoner' ),
		'KR' => __( 'South Korea', 'zoner' ),
		'SS' => __( 'South Sudan', 'zoner' ),
		'ES' => __( 'Spain', 'zoner' ),
		'LK' => __( 'Sri Lanka', 'zoner' ),
		'SD' => __( 'Sudan', 'zoner' ),
		'SR' => __( 'Suriname', 'zoner' ),
		'SJ' => __( 'Svalbard and Jan Mayen', 'zoner' ),
		'SZ' => __( 'Swaziland', 'zoner' ),
		'SE' => __( 'Sweden', 'zoner' ),
		'CH' => __( 'Switzerland', 'zoner' ),
		'SY' => __( 'Syria', 'zoner' ),
		'TW' => __( 'Taiwan', 'zoner' ),
		'TJ' => __( 'Tajikistan', 'zoner' ),
		'TZ' => __( 'Tanzania', 'zoner' ),
		'TH' => __( 'Thailand', 'zoner' ),
		'TL' => __( 'Timor-Leste', 'zoner' ),
		'TG' => __( 'Togo', 'zoner' ),
		'TK' => __( 'Tokelau', 'zoner' ),
		'TO' => __( 'Tonga', 'zoner' ),
		'TT' => __( 'Trinidad and Tobago', 'zoner' ),
		'TN' => __( 'Tunisia', 'zoner' ),
		'TR' => __( 'Turkey', 'zoner' ),
		'TM' => __( 'Turkmenistan', 'zoner' ),
		'TC' => __( 'Turks and Caicos Islands', 'zoner' ),
		'TV' => __( 'Tuvalu', 'zoner' ),
		'UG' => __( 'Uganda', 'zoner' ),
		'UA' => __( 'Ukraine', 'zoner' ),
		'AE' => __( 'United Arab Emirates', 'zoner' ),
		'GB' => __( 'United Kingdom (UK)', 'zoner' ),
		'US' => __( 'United States (US)', 'zoner' ),
		'UY' => __( 'Uruguay', 'zoner' ),
		'UZ' => __( 'Uzbekistan', 'zoner' ),
		'VU' => __( 'Vanuatu', 'zoner' ),
		'VA' => __( 'Vatican', 'zoner' ),
		'VE' => __( 'Venezuela', 'zoner' ),
		'VN' => __( 'Vietnam', 'zoner' ),
		'WF' => __( 'Wallis and Futuna', 'zoner' ),
		'EH' => __( 'Western Sahara', 'zoner' ),
		'WS' => __( 'Western Samoa', 'zoner' ),
		'YE' => __( 'Yemen', 'zoner' ),
		'ZM' => __( 'Zambia', 'zoner' ),
		'ZW' => __( 'Zimbabwe', 'zoner' )
		));


		$states = array(
			'AF' => array(),
			'AT' => array(),
			'BE' => array(),
			'BI' => array(),
			'CZ' => array(),
			'DE' => array(),
			'DK' => array(),
			'EE' => array(),
			'FI' => array(),
			'FR' => array(),
			'IS' => array(),
			'IL' => array(),
			'KR' => array(),
			'NL' => array(),
			'NO' => array(),
			'PL' => array(),
			'PT' => array(),
			'SG' => array(),
			'SK' => array(),
			'SI' => array(),
			'LK' => array(),
			'SE' => array(),
			'VN' => array(),
		);
		
		foreach ( $this->countries as $abbr => $country ) {
			if ( !isset( $states[ $abbr ] ) && file_exists( dirname(__FILE__) . '/states/' . $abbr . '.php' ) ) {
				include( dirname(__FILE__) . '/states/' .  $abbr . '.php' );
			}
		}			

		$this->states = apply_filters( 'zoner_states', $states );
	}
	
	public function get_states( $country ) {
		return ( isset( $this->states[ $country ] ) ) ? $this->states[ $country ] : false;
	}
	
	public function get_name_state( $country, $state ) {
		return ( isset( $this->states[ $country ][$state] ) ) ? $this->states[ $country ][$state] : false;
	}
	
	public function country_dropdown_list ( $selected_country = '') {
		global $zoner_config;
			   $out = '';
		
		if ( apply_filters('zoner_sort_countries', true ) ) asort( $this->countries );
		
		if ( $this->countries ) foreach ( $this->countries as $key=>$value) :
			 $out .= '<option';
    		 if ($selected_country==$key) $out .= ' selected="selected"';
    			$out .= ' value="' . esc_attr( $key ) . '">'. $value .'</option>';
		endforeach;
		
		return $out;
	}
	
	public function country_specific_dropdown_list ( $selected_country = '') {
		global $zoner_config;
			   $out = '';
			   $countries = array();
			   $countries = $this->get_specific_countries();
		
		if ( $countries) foreach ( $countries as $key=>$value) :
			 $out .= '<option';
    		 if ($selected_country==$key) $out .= ' selected="selected"';
    			$out .= ' value="' . esc_attr( $key ) . '">'. $value .'</option>';
		endforeach;
		
		return $out;
	}
	
	
	public function get_specific_countries() {
		global $zoner_config;
		
		if ( apply_filters('zoner_sort_countries', true ) ) asort( $this->countries );
		
		$specific_country = array();
		if (!empty($zoner_config['specific-countries']))
		    $specific_country = $zoner_config['specific-countries'];
			   
		if (!empty($specific_country)) {
			foreach ($this->countries as $key=>$value) {
				if (in_array($key, $specific_country)) {
					$countries[$key] = $value;
				}
			}
		} else {
			$countries = $this->countries;
		}
		
		return $countries;
	}
	
	public function states_dropdown_list ( $selected_country = '', $state = '') {
		$out = '';
		if ( $this->get_states($selected_country) ) foreach ( $this->get_states($selected_country) as $key=>$value) :
			 $out .= '<option';
    		 if ($state == $key) $out .= ' selected="selected"';
    			$out .= ' value="' . esc_attr( $key ) . '">'. $value .'</option>';
		endforeach;
		
		return $out;
	}
	
	public function states_array_list( $selected_country = '') {
		return $this->get_states($selected_country);
	}
	
	public function zoner_get_all_metadata_($meta) {
		global $wpdb;
		
		$in_array = $result = array();
		$query = $wpdb->prepare( "SELECT distinct pm.meta_value FROM {$wpdb->posts} p, {$wpdb->postmeta} pm WHERE pm.meta_key='%s' AND ((pm.meta_value IS NOT NULL) OR (trim(pm.meta_value) != '') OR (pm.meta_value != 0)) AND p.ID = pm.post_id AND p.post_status = 'publish' ORDER BY pm.meta_value ASC", $meta );
		$items = $wpdb->get_results( $query, 'ARRAY_A' );
		
		if (!empty($items)) {
			foreach ($items as $item) {
				if (!empty($item))
				$in_array[] = $item['meta_value'];
			}			
		}
		
		$result = array_unique($in_array);
		
		return $result;
	}
	
}