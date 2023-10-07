<?php
/**
 * Plugin Name: Shipping Servientrega Panamá Woo
 * Description: Shipping Servientrega Panamá Woocommerce
 * Version: 1.0.0
 * Author: Saul Morales Pacheco
 * Author URI: https://saulmoralespa.com
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * WC tested up to: 6.9
 * WC requires at least: 6.9
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if(!defined('SHIPPING_SERVIENTREGA_PA_WC_SSP_VERSION')){
    define('SHIPPING_SERVIENTREGA_PA_WC_SSP_VERSION', '1.0.0');
}

add_action( 'plugins_loaded', 'shipping_servientrega_pa_wc_ssp_init', 1 );

function shipping_servientrega_pa_wc_ssp_init(){
    if ( ! shipping_servientrega_pa_wc_ssp_requirements() )
        return;

    shipping_servientrega_pa_wc_ssp()->run_servientrega_wc();
}

function shipping_servientrega_pa_wc_ssp_notices( $notice ) {
    ?>
    <div class="error notice">
        <p><?php echo $notice; ?></p>
    </div>
    <?php
}

function shipping_servientrega_pa_wc_ssp_requirements(){

    if ( ! extension_loaded( 'xml' ) ){
        if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
            add_action(
                'admin_notices',
                function() {
                    shipping_servientrega_pa_wc_ssp_notices( 'Shipping Servientrega Panamá Woocommerce: Requiere la extensión xml se encuentre instalada' );
                }
            );
        }
        return false;
    }

    if ( ! is_plugin_active(
        'woocommerce/woocommerce.php'
    ) ) {
        if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
            add_action(
                'admin_notices',
                function() {
                    shipping_servientrega_pa_wc_ssp_notices( 'Shipping Servientrega Panamá Woocommerce requiere que se encuentre instalado y activo el plugin: Woocommerce' );
                }
            );
        }
        return false;
    }

    $woo_countries   = new WC_Countries();
    $default_country = $woo_countries->get_base_country();

    if ( ! in_array( $default_country, array( 'PA' ), true ) ) {
        if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
            add_action(
                'admin_notices',
                function() {
                    $country = 'Shipping Servientrega Panamá Woocommerce requiere que el país donde se encuentra ubicada la tienda sea Panamá '  .
                        sprintf(
                            '%s',
                            '<a href="' . admin_url() .
                            'admin.php?page=wc-settings&tab=general#s2id_woocommerce_currency">' .
                            'Click para establecer</a>' );
                    shipping_servientrega_pa_wc_ssp_notices( $country );
                }
            );
        }
        return false;
    }

    return true;
}

function shipping_servientrega_pa_wc_ssp(){
    static $plugin;
    if (!isset($plugin)){
        require_once('includes/class-shipping-servientrega-pa-wc-ssp-plugin.php');
        $plugin = new SHIPPING_SERVIENTREGA_PA_WC_SSP_Plugin(__FILE__, SHIPPING_SERVIENTREGA_PA_WC_SSP_VERSION);
    }
    return $plugin;
}