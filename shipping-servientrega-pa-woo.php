<?php
/**
 * Plugin Name: Shipping Servientrega Panamá Woo
 * Description: Shipping Servientrega Panamá Woocommerce
 * Version: 1.0.0
 * Author: Saúl Morales Pacheco
 * Author URI: https://saulmoralespa.com
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * WC tested up to: 10.2.2
 * WC requires at least: 8.9
 * Requires at least: 6.0
 * Tested up to: 6.8
 * Requires PHP: 8.1
 * Requires Plugins: woocommerce,provincias-y-distritos-de-panama-para-woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if(!defined('SHIPPING_SERVIENTREGA_PA_WC_SSP_VERSION')){
    define('SHIPPING_SERVIENTREGA_PA_WC_SSP_VERSION', '1.0.0');
}

add_action( 'plugins_loaded', 'shipping_servientrega_pa_wc_ssp_init');
add_action(
        'before_woocommerce_init',
        function () {
            if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
                \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__ );
            }
        }
);

function shipping_servientrega_pa_wc_ssp_init(): void
{
    if ( ! shipping_servientrega_pa_wc_ssp_requirements() )
        return;

    shipping_servientrega_pa_wc_ssp()->run_servientrega_wc();
}

function shipping_servientrega_pa_wc_ssp_notices( $notice ): void
{
    ?>
    <div class="error notice">
        <p><?php echo $notice; ?></p>
    </div>
    <?php
}

function shipping_servientrega_pa_wc_ssp_requirements(): bool
{

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

    $woo_countries   = new WC_Countries();
    $default_country = $woo_countries->get_base_country();

    if ($default_country !== 'PA') {
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

function activate_servientrega_pa_wc_ssp(): void
{
    if ( ! wp_next_scheduled( 'servientrega_pa_wc_ssp_schedule'  ) ) {
        wp_schedule_event( time(), 'monthly', 'servientrega_pa_wc_ssp_schedule' );
    }
}

function deactivation_servientrega_pa_wc_ssp():void
{
    wp_clear_scheduled_hook( 'servientrega_pa_wc_ssp_schedule' );
}

register_activation_hook( __FILE__, 'activate_servientrega_pa_wc_ssp' );
register_deactivation_hook( __FILE__, 'deactivation_servientrega_pa_wc_ssp' );