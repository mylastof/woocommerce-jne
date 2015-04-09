jQuery(document).ready(function($) {
	$city = $( '#calc_shipping_city' );
	$cityparent = $city.parent();
	var city_name = $city.attr( 'name' ),
		city_id = $city.attr( 'id' ),
		value = $city.val(),
		placeholder = 'Town / City';

	$('#calc_shipping_country').change( function(){
		$city = $( '#calc_shipping_city' );

		var country = $(this).find('option:selected').val();

		if(country != 'ID') {
			$cityparent.show().find( '.chosen-container, .select2-container' ).remove();
			$city.replaceWith( '<input type="text" class="input-text" name="' + city_name + '" id="' + city_id + '" placeholder="' + placeholder + '" />' );
		} else if(country == 'ID') {
			if ( $city.is( 'input' ) ) {
				$city.replaceWith( '<select name="' + city_name + '" id="' + city_id + '" class="city_select" placeholder="' + placeholder + '"></select>' );
				$city = $( this ).closest( 'div' ).find( '#calc_shipping_city' );
			}
			$city.append( '<option value="">Select an option</option>' );

			if(typeof yit_woocommerce != 'undefined') mindig_update();
		}
	});

	$( document ).on( 'change', 'select.shipping_method, input[name^=shipping_method]', function() {
		$city = $( '#calc_shipping_city' );

		var state = $('#calc_shipping_state').find('option:selected').text();
		var datacity = jne_city[state];
		var country = $('#calc_shipping_country').find('option:selected').val();

		if(country == 'ID') {
			if ( $city.is( 'input' ) ) {
				$city.replaceWith( '<select name="' + city_name + '" id="' + city_id + '" class="city_select" placeholder="' + placeholder + '"></select>' );
				$city = $( this ).closest( 'div' ).find( '#calc_shipping_city' );
			}

			$city.empty();
			$city.append( '<option value="">Select a city</option>' );

			$.each(datacity, function(i){
				$city.append($("<option></option>")
                	    		.attr("value",datacity[i])
                 	   		.text(datacity[i]));
			});

			if(typeof yit_woocommerce != 'undefined') mindig_update();
		}
	});

	$('#calc_shipping_state').change( function(){
		$city = $( '#calc_shipping_city' );

		var state = $(this).find('option:selected').text();
		var datacity = jne_city[state];
		var country = $('#calc_shipping_country').find('option:selected').val();

		if(country == 'ID') {
			if ( $city.is( 'input' ) ) {
				$city.replaceWith( '<select name="' + city_name + '" id="' + city_id + '" class="city_select" placeholder="' + placeholder + '"></select>' );
				$city = $( this ).closest( 'div' ).find( '#calc_shipping_city' );
			}

			$city.empty();
			$city.append( '<option value="">Select a city</option>' );

			$.each(datacity, function(i){
				$city.append($("<option></option>")
                	    		.attr("value",datacity[i])
                 	   		.text(datacity[i]));
			});

			if(typeof yit_woocommerce != 'undefined') mindig_update();
		}
	});

	$( document ).ready(function() {
		$city = $( '#calc_shipping_city' );

		var currentcity = $city.val();
		var state = $('#calc_shipping_state').find('option:selected').text();
		var datacity = jne_city[state];
		var country = $('#calc_shipping_country').find('option:selected').val();

		if(country == 'ID') {
			if ( $city.is( 'input' ) ) {
				$city.replaceWith( '<select name="' + city_name + '" id="' + city_id + '" class="city_select" placeholder="' + placeholder + '"></select>' );
				$city = $( '#calc_shipping_state' ).closest( 'div' ).find( '#calc_shipping_city' );
			}

			$city.empty();
			$city.append( '<option value="">Select a city</option>' );

			if( typeof datacity != 'undefined' )
				$.each(datacity, function(i){
					$city.append($("<option></option>")
                    				.attr("value",datacity[i])
                    				.text(datacity[i]));
                  		});
				

			if(typeof yit_woocommerce != 'undefined') mindig_update();
		}
	});

	function mindig_update() {
		$city = $( '#calc_shipping_city' );

		$city.select2();
	}
});