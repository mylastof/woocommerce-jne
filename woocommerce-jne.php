<?php
/*
Plugin Name: WooCommerce JNE Shipping ( Free Version )
Plugin URI: http://www.agenwebsite.com/plugin-woocommerce-jne-shipping-indonesia-free-version.html
Description: Plugin untuk WooCommerce dengan penambahan metode shipping jne_free, dilengkapi dengan mata uang Indonesia (Rp). Dapatkan versi lengkap di <a href="http://www.agenwebsite.com/plugin-woocommerce-jne-shipping-indonesia-full-version.html">WooCommerce JNE Shipping Full Version</a>
Version: 1.0.1
Author: ItsMeFurZy
Author URI: http://itsmefurzy.blogspot.com/
*/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
add_action('plugins_loaded', 'woocommerce_jne_free_shipping_init', 0);

function woocommerce_jne_free_shipping_init() {
	if (!class_exists('woocommerce_shipping_method')) return;

		class WC_JNE_FREE extends woocommerce_shipping_method {
		  
		  function __construct() { 
		    $this->id            = 'jne_free';
		    $this->method_title = __('JNE Shipping Free', 'woothemes');
		 
		    $this->jne_free_option        = 'woocommerce_jne_free_data';
		    $this->admin_page_heading        = __( 'JNE Shipping Free', 'woocommerce' );
		    $this->admin_page_description    = __( 'JNE let you define a standard rate per item, or per order.', 'woocommerce' );
		 
		    add_action( 'woocommerce_update_options_shipping_' . $this->id, array( &$this, 'process_admin_options' ) );
		    add_action( 'woocommerce_update_options_shipping_' . $this->id, array( &$this, 'process_jne_free' ) );
		     
		    $this->init();
		  } 
		     
		  function init() {
		    // Load the form fields.
		    $this->init_form_fields();
		 
		    // Load the settings.
		    $this->init_settings();
		 
		    // Define user set variables
		    $this->enabled         = $this->settings['enabled'];
		    $this->title           = $this->settings['title'];
		    $this->availability    = $this->settings['availability'];
		    $this->countries       = $this->settings['countries'];
		 
		    // Get options
		    $this->options         = (array) explode( "\n", $this->options );
		 
		    // Load jne_free rates
		    $this->get_jne_free();
		     
		  }
		 
		    /**
		     * Initialise Gateway Settings Form Fields
		     */
		    function init_form_fields() {
			global $woocommerce;

			$this->form_fields = array(
			    'enabled' => array(
				            'title'         => __( 'Enable/Disable', 'woocommerce' ), 
				            'type'          => 'checkbox', 
				            'label'         => __( 'Enable this shipping method', 'woocommerce' ), 
				            'default'       => 'yes',
				        ), 
			    'title' => array(
				            'title'         => __( 'Method Title', 'woocommerce' ), 
				            'type'          => 'text', 
				            'description'   => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ), 
				            'default'       => __( 'JNE', 'woocommerce' ),
				        ),
			    'availability' => array(
					    'title' 	    => __( 'Method availability', 'woocommerce' ),
					    'type' 	    => 'select',
					    'default' 	    => 'all',
					    'class'	    => 'availability',
					    'options'	    => array(
								'all' 	    => __('All allowed countries', 'woocommerce'),
						    		'specific' 	    => __('Specific Countries', 'woocommerce')
						    		)
					    ),
			    'countries' => array(
					    'title' 	    => __( 'Specific Countries', 'woocommerce' ),
					    'type' 	    => 'multiselect',
					    'class'	    => 'chosen_select',
					    'css'	    => 'width: 450px;',
					    'default' 	    => '',
					    'options'	    => $woocommerce->countries->countries
					    ),
			    'credits' => array(
				            'title'         => __( 'Enable/Disable Credit Link', 'woocommerce' ), 
				            'type'          => 'checkbox', 
				            'label'         => __( 'Enable credit link', 'woocommerce' ), 
				            'default'       => 'yes',
				        ), 
			    );
		     
		    } // End init_form_fields()
		     
		    function calculate_shipping( $package = array() ) {
		      global $woocommerce;
			 
		      $_tax = &new woocommerce_tax();
			 
		      $this->shipping_total  = 0;
		      $this->shipping_tax    = 0;
		       
		      $cost = 0;
		      $total_weight = 0;
		       
		       
		      $state = (isset($_POST['state']))?$_POST['state']:$package['destination']['state'];
		      $state = (isset($_POST['calc_shipping_state']))?$_POST['calc_shipping_state']:$state;
		       
		      $shipping_cities = get_option( 'woocommerce_jne_free_data' );
		      
		      $shipping_price = $shipping_cities['cost_data'][$state];
		 
		      $cost = $shipping_price['price'];

		      if($cost == 0) {
				return false;
		      }
		       
		      if (sizeof($woocommerce->cart->cart_contents)>0){
			foreach($woocommerce->cart->cart_contents as $cart_product){
			  $total_weight += $cart_product['data']->weight * $cart_product['quantity'];
			  if(!$total_weight) { $total_weight = 1; }
			}
		      }
		      $cost = $cost * ceil($total_weight);

		      $rate = array(
			'id'        => $this->id,
			'label'     => $this->title,
			'cost'      => $cost
		      );

		      $this->add_rate($rate);
		    } 
		 
		    /**
		     * Admin Panel Options 
		     * - Options for bits like 'title' and availability on a country-by-country basis
		     *
		     * @since 1.0.0
		     */
		    public function admin_options() {
			global $woocommerce;
			?>
			<h3><?php echo $this->admin_page_heading; ?></h3>
			<p><?php echo $this->admin_page_description; ?></p>
			<table class="form-table">
			<?php
			    // Generate the HTML For the settings form.
			    $this->generate_settings_html();
			    ?>

		      <tr valign="top">
			  <th scope="row" class="titledesc"><?php _e('Import City', 'woothemes') ?></th>
			  <td class="forminp">
			    <input type="file" name="woocommerce_jne_free_import_city" id="woocommerce_jne_free_import_city" style="min-width:50px;" value="<?php echo esc_attr( get_option( 'woocommerce_jne_free_main_city' ) ); ?>" />
			  </td>
		      </tr>
		      <tr valign="top">
			  <th scope="row" class="titledesc"><?php _e('Data', 'woothemes') ?></th>
			  <td class="forminp">
			    <?php $shipping_cities = get_option( 'woocommerce_jne_free_data' ); ?>
			    <?php if(count($shipping_cities['cost_data']) > 0 && isset($shipping_cities['cost_data'])){ ?>
			      <?php foreach($shipping_cities['cost_data'] as $shipping_city){ ?>
				<p style="background-color:#dedede;display:block;width:150px;float:left;padding:5px;margin:5px;">
				  <?php echo $shipping_city['city']; ?><br/>
				  <?php echo $shipping_city['price']; ?>
				</p>
				<?php $city_counter++; ?>
			      <?php } ?>
			      <div style="clear:both;"></div>
			      <a href="<?php echo $shipping_cities['file_path']; ?>">Backup Data</a>
			    <?php }else{ ?>
			      Empty
			    <?php } ?>
			  </td>
		      </tr>
		      <tr>
			  <th scope="row" class="titledesc"><?php _e('Credits', 'woothemes') ?></th>
		          <td><a href="<?php echo esc_url( home_url( '/' ) ); ?>wp-content/plugins/woocommerce-jne-free/data.csv">Download Example Data jne_free</a> | <a href="http://www.agenwebsite.com/" target="_blank">WooCommerce JNE Shipping</a> | <a href="http://youtu.be/bAGrwbyRrZk">How to Settings WooCommerce JNE</a> | <a href="http://youtu.be/5qQQRXzQ3lc" target="_blank">How to Post Products</a></td>
		      </tr>
			</table><!--/.form-table-->
			<?php
		    } // End admin_options()
		     
		  function process_jne_free() {
		    // Save the rates
		    $cities_cost = array();
		 
		    if($_FILES["woocommerce_jne_free_import_city"]["type"] == 'text/csv'){
		      $upload_dir = wp_upload_dir();
		      move_uploaded_file( $_FILES["woocommerce_jne_free_import_city"]["tmp_name"], $upload_dir['path'] . '/data.csv');
		 
		      $fd = fopen ($upload_dir['path'] . '/data.csv', "r");
		      $city_counter = 0;
		      while (!feof ($fd)) {
			$buffer = fgetcsv($fd, filesize( $upload_dir['path'] . '/data.csv' ) );
			if(!empty($buffer[0]) && !empty($buffer[1])){
			  $city_name = strtolower(preg_replace('/\s+/', '-', $buffer[0]));
			  $cities_cost[$city_name] = array('city' => $buffer[0], 'price' => preg_replace('/\s+/', '', $buffer[1]));
			  
			  $city_counter++;
			}
		      }
		      fclose ($fd);
		       
		      $jne_free_options['file_path'] = $upload_dir['url'] . '/data.csv';
		      $jne_free_options['cost_data'] = $cities_cost;
		 
		      update_option( $this->jne_free_option, $jne_free_options );
		    }
		 
		    $this->get_jne_free();
		  }
		 
		  function get_jne_free() {
		    $this->jne_free = array_filter( (array) get_option( $this->jne_free_option ) );
		  }

		  function is_available( $package ) {
			global $woocommerce;
			$is_available = true;

			if ( $this->enabled == 'no' ) {
				$is_available = false;
			} else {
				$ship_to_countries = '';
				$shipping_cities = get_option( 'woocommerce_jne_free_yes_data' );
				$kota = $package['destination']['state'];


				if ( $this->availability == 'specific' ) {
					$ship_to_countries = $this->countries;
				} elseif ( get_option( 'woocommerce_allowed_countries' ) == 'specific' ) {
					$ship_to_countries = get_option( 'woocommerce_specific_allowed_countries' );
				}

				if ( is_array( $ship_to_countries ) && ! in_array( $package['destination']['country'], $ship_to_countries ) ) {
					$is_available = false;
				}
				
			}

			return apply_filters( 'woocommerce_shipping_' . $this->id . '_is_available', $is_available, $package );
		  }

		}
		 
		function add_jne_free_method( $methods ) {
		    $methods[] = 'WC_JNE_FREE'; return $methods;
		}
		add_filter('woocommerce_shipping_methods', 'add_jne_free_method' );
		 
		function add_states($states){
		  $shipping_cities = get_option( 'woocommerce_jne_free_data' );
		   
		  if($shipping_cities) {asort($shipping_cities['cost_data']);}
		 
		  if(is_array($shipping_cities['cost_data']) && count($shipping_cities['cost_data']) > 0){
		    foreach($shipping_cities['cost_data'] as $key => $city){
		      $new_states[$key] = $city['city'];
		    }
		  }
		   
		  $states['ID'] = $new_states;
		   
		  return $states;
		}
		 
		function modified_address_fields($address_fields){        
		  unset($address_fields['billing_postcode']);
		  unset($address_fields['billing_city']);
		   
		   
		  unset($address_fields['shipping_postcode']);
		  unset($address_fields['shipping_city']);
		   
		  return $address_fields;
		}
		 
		function rename_state($address_fields){
		  $address_fields['state']['label'] = __('Kota/Propinsi', 'woocommerce');
		  $address_fields['state']['placeholder'] = __('Pilih Kota/Propinsi Anda', 'woocommerce');
		   
		  return $address_fields;
		}

		if(!function_exists('zy_add_rand_currency_symbol') && !function_exists('zy_add_rand_currency')) {
			function zy_add_rand_currency_symbol( $symbol ) {
				$currency = get_option( 'woocommerce_currency' );
				switch( $currency ) {
					case 'RP': $symbol = 'Rp '; break;
				}
				return $symbol;
			}
			function zy_add_rand_currency( $currencies ) {
			    $currencies['RP'] = __( 'Indonesian Rupiah (RP)', 'woothemes' );
			    return $currencies;
			}
			add_filter('woocommerce_currencies', 'zy_add_rand_currency' );
			add_filter('woocommerce_currency_symbol', 'zy_add_rand_currency_symbol' );
		}

		function jne_shipping_credits() {
			$enable = get_option ( 'woocommerce_jne_free_settings' );
			if($enable['credits'] == 'yes') {
				$ab = '<div align="right">Plugin <a href="http://www.agenwebsite.com/plugin-woocommerce-jne-shipping-indonesia-free-version.html" target="_blank" title="Download WooCommerce JNE Shipping">WooCommerce JNE Shipping</a> by <a href="http://www.agenwebsite.com/" target="_blank" title="AgenWebsite.com">AgenWebsite</a>';			
				echo $ab;
			}
		}

		add_action('woocommerce_after_checkout_form', 'jne_shipping_credits');

		add_filter('woocommerce_shipping_fields', 'modified_address_fields');
		add_filter('woocommerce_get_country_locale_default', 'rename_state');
		add_filter('woocommerce_get_country_locale_default', 'modified_address_fields');
		add_filter('woocommerce_states', 'add_states',10,1);
	}

}
?>
