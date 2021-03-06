<?php

/**
 * Plugin Name: WooCommerce Subscriptions - Hide Cancel Button Options
 * Plugin URI:  https://github.com/jrick1229/wcs-hide-cancel-options
 * Description: Gives options in the Subscriptions settings for hiding the 'Cancel' button on the My Account page.
 * Author:      jrick1229
 * Author URI:  http://joeyrr.com/
 * Version:     1.1.0
 * License:     GPLv3
 *
 * GitHub Plugin URI: jrick1229/wcs-hide-cancel-options
 * GitHub Branch: master
 *
 * Copyright 2018 Prospress, Inc.  (email : freedoms@prospress.com)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package WooCommerce Subscriptions
 * @author  Prospress Inc.
 * @since   1.0.0
 */

/**
 * Add settings tab
 */

class wcs_hide_cancel_settings {
    
    public static $option_prefix = 'woocommerce_autocomplete_orders';
    
    public static function init() {
        add_filter( 'woocommerce_subscription_settings', __CLASS__ . '::add_settings', 10, 1 );
    }

    public static function add_settings( $settings ) {

		return array_merge( $settings, array(
			array(
				'name'     => __( 'Cancel Button Options', 'woocommerce-subs-cancel-button-options' ),
				'type'     => 'title',
				'id'       => self::$option_prefix,
			),
			array(
                'name'     => __( 'Cancel Button', 'woocommerce-subs-cancel-button-options' ),
                'desc'     => __( 'Choose when you would like to hide/show the Cancel button on the My Account page.' ),
                'id'       => self::$option_prefix,
                'css'      => 'min-width:150px;',
                'default'  => 'always',
                'type'     => 'select',
                'options'  => array(
                    'always_show'        => _x( "Always show", 'woocommerce-subs-cancel-button-options' ),
                    'sub_expiration'   => _x( "Hide if subscription has an 'End Date'", 'woocommerce-subs-cancel-button-options' ),
                    'always_hide' => _x( "Always hide", 'woocommerce-subs-cancel-button-options' )
                ),
                'desc_tip' => true,
            ),
			array( 'type' => 'sectionend', 'id' => self::$option_prefix ),
		) );
        
	}
        
}
wcs_hide_cancel_settings::init();


/**
 * What to do based on the selected option from above
 */

if ( 'always_show' == get_option( wcs_hide_cancel_settings::$option_prefix ) ) { 
}
elseif ( 'sub_expiration' == get_option( wcs_hide_cancel_settings::$option_prefix ) ) {

    /**
     * Remove the "cancel" button.
     *
     * @param array           $actions      Array of action buttons.
     * @param WC_Subscription $subscription The subscription object.
     *
     * @return array The filtered array of actions.
     */
    function eg_remove_my_subscriptions_button( $actions, $subscription ) {
        if ( $subscription->get_time( 'end' ) === 0 || $next_payment_timestamp > $subscription->get_time( 'end' ) ) {
            return $actions;
        }
        // Hide "Cancel" button.
        unset( $actions['cancel'] );
        return $actions;
    }
    add_filter( 'wcs_view_subscription_actions', 'eg_remove_my_subscriptions_button', 100, 2 );
    
}
elseif ( 'always_hide' == get_option( wcs_hide_cancel_settings::$option_prefix ) ) {
    function eg_remove_my_subscriptions_cancel_button( $actions, $subscription ) {
        foreach ( $actions as $action_key => $action ) {
            switch ( $action_key ) {
    			case 'cancel':
                    unset( $actions[ $action_key ] );
                    break;
                default: 
                    error_log( '-- $action = ' . print_r( $action, true ) );
                    break;
            }
        }
        return $actions;
    }
    add_filter( 'wcs_view_subscription_actions', 'eg_remove_my_subscriptions_cancel_button', 100, 2 );
}
else {}