<?php

/**
 * Plugin Name:     WooCommerce Bulk Shipping seperated per shipping class
 * Description:     Display different options on check out  for  different shipping classes
 * Author:          essenmitsosse
 * Version:         1.0.0
 * Author URI:      http://essenmitsosse.de
 * Text Domain:     woocommerce-bulk-shipping-seperated
 * Upgrade Check:   none
 * Last Change:     15.08.2015 14:00
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Woocommerce_Bulk_Shipping_Seperated {

	/**
     * Plugin version
     * @var string
     */
    static public $version = "1.0.0";

	/**
     * Singleton object holder
     * @var mixed
     */
    static private $instance = NULL;

    public function __construct() {
		add_filter( 'woocommerce_cart_shipping_packages', array( 'Woocommerce_Bulk_Shipping_Seperated', 'bulky_woocommerce_cart_shipping_packages' ) );
	}

    /**
	* Creates an Instance of this Class
	*
	* @access public
	* @since 1.0.0
	* @return Woocommerce_Variations_With_Radio_Buttons
	*/
	public static function get_instance() {

		if ( NULL === self::$instance )
			self::$instance = new self;

		return self::$instance;
	}

	public static function bulky_woocommerce_cart_shipping_packages ( $packages ) {
		// Reset the packages
		$packages = array();
	  
		// Bulky items
		$free_items   = array();
		$regular_items = array();
		
		// Sort free from others
		foreach ( WC()->cart->get_cart() as $item ) {
			if ( $item['data']->needs_shipping() ) {
				if ( $item['data']->get_shipping_class() == 'free' || $item['data']->get_shipping_class() == '' ) {
					$free_items[] = $item;
				} else {
					$regular_items[] = $item;
				}
			}
		}
		
		// Put inside packages
		if ( $free_items ) {
			$packages[] = array(
				'ship_via'        => array( 'free_shipping' ),
				'contents'        => $free_items,
				'contents_cost'   => array_sum( wp_list_pluck( $free_items, 'line_total' ) ),
				'applied_coupons' => WC()->cart->applied_coupons,
				'destination'     => array(
					'country'   => WC()->customer->get_shipping_country(),
					'state'     => WC()->customer->get_shipping_state(),
					'postcode'  => WC()->customer->get_shipping_postcode(),
					'city'      => WC()->customer->get_shipping_city(),
					'address'   => WC()->customer->get_shipping_address(),
					'address_2' => WC()->customer->get_shipping_address_2()
				)
			);
		}
		if ( $regular_items ) {
			$packages[] = array(
				'ship_via'        => array( 'flat_rate' ),
				'contents'        => $regular_items,
				'contents_cost'   => array_sum( wp_list_pluck( $regular_items, 'line_total' ) ),
				'applied_coupons' => WC()->cart->applied_coupons,
				'destination'     => array(
					'country'   => WC()->customer->get_shipping_country(),
					'state'     => WC()->customer->get_shipping_state(),
					'postcode'  => WC()->customer->get_shipping_postcode(),
					'city'      => WC()->customer->get_shipping_city(),
					'address'   => WC()->customer->get_shipping_address(),
					'address_2' => WC()->customer->get_shipping_address_2()
				)
			);
		} 
		
		return $packages;
	}
}

if ( class_exists( 'Woocommerce_Bulk_Shipping_Seperated' ) ) {

	add_action( 'plugins_loaded', array( 'Woocommerce_Bulk_Shipping_Seperated', 'get_instance' ) );

}
