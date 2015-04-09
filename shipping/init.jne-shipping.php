<?php
/**
 * WooCommerce JNE Shipping
 *
 * File main menambahkan class jne shipping ke woocommerce
 *
 * @author	Fairuz Fatin
 * @category	Core
 * @package	WooCommerce JNE Shipping
 */
	class WC_JNE extends WC_Shipping_Method {
		
		function __construct() { 
			$this->id			= 'jne_shipping';
			$this->method_title		= __('JNE Shipping', 'woothemes');
			$this->jne_save			= 'woocommerce_jne_shipping_data_save';
			$this->jne_free_option		= 'woocommerce_jne_free_data';
			$this->admin_page_heading	= __( 'JNE Shipping', 'woocommerce' );
			$this->admin_page_description	= __( 'JNE let you define a standard rate per item, or per order.', 'woocommerce' );
		 
			add_action( 'woocommerce_update_options_shipping_' . $this->id, array( &$this, 'process_admin_options' ) );
			add_action( 'woocommerce_update_options_shipping_' . $this->id, array( &$this, 'process_jne' ) );		 
			    		     
			$this->init();
		} 
		     
		function init() {
			// Load the form fields.
			$this->init_form_fields();
		 
			// Load the settings.
			$this->init_settings();
		 
			// Define user set variables
			$this->enabled                 = $this->settings['enabled'];
			$this->title                   = $this->settings['title'];
			$this->jne_weight              = $this->settings['jne_weight'];
		}
		 
		function init_form_fields() {
			global $woocommerce;

			$this->form_fields = array(
				'enabled' => array(
					'title'         => __( 'Aktifkan/Non-aktifkan', 'woocommerce' ), 
					'type'          => 'checkbox', 
					'label'         => __( 'Aktifkan WooCommerce JNE Shipping', 'woocommerce' ), 
					'default'       => 'yes',
					), 
				'title' => array(
					'title'         => __( 'Judul', 'woocommerce' ), 
					'description' 	=> __( 'Tambahkan judul untuk fitur pengiriman kamu.', 'woocommerce' ),
					'desc_tip'	=> true,
					'type'          => 'text',
					'default'       => __( 'JNE Shipping', 'woocommerce' ),
					),
				'jne_weight' => array(
					'title'         => __( 'Berat default', 'woocommerce' ), 
					'description' 	=> __( 'Otomatis setting berat produk jika kamu tidak setting pada masing-masing produk.', 'woocommerce' ),
					'desc_tip'	=> true,
					'type'          => 'number',
					'default'       => __( '0.25', 'woocommerce' ),
					'custom_attributes' => array(
								'step'	=> 'any',
								'min'	=> '0'
						),
					'placeholder'	=> '0.00',
					'default'		=> '1',
					),
				'jne_service' => array(
					'type'          => 'jne_service',
					),
				'jne_import' => array(
					'type'          => 'jne_import',
					),
				'jne_free_shipping' => array(
					'type'          => 'jne_free_shipping',
					),
				'free_shipping_city' => array(
					'type'          => 'free_shipping_city',
				),
				'jne_credit' => array(
					'type'          => 'jne_credit',
					),
			);
		     
		}
		 
		public function admin_options() {
			global $woocommerce;
			$jne_data = get_option( 'woocommerce_jne_free_data' );
		?>
			<h3><?php echo $this->admin_page_heading; ?></h3>
			<p><?php echo $this->admin_page_description; ?></p>

			<p><img src="http://www.agenwebsite.com/images/agenwebsite_jne.png" /></p>

			<?php if(empty($jne_data['cost_data'])) { ?>
				<iframe src="http://www.agenwebsite.com/check.php?product=woocommerce_jne_free&action=kota" width="100%" border="0" height="60px" scrolling="0"></iframe>
			<?php } else { ?>
				<iframe src="http://www.agenwebsite.com/check.php?product=woocommerce_jne_free&version=<?php echo jne_version; ?>" width="100%" border="0" height="60px" scrolling="0"></iframe>
			<?php } ?>

			<table class="form-table hide-data">
			<?php
				$this->generate_settings_html();
			?>

			</table><!--/.form-table-->

			<script type="text/javascript">
			jQuery(function($) {
				$('#jne_delete').click(function(){
					var answer = confirm("Hapus semua data jne?")
					if (!answer) {
						return false;
					}
				});
				jQuery('.for-full-user').click(function(){
					alert("Pengaturan ini tidak berfungsi untuk free version. Jika Anda membutuhkan fungsi ini, anda harus mengupgrade ke full version.");
					return false;
			});	
			});
			</script>

			<style>
			.premium-version { opacity: 0.7 }
			</style>
			<?php
		}

		public function generate_jne_service_html() {
			ob_start();
		?>
		<tr valign="top" class="premium-version">
			<th scope="row" class="titledesc"><?php _e( 'Layanan JNE', 'woocommerce' ); ?><p><strong><a href="http://www.agenwebsite.com/products/woocommerce-jne-shipping" class="help_tip" data-tip="Dapatkan fitur ini dengan membeli full version." target="_blank">Premium Version</a></strong></p></th>
			<td class="forminp" id="bacs_accounts">
				<table class="widefat wc_input_table" cellspacing="0">
					<thead>
						<tr>
							<th class="sort">&nbsp;</th>
							<th>Nama Pengiriman <img class="help_tip" data-tip="Metode pengiriman yang digunakan." src="<?php echo plugins_url( 'images/help.png', __FILE__ ); ?>" height="16" width="16" style="float:none;" /></th>
							<th>Biaya Packing <img class="help_tip" data-tip="Biaya tambahan, bisa disetting untuk tambahan biaya packing dan lain-lain." src="<?php echo plugins_url( 'images/help.png', __FILE__ ); ?>" height="16" width="16" style="float:none;" /></th>
							<th style="width:14%;text-align:center;">Asuransi <img class="help_tip" data-tip="Kalkulasi biaya tambahan untuk asuransi pengiriman produk Anda." src="<?php echo plugins_url( 'images/help.png', __FILE__ ); ?>" height="16" width="16" style="float:none;" /></th>
							<th style="width:14%;text-align:center;">Aktifkan</th>
						</tr>
					</thead>
					<tbody>	
						<tr class="service for-full-user">
							<td class="sort for-full-user"></td>
							<td><input type="text" value="OKE" disabled="disabled" /></td>
							<td><input type="number" value="0" disabled="disabled" /></td>
							<td style="text-align:center;"><input type="checkbox" value="1" class="for-full-user" /></td>
							<td style="text-align:center;"><input type="checkbox" value="1" class="for-full-user" /></td>
						</tr>
						<tr class="service for-full-user">
							<td class="sort for-full-user"></td>
							<td><input type="text" value="REG" disabled="disabled" /></td>
							<td><input type="number" value="0" disabled="disabled" /></td>
							<td style="text-align:center;"><input type="checkbox" value="1" class="for-full-user" /></td>
							<td style="text-align:center;"><input type="checkbox" value="1" class="for-full-user" checked='checked' /></td>
						</tr>
						<tr class="service for-full-user">
							<td class="sort for-full-user"></td>
							<td><input type="text" value="YES" disabled="disabled"/></td>
							<td><input type="number" value="0" disabled="disabled"/></td>
							<td style="text-align:center;"><input type="checkbox" value="1" class="for-full-user" /></td>
							<td style="text-align:center;"><input type="checkbox" value="1" class="for-full-user" /></td>
						</tr>
					</tbody>
				</table>
            		</td>
		</tr>
		<?php
			return ob_get_clean();
		}

		public function generate_jne_import_html() {
			ob_start();
			$jne_data = get_option( 'woocommerce_jne_free_data' );
		?>
		<tr valign="top">			  	
			<th scope="row" class="titledesc"><?php _e('Impor Kota', 'woojne') ?> <img class="help_tip" data-tip="Masukan file csv untuk menginput data kota kamu." src="<?php echo plugins_url( 'images/help.png', __FILE__ ); ?>" height="16" width="16" /></th>
			<td>
				<p><input type="file" name="woocommerce_jne_import_city" id="woocommerce_jne_import_city" style="min-width:393px;" /> <input name="save" class="button-primary help_tip" data-tip="Klik untuk melakukan upload data kota." class="button-primary" type="submit" value="Upload Data Kota"> <a href="http://www.agenwebsite.com/?add-to-cart=5318" class="button-primary help_tip" data-tip="Silahkan lengkapi form checkout untuk mendapatkan data kota." target="_blank">Download Data Kota</a>
</p>
				<?php if(!empty($jne_data['cost_data'])) { ?>
					<p style="background: #FFF;  border-left: 4px solid #FFF;  -webkit-box-shadow: 0 1px 1px 0 rgba(0, 0, 0, 0.1);  box-shadow: 0 1px 1px 0 rgba(0, 0, 0, 0.1);  margin: 5px 0 2px;  padding: 5px 12px;  border-color: #7AD03A;">Anda telah melakukan upload data, dan jika Anda ingin <u>mengedit data kota</u>, Anda bisa mengedit file csv terlebih dahulu.</p>
				<?php } else { ?>
					<p style="background: #FFF;  border-left: 4px solid #FFF;  -webkit-box-shadow: 0 1px 1px 0 rgba(0, 0, 0, 0.1);  box-shadow: 0 1px 1px 0 rgba(0, 0, 0, 0.1);  margin: 5px 0 2px;  padding: 5px 12px;  border-color: #D54E21;">Anda belum melakukan upload data kota. <a href="http://docs.agenwebsite.com/?url=WooCommerce%20JNE%20Shipping" target="_blank" class="help_tip" data-tip="Klik untuk melihat dokumentasi terbaru.">Cara upload data kota &raquo;</a></p>
				<?php } ?>
			</td>
		</tr>
		<tr valign="top" class="premium-version">
			<td></td>	
			<td>
				<a href="#" class="button-primary help_tip for-full-user" data-tip="Anda bisa mengedit data kota langsung melalui fitur ini.">Edit Data Kota</a>
				<a href="#" class="button-primary help_tip for-full-user" data-tip="Backup data ongkos kirim agar bisa Anda digunakan kembali.">Backup Data</a>
				<input type="submit" class="button-primary help_tip" id="jne_delete" name="jne_delete" data-tip="Ketika Anda mengklik dan mengkonfirmasikannya Plugin ini akan menghapus semua data tarif." value="Hapus Semua Data">
				<p><strong><a href="http://www.agenwebsite.com/products/woocommerce-jne-shipping" class="help_tip" data-tip="Dapatkan fitur ini dengan membeli full version." target="_blank">Premium Version</a></strong></p>
			</td>   
		</tr>  
		<?php
			return ob_get_clean();
		}

		public function generate_jne_free_shipping_html() {
			ob_start();
		?>

		<tr valign="top">
			<th scope="row" class="titledesc" colspan="2">
				<h3><?php _e( 'JNE Free Shipping ( Extensions )', 'woocommerce' ); ?></h3>
				<p>Anda bisa menambahkan pengiriman gratis untuk layanan jne Anda.</p>
			</th>
		</tr>

		<?php
			return ob_get_clean();
		}

		public function generate_free_shipping_city_html() {
			ob_start();
		?>

		<tr valign="top" class="premium-version for-full-user">
			<th scope="row" class="titledesc">
				<label for="woocommerce_jne_shipping_free_shipping_enabled">Aktifkan/Non-aktifkan</label>
				<p><strong><a href="http://www.agenwebsite.com/products/woocommerce-jne-shipping" class="help_tip" data-tip="Dapatkan fitur ini dengan membeli full version." target="_blank">Premium Version</a></strong></p>
			</th>
			<td class="forminp" style="vertical-align:top;">
				<fieldset>
					<legend class="screen-reader-text"><span>Aktifkan/Non-aktifkan</span></legend>
					<label for="woocommerce_jne_shipping_free_shipping_enabled">
					<input  class="" type="checkbox"/> Aktifkan Free Shipping</label><br/>
				</fieldset>
			</td>
		</tr>
		<tr valign="top" class="premium-version for-full-user">
			<th scope="row" class="titledesc">
				<label for="jne_shipping_free_shipping_city">Pilihan Kota Gratis</label> <img class="help_tip" data-tip="Silahkan pilih kota tujuan untuk metode pengiriman gratis." src="<?php echo plugins_url( 'images/help.png', __FILE__ ); ?>" height="16" width="16" />
				<p><strong><a href="http://www.agenwebsite.com/products/woocommerce-jne-shipping" class="help_tip" data-tip="Dapatkan fitur ini dengan membeli full version." target="_blank">Premium Version</a></strong></p>
			</th>
			<td class="forminp" style="vertical-align:top;">
				<fieldset>
					<legend class="screen-reader-text"><span>Pilihan Kota Gratis</span></legend>
					<select multiple="multiple" class="multiselect chosen_select ajax_chosen_select_city" name="jne_shipping_free_shipping_city[]" id="jne_shipping_free_shipping_city" style="width: 450px;" data-placeholder="Pilih kota&hellip;">
					</select>
					<p><a class="select_all button" href="#">Select all</a> <a class="select_none button" href="#">Select none</a></p>
				</fieldset>
			</td>
		</tr>
		<tr valign="top" class="premium-version for-full-user">
			<th scope="row" class="titledesc">
				<label for="woocommerce_jne_shipping_free_shipping_service">Layanan Gratis</label>
				<img class="help_tip" data-tip="Pilih layanan metode pengiriman untuk pengiriman gratis." src="http://dev.agenwebsite.com/jne/wp-content/plugins/woocommerce/assets/images/help.png" height="16" width="16" />
				<p><strong><a href="http://www.agenwebsite.com/products/woocommerce-jne-shipping" class="help_tip" data-tip="Dapatkan fitur ini dengan membeli full version." target="_blank">Premium Version</a></strong></p>
			</th>
			<td class="forminp" style="vertical-align:top;">
				<fieldset>
					<legend class="screen-reader-text"><span>Layanan Gratis</span></legend>
					<select class="select chosen_select" name="woocommerce_jne_shipping_free_shipping_service" id="woocommerce_jne_shipping_free_shipping_service" style="width: 450px;"  >
						<option value="reg" >REG</option>
						<option value="yes" >YES</option>
						<option value="oke" >OKE</option>
					</select>
				</fieldset>
			</td>
		</tr>
		<?php
			return ob_get_clean();
		}

		public function generate_jne_credit_html() {
			ob_start();
		?>
		<tr valign="top">
			<td></td>
			<td>Developed By <a href="http://www.agenwebsite.com/" target="_blank" title="AgenWebsite.com - One Stop eCommerce Solutions" class="help_tip" data-tip="AgenWebsite.com - One Stop eCommerce Solutions.">AgenWebsite</a> | <a href="http://docs.agenwebsite.com/?url=WooCommerce%20JNE%20Shipping" target="_blank" class="help_tip" data-tip="Klik untuk melihat dokumentasi terbaru.">See Documentation</a> | <a href="http://www.agenwebsite.com/products/woocommerce-jne-shipping" target="_blank" class="help_tip" data-tip="Dapatkan fitur ini dengan membeli full version.">Buy Full Version</a></td>      
		</tr> 
		<?php
			return ob_get_clean();
		}
		     
		function calculate_shipping( $package = array() ) {
			global $woocommerce;

			$this->shipping_total  = 0;
			$this->shipping_tax    = 0;

			$cost = 0;
			$total_weight = 0;
		       	$shipping_cities = '';

			$city = $woocommerce->customer->get_shipping_city();
		       
			$shipping_cities = get_option( 'woocommerce_jne_free_data' );

			$shipping_price = $shipping_cities['cost_data'][$city];
		 
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
		      
			if(!is_int($total_weight)) {
				$jne_weight = explode('.',$total_weight);
					if($jne_weight[1] <= 3 && $jne_weight[1] != "") {
						$total_weight = ceil($total_weight) - 1;
						if($total_weight == 0) {
							$total_weight = 1;
						}
					} else {
						$total_weight = ceil($total_weight);
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

		function process_jne() {
			include_once 'process.jne-shipping.php';
		}

		function get_jne() {
			$this->jne_free = array_filter( (array) get_option( $this->jne_free_option ) );
		}

		function is_available( $package ) {
			global $woocommerce;
			$is_available = true;

			if ( $this->enabled == 'no' ) {
				$is_available = false;
			} else {
				$ship_to_countries = '';
				$kota = $package['destination']['state'];

				if ( $this->availability == 'specific' ) {
					$ship_to_countries = $this->countries;
				} elseif ( get_option( 'woocommerce_allowed_countries' ) == 'specific' ) {
					$ship_to_countries = get_option( 'woocommerce_specific_allowed_countries' );
				}

				if ( is_array( $ship_to_countries ) && ! in_array( $package['destination']['country'], $ship_to_countries ) ) {
					$is_available = true;
				}
				
			}

			return apply_filters( 'woocommerce_shipping_' . $this->id . '_is_available', $is_available, $package );
		}
	}
		 
	function add_jne_shipping_method( $methods ) {
		$methods[] = 'WC_JNE'; return $methods;
	}
	add_filter('woocommerce_shipping_methods', 'add_jne_shipping_method' );
?>