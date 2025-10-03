<?php

class SHIPPING_SERVIENTREGA_PA_WC_SSP_Plugin
{
    /**
     * Filepath of main plugin file.
     *
     * @var string
     */
    public string $file;
    /**
     * Plugin version.
     *
     * @var string
     */
    public string $version;
    /**
     * Absolute plugin path.
     *
     * @var string
     */
    public string $plugin_path;
    /**
     * Absolute plugin URL.
     *
     * @var string
     */
    public string $plugin_url;
    /**
     * assets plugin.
     *
     * @var string
     */
    public string $assets;
    /**
     * Absolute path to plugin includes dir.
     *
     * @var string
     */
    public string $includes_path;
    /**
     * Absolute path to plugin lib dir
     *
     * @var string
     */
    public string $lib_path;
    /**
     * @var bool
     */
    private bool $is_bootstrapped = false;

    public function __construct($file, $version)
    {
        $this->file = $file;
        $this->version = $version;
        $this->plugin_path   = trailingslashit( plugin_dir_path( $file ) );
        $this->plugin_url    = trailingslashit( plugin_dir_url( $file ) );
        $this->assets = $this->plugin_url . trailingslashit('assets');
        $this->includes_path = $this->plugin_path . trailingslashit( 'includes' );
        $this->lib_path = $this->plugin_path . trailingslashit( 'lib' );
    }

    public function run_servientrega_wc(): void
    {
        try{
            if ($this->is_bootstrapped){
                throw new Exception( 'Shipping Servientrega Panamá Woocommerce can only be called once');
            }
            $this->_run();
            $this->is_bootstrapped = true;
        }catch (Exception $e){
            if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
                add_action('admin_notices', function() use($e) {
                    shipping_servientrega_pa_wc_ssp_notices($e->getMessage());
                });
            }
        }
    }

    protected function _run(): void
    {
        if (!class_exists('\Saulmoralespa\ServientregaPanama\WebService'))
            require_once ($this->lib_path . 'vendor/autoload.php');
        require_once ($this->includes_path . 'class-method-shipping-servientrega-pa-wc-ssp.php');
        require_once ($this->includes_path . 'class-shipping-servientrega-pa-wc-ssp.php');

        add_filter( 'plugin_action_links_' . plugin_basename($this->file), array($this, 'plugin_action_links'));
        add_filter( 'woocommerce_shipping_methods', array($this, 'shipping_servientrega_pa_wc_add_method'));
        add_action( 'admin_enqueue_scripts', array($this, 'enqueue_scripts_admin'));
        add_action( 'woocommerce_order_status_changed', array('Shipping_Servientrega_PA_WC_SSP', 'generate_guide'), 20, 4 );
        add_action( 'servientrega_pa_wc_ssp_schedule', array('Shipping_Servientrega_PA_WC_SSP', 'delete_old_pdfs'));
        add_action( 'shipping_servientrega_pa_wc_generated_guide', array($this, 'order_status_changed_state_guide'), 20);
        add_action( 'order_status_changed_state_guide_schedule', array('Shipping_Servientrega_PA_WC_SSP', 'order_status_changed_state_guide_schedule') );
        add_action( 'orders_status_changed_state_guide_schedule', array('Shipping_Servientrega_PA_WC_SSP', 'orders_status_changed_state_guide_schedule') );
        add_action( 'wp_ajax_shipping_servientrega_order_status_changed_state_guide', array($this, 'shipping_servientrega_order_status_changed_state_guide'));
    }

    public function log($message): void
    {
        if (is_array($message) || is_object($message))
            $message = print_r($message, true);
        $logger = new WC_Logger();
        $logger->add('shipping-servientrega-pa-ssp', $message);
    }

    public function plugin_action_links($links): array
    {
        $plugin_links = array();
        $plugin_links[] = '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=shipping&section=shipping_servientrega_pa_wc') . '">' . 'Configuraciones' . '</a>';
        return array_merge( $plugin_links, $links );
    }

    public function shipping_servientrega_pa_wc_add_method( array $methods ): array
    {
        $methods['shipping_servientrega_pa_wc'] = 'WC_Shipping_Method_Shipping_Servientrega_PA_WC_SSP';
        return $methods;
    }

    public function enqueue_scripts_admin($hook): void
    {
        if($hook === 'woocommerce_page_wc-settings' && isset($_GET['section']) && $_GET['section'] === 'shipping_servientrega_pa_wc'){
            wp_enqueue_script( 'shipping-servientrega-pa-sweet-alert', $this->assets. 'js/sweetalert2.min.js', array( 'jquery' ), $this->version, true );
            wp_enqueue_script( 'shipping-servientrega-pa', $this->assets. 'js/shipping-servientrega-pa.js', array( 'jquery' ), $this->version, true );
        }
    }

    public function order_status_changed_state_guide($order_id): void
    {
        $order = wc_get_order($order_id);

        if($order->has_status('completed')) return;

        $hour_current = (int)wp_date('H');
        $hour_execution = 18;
        $hours = $hour_current < $hour_execution ? $hour_execution - $hour_current : 24 - $hour_current + $hour_execution;
        $hours = $hours === 0 ? 24 : $hours;
        $hours_to_seconds = 60 * 60 * $hours;

        wp_schedule_single_event(time() + $hours_to_seconds, 'order_status_changed_state_guide_schedule', array('order_id' => $order_id));
    }

    public static function shipping_servientrega_order_status_changed_state_guide(): void
    {
        check_ajax_referer('shipping_servientrega_order_status_changed_state_guide', 'nonce');

        wp_schedule_single_event(time() + 5, 'orders_status_changed_state_guide_schedule');

        wp_send_json(['status' => 'success']);
    }
}