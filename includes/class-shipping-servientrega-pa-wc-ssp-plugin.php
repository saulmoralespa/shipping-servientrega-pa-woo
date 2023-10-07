<?php

class SHIPPING_SERVIENTREGA_PA_WC_SSP_Plugin
{
    /**
     * Filepath of main plugin file.
     *
     * @var string
     */
    public $file;
    /**
     * Plugin version.
     *
     * @var string
     */
    public $version;
    /**
     * Absolute plugin path.
     *
     * @var string
     */
    public $plugin_path;
    /**
     * Absolute plugin URL.
     *
     * @var string
     */
    public $plugin_url;
    /**
     * Absolute path to plugin includes dir.
     *
     * @var string
     */
    public $includes_path;
    /**
     * Absolute path to plugin lib dir
     *
     * @var string
     */
    public $lib_path;
    /**
     * @var bool
     */
    private $_bootstrapped = false;

    public function __construct($file, $version)
    {
        $this->file = $file;
        $this->version = $version;
        $this->plugin_path   = trailingslashit( plugin_dir_path( $file ) );
        $this->plugin_url    = trailingslashit( plugin_dir_url( $file ) );
        $this->includes_path = $this->plugin_path . trailingslashit( 'includes' );
        $this->lib_path = $this->plugin_path . trailingslashit( 'lib' );
    }

    public function run_servientrega_wc()
    {
        try{
            if ($this->_bootstrapped){
                throw new Exception( 'Shipping Servientrega Panamá Woocommerce can only be called once');
            }
            $this->_run();
            $this->_bootstrapped = true;
        }catch (Exception $e){
            if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
                add_action('admin_notices', function() use($e) {
                    shipping_servientrega_pa_wc_ssp_notices($e->getMessage());
                });
            }
        }
    }

    protected function _run()
    {
        if (!class_exists('\ServientregaPanama\WebService'))
            require_once ($this->lib_path . 'servientrega-webservice-php/src/autoload.php');
        require_once ($this->includes_path . 'class-method-shipping-servientrega-pa-wc-ssp.php');
        require_once ($this->includes_path . 'class-shipping-servientrega-pa-wc-ssp.php');

        add_filter( 'plugin_action_links_' . plugin_basename($this->file), array($this, 'plugin_action_links'));
        add_filter( 'woocommerce_shipping_methods', array($this, 'shipping_servientrega_pa_wc_add_method'));
        add_action( 'woocommerce_order_status_changed', array('Shipping_Servientrega_PA_WC_SSP', 'generate_guide'), 20, 4 );
    }

    public function log($message)
    {
        if (is_array($message) || is_object($message))
            $message = print_r($message, true);
        $logger = new WC_Logger();
        $logger->add('shipping-servientrega-pa-ssp', $message);
    }

    public function plugin_action_links($links)
    {
        $plugin_links = array();
        $plugin_links[] = '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=shipping&section=shipping_servientrega_pa_wc') . '">' . 'Configuraciones' . '</a>';
        return array_merge( $plugin_links, $links );
    }

    public function shipping_servientrega_pa_wc_add_method( $methods )
    {
        $methods['shipping_servientrega_pa_wc'] = 'WC_Shipping_Method_Shipping_Servientrega_PA_WC_SSP';
        return $methods;
    }
}