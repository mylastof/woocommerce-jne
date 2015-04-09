<?php
/**
 * WooCommerce JNE Shipping ( Process )
 *
 * Untuk memproses data yang masuk
 *
 * @author	ItsMeFurZy
 * @category	Core
 * @package	WooCommerce JNE Shipping
 */

	if($_FILES['woocommerce_jne_import_city']['error'] != UPLOAD_ERR_NO_FILE) {
		$cities_cost = array();
		$upload_path = $_FILES["woocommerce_jne_import_city"]["tmp_name"]; 
		$ext = strtolower(end(explode('.', $_FILES['woocommerce_jne_import_city']['name'])));

		if(!empty($upload_path) and $ext == 'csv') {
			$fd = fopen ($upload_path, "r");
			$city_counter = 0;
			while (!feof ($fd)) {
				$buffer = fgetcsv($fd, filesize( $upload_path ) );
				if(!empty($buffer[0])){
					$buffer[0] = $buffer[0];
					$buffer[1] = iconv( 'UTF-8', 'ISO-8859-15//TRANSLIT',$buffer[1]);

					$city_name = $buffer[0];
					$cities_cost[$city_name] = array( 'city' => $city_name, 'price' => $buffer[1] );
					$city_counter++;
				}
			}
			fclose ($fd);
			
			$jne_options['cost_data'] = $cities_cost;
			$jne_options['city_count'] = $city_counter;
			 
			update_option( $this->jne_free_option, $jne_options );
		} 
	}

	if(isset($_POST['jne_delete'])) {
		update_option( $this->jne_free_option, '' );
	}
	
	$this->get_jne();
?>