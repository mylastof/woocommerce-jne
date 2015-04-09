jQuery(function($) {

	if(typeof jne_version !== 'undefined') {
		var trigger_chosen = 'liszt:updated';
	} else {
		var trigger_chosen = 'chosen:updated';
	}

	$( 'body' ).bind( 'state_to_city_changed', function() {
		if($().chosen)
			$( 'select#billing_city, select#shipping_city' ).chosen().trigger( trigger_chosen );
		else if($().select2)
			$( 'select#billing_city, select#shipping_city' ).select2();
	});
	$( 'body' ).bind( 'country_to_city_changed', function() {
		if($().chosen)
			$( 'select#billing_city, select#shipping_city' ).chosen().trigger( trigger_chosen );
		else if($().select2)
			$( 'select#billing_city, select#shipping_city' ).select2();
	});
	$( 'body' ).bind( 'load_billing_city', function() {
		if($().chosen)
			$( 'select#billing_city' ).chosen().trigger( trigger_chosen );
		else if($().select2)
			$( 'select#billing_city' ).select2();
	});
	$( 'body' ).bind( 'load_shipping_city', function() {
		if($().chosen)
			$( 'select#shipping_city' ).chosen().trigger( trigger_chosen );
		else if($().select2)
			$( 'select#shipping_city' ).select2();
	});	

	// wc_checkout_params is required to continue, ensure the object exists
	if (typeof wc_checkout_params === "undefined")
		return false;

	if($().chosen)
		$("select#billing_city, select#shipping_city").chosen( { search_contains: true } );
	else if($().select2)
		$("select#billing_city, select#shipping_city").select2();

});