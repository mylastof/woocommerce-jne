<?php
/*
Plugin Name: WooCommerce JNE Shipping ( Free Version )
Plugin URI: http://www.agenwebsite.com/products/woocommerce-jne-shipping
Description: Plugin untuk WooCommerce dengan penambahan metode shipping JNE. <strong>Dapatkan versi lengkap di <a href="http://www.agenwebsite.com/products/woocommerce-jne-shipping" target="_blank">WooCommerce JNE Shipping Full Version</a></strong>
Version: 7.0.2
Author: Fairuz Fatin
Author URI: http://www.agenwebsite.com/
*/
	function jne_shipping_init() {
 		define('jne_version',jne_get_version());
		$woo_ver = woo_get_version();

		if($woo_ver[1] == 0) 
			define('jne_url', 'admin.php?page=woocommerce_settings');
		else 
			define('jne_url', 'admin.php?page=wc-settings');
	
		include_once 'shipping/init.jne-shipping.php';
				 
		function jne_links( $actions, $plugin_file, $plugin_data, $context ) {
			array_unshift($actions, '<a href="http://www.agenwebsite.com/products/woocommerce-jne-shipping" target="_blank">Buy Full Version</a> | <a href="'.jne_url.'&tab=shipping&section=wc_jne">Settings</a>');
			return $actions;
		}

		add_filter('woocommerce_shipping_calculator_enable_city', '__return_true');
		add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'jne_links', 10, 4);

		function billing_jne_free_modified_address_fields($address_fields){ 
			$jne_free_states[''] = __( 'Select an option', 'woocommerce' );

			$shipping_cities = get_option( 'woocommerce_jne_free_data' );
		   
			if(isset($shipping_cities) && isset($shipping_cities['cost_data'])) {asort($shipping_cities['cost_data']);}
	 
			if(is_array($shipping_cities['cost_data']) && count($shipping_cities['cost_data']) > 0){
				foreach($shipping_cities['cost_data'] as $key => $city){
					$new_states[$key] = $city['city'];
				}
			}

			$form = 'form-row-wide';
			if($form == 'form-row-wide') $clear = true; else $clear = false;

			$address_fields['billing']['billing_city'] = array(
				'type'		=> 'select',
				'label'		=> 'City',
				'placeholder'	=> 'City',
				'required'	=> true,
				'class'		=> array($form, 'update_totals_on_change'),
				'clear'		=> $clear,
				'defaults'	=> array(
					'' => __( 'Select an option', 'woocommerce' ),
				),
				'options'       => $new_states
			);
			$address_fields['shipping']['shipping_city'] = array(
				'type'          => 'select',
				'label'         => 'City',
				'placeholder'       => 'City',
				'required'      => true,
				'class'         => array($form, 'update_totals_on_change'),
				'clear'         => $clear,
				'defaults'		 => array(
					'' => __( 'Select an option', 'woocommerce' ),
				),
				'options'       => $new_states
			);
			return $address_fields;
		}

		add_filter('woocommerce_checkout_fields', 'billing_jne_free_modified_address_fields', 1, 10);
	}
	add_action('init', 'jne_shipping_init', 0);

	function jne_get_version() {
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		$data = get_plugins( '/' . plugin_basename( 'woocommerce-jne' ) );
		$version = $data['woocommerce-jne.php']['Version'];
		return $version;
	}

	if(!function_exists('woo_get_version')) {
		function woo_get_version() {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			$data = get_plugins( '/' . plugin_basename( 'woocommerce' ) );
			$version = explode('.',$data['woocommerce.php']['Version']);
			return $version;
		}
	}

	function jne_enqueue_script() {
		$woo_ver = woo_get_version();

		if ( is_page() ) {

			if($woo_ver[1] == 0) {
				if(is_checkout()) {
					wp_deregister_script( 'wc-checkout' );
					wp_enqueue_script('woocommerce-jne-old', plugin_dir_url(__FILE__) . 'assets/js/woocommerce-jne.old.js', array( 'jquery' ), false, true);
				}
				wp_enqueue_script('woocommerce-jne-old-version', plugin_dir_url(__FILE__) . 'assets/js/woocommerce-jne.ver.js', array( 'jquery' ), false, true);
			}

			wp_enqueue_script('woocommerce-jne-chosen', plugin_dir_url(__FILE__) . 'assets/js/woocommerce-jne-chosen.js', array( 'jquery' ), false, true);

		}

	}
	add_action( 'wp_enqueue_scripts', 'jne_enqueue_script' );
?>