<?php
/*
  Plugin Name: WooCommerce Rocket.Chat Order Notifications
  Plugin URI: https://www.proxeuse.com/en/wordpress-plugins/woocommerce/rocket-chat-order-notifications/
  Description: This plugin will notify you of new WooCommerce orders by posting in one of your Rocket.Chat channels. You can adjust a lot to meet your requirements.
  Version: 1.0.0
  Author: Proxeuse
  Author URI: https://www.proxeuse.com/
  License: GPLv2 or later
  Text Domain: woocommerce-rocket-chat-order-notifications
*/

/**
 * Massive thanks to LoicTheAztec (StackOverFlow) for sharing his WooCommerce hook.
 * His code: https://stackoverflow.com/a/42533543
 * GitHub: https://github.com/lomars
 */
 add_action('woocommerce_thankyou', 'rocketchatapi', 10, 1);
 function rocketchatapi( $order_id ) {
   if ( ! $order_id )
    return;

   // Allow code execution only once
   if( ! get_post_meta( $order_id, '_thankyou_action_done', true ) ) {

     // Set the variables below:
     $rocketChatURL = "http://127.0.0.1:3000";
     $rocketChatUsername = "woocommerce";
     $rocketChatPassword = "PasswordHere";
     $rocketChatChannel = "#woocommerce";
     $rocketChatAlias = "WooCommerce Bot";

     // Get an instance of the WC_Order object
     $order = wc_get_order( $order_id );

    // Request User ID and Auth Token by Rocket.Chat API
    // start curl request
 		$ch = curl_init();
    // set curl url
 		curl_setopt($ch, CURLOPT_URL, "$rocketChatURL/api/v1/login");
    // set POST variables
 		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array("user"=>$rocketChatUsername, "password"=>$rocketChatPassword)));
    // we want a response
 		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // execute curl command and save to $authOutput
 		$authOutput = curl_exec($ch);
    // close curl connection
 		curl_close($ch);
    // decode authOutput so we can get data from it
 		$authArray = json_decode($authOutput, true);

 		// save userid and authtoken to be used later
 		$userid = $authArray['data']['userId'];
 		$authtoken = $authArray['data']['authToken'];

		// get customers personal information
		$fullname = $order->get_formatted_billing_full_name();
		$company = $order->get_billing_company();
		$country = $order->get_billing_country();

		// get order totals
		$ordersubtotal = html_entity_decode(strip_tags($order->get_subtotal_to_display()));
		$odervat = html_entity_decode(strip_tags(wc_price($order->get_total_tax())));
		$ordertotal = html_entity_decode(strip_tags($order->get_formatted_order_total()));
		$discount = html_entity_decode(strip_tags($order->get_discount_to_display()));

		// get other data
		$ordercount = $order->get_item_count();
		$orderid = $order->get_id();
		$orderURL = $order->get_view_order_url();

		// set start of message
		$message = "Hi there! A new order has been placed by $fullname ($company) from $country.\n The client has ordered $ordercount product(s): ";

		// Loop through order items
		foreach ( $order->get_items() as $item_id => $item ) {
      // if only one product in order
			if($ordercount = 1){
				// add product name to the message
				$message .= $item->get_name();
			}
      // if more products in order
			else{
        // add product to array
				$productsArray[] = $item->get_name();
			}
		}
    // implode array to string
    $products = implode(",", $productsArray);
    // add string to message
    $message .= $products;

		// add a link to WooCommerce view order page
		$message .= "\n $orderURL \n \n";

		// if discount applies
		if($order->get_discount_total() > 0){
      // add to message
			$message .= "*Subtotal:* $ordersubtotal \n *Discount:* $discount \n *Taxes:* $odervat \n *Total:* $ordertotal \n";
		}
    // no discount
		else{
      // add to message
			$message .= "*Subtotal:* $ordersubtotal \n *Taxes:* $odervat \n *Total:* $ordertotal \n";
		}

		// Send message to Rocket.Chat channel by using the Rocket.Chat API
    // start curl request
 		$ch = curl_init();
    // set curl url
 		curl_setopt($ch, CURLOPT_URL, "$rocketChatURL/api/v1/chat.postMessage");
    // set POST variables
 		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array("channel"=>$rocketChatChannel, "text"=>"$message", "alias"=>$rocketChatAlias)));
    // set http headers
 		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      // set auth token required by rocketchat api
      "X-Auth-Token: $authtoken",
      // set userid also required by rocketchat api
 		  "X-User-Id: $userid",
 		));
    // we want a response
 		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // execute curl command and save to $messageOutput
 		$messageOutput = curl_exec($ch);
    // close curl connection
 		curl_close($ch);
    // decode authOutput so we can get data from it
 		$messageArray = json_decode($messageOutput,true);

		// Flag the action as done (to avoid repetitions on reload for example)
		$order->update_meta_data( '_thankyou_action_done', true );
		$order->save();
	}
 }
