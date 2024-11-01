<?php
/**
 * @package Zoranga
 * @version 1.0.0
 */
/*
Plugin Name: Zoranga WooCommerce payment gateway
Plugin URI: https://zoranga.com/
Description: A woocommerce paymernt gateway to help you receive airtime as payment
Author: Incofab Ikenna
Version: 1.0.0
Author URI: https://github.com/incofab/
*/

define("WC_ZORANGA_DEV", false);

// Include our Gateway Class and register Payment Gateway with WooCommerce
add_action( 'plugins_loaded', 'zoranga_init', 0 );

function zoranga_init() 
{
	// If the parent WC_Payment_Gateway class doesn't exist
	// it means WooCommerce is not installed on the site
	// so do nothing
	if ( ! class_exists( 'WC_Payment_Gateway' ) ) return;
	
	// If we made it this far, then include our Gateway Class
	include_once( 'wc_zoranga.php' );

	// Now that we have successfully included our class,
	// Lets add it too WooCommerce
	add_filter( 'woocommerce_payment_gateways', 'add_zoranga_payment_gateway' );

	function add_zoranga_payment_gateway( $methods ) {
		$methods[] = 'WC_Zoranga';
		return $methods;
	}
}

// Add custom action links
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'zoranga_action_links' );

function zoranga_action_links( $links ) 
{
	$plugin_links = array(
		'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout' ) . '">' . __( 'Settings', 'spyr-authorizenet-aim' ) . '</a>',
	);

	// Merge our new link with the default ones
	return array_merge( $plugin_links, $links );	
}

