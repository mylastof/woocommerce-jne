jQuery(function($) {
	var billing_city = get_city('billing');
	var shipping_city = get_city('shipping');

	$('#shipping_state, #shipping_city').change( function(){
		$( 'body' ).trigger( 'update_checkout' );
	});

	function get_city(type) {
		if(type == 'billing')
			$city = $( '#billing_city' );
		else
			$city = $( '#shipping_city' );

		return $city.val();
	}
	
	function get_state(type) {
		if(type == 'billing')
			$state = $( '#billing_state' );
		else
			$state = $( '#shipping_state' );

		return $state.find('option:selected').text();
	}

	function get_country(type) {
		if(type == 'billing') {
			if( $('#billing_country').is( 'select' ) )
				var country = $('#billing_country').find('option:selected').val();
			else
				var country = $('#billing_country').val();
		}
		else {
			if( $('#shipping_country').is( 'select' ) )
				var country = $('#shipping_country').find('option:selected').val();
			else
				var country = $('#shipping_country').val();
		}
		return country;
	}

	function change_select(type) {
		if(type == 'billing')
			$( 'body' ).trigger( 'load_billing_city', [billing_state, $( '#billing_state' ).closest( 'div' )] );
		else
			$( 'body' ).trigger( 'load_shipping_city', [shipping_state, $( '#shipping_state' ).closest( 'div' )] );
	}

	function change_city(type) {
		if(type == 'billing') {
			var city = billing_city;
			$city = $( '#billing_city' );
		} else {
			var city = shipping_city;
			$city = $( '#shipping_city' );
		}

		$cityparent = $city.parent();
		var city_name = $city.attr( 'name' ),
			city_id = $city.attr( 'id' ),
			placeholder = 'Town / City';
		var state = get_state(type);
		var country = get_country(type);
		var datacity = jne_city[state];

		if(country == 'ID') {
			if ( $city.is( 'input' ) ) {
				$city.replaceWith( '<select name="' + city_name + '" id="' + city_id + '" class="city_select" placeholder="' + placeholder + '"></select>' );
				$city = $( '#billing_state' ).closest( 'div' ).find( '#billing_city, #shipping_city' );
			}

			$city.empty();
			$city.append( '<option value="">City</option>' );
			if(datacity != undefined) {
				$.each(datacity, function(i){
					if(datacity[i] == city) {
						$city.append($("<option></option>")
                    					.attr("value",datacity[i])
							.attr("selected","selected")
                    					.text(datacity[i]));
					} else {
						$city.append($("<option></option>")
                    					.attr("value",datacity[i])
                    					.text(datacity[i]));
					}
				});
			} 

			change_select(type);
		} else {
			$cityparent.show().find( '.chosen-container, .select2-container' ).remove();
			$city.replaceWith( '<input type="text" class="input-text" name="' + city_name + '" id="' + city_id + '" placeholder="' + placeholder + '" />' );
		}
	}

	$('#billing_state, #billing_country').change( function(){
		change_city('billing');
	});

	$('#shipping_state, #shipping_country').change( function(){
		change_city('shipping');
	});

	$( document ).ready(function() {
		change_city('billing');
		change_city('shipping');
	});

});