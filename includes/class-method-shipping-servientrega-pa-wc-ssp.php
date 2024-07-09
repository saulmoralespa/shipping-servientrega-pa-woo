<?php

class WC_Shipping_Method_Shipping_Servientrega_PA_WC_SSP extends WC_Shipping_Method
{

    public string $debug;

    public string $isTest;

    public string $username;

    public string $password;

    public function __construct($instance_id = 0)
    {
        parent::__construct($instance_id);

        $this->id = 'shipping_servientrega_pa_wc';
        $this->instance_id = absint( $instance_id );
        $this->method_title = __( 'Servientrega' );
        $this->method_description = __( 'Servientrega empresa transportadora' );
        $this->title = __( 'Servientrega' );

        $this->supports = array(
            'settings',
            'shipping-zones'
        );

        $this->init();
        $this->debug = $this->get_option( 'debug' );
        $this->isTest = (bool)$this->get_option( 'environment' );

        if ($this->isTest){
            $this->username = $this->get_option( 'sandbox_usu' );
            $this->password = $this->get_option( 'sandbox_pwd' );
        }else{
            $this->username  = $this->get_option( 'username' );
            $this->password = $this->get_option( 'password' );
        }
    }

    public function is_available($package): bool
    {
        return parent::is_available($package) &&
            $this->username &&
            $this->password;
    }

    public function init(): void
    {
        // Load the settings API.
        $this->init_form_fields(); // This is part of the settings API. Override the method to add your own settings.
        $this->init_settings(); // This is part of the settings API. Loads settings you previously init.
        // Save settings in admin if you have any defined.
        add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
    }

    public function init_form_fields(): void
    {
        $this->form_fields = include(dirname(__FILE__) . '/admin/settings.php');
    }

    public function admin_options(): void
    {
        ?>
        <h2><?php echo esc_html($this->title); ?></h2>
        <p><?php echo wp_kses_post(wpautop($this->method_description)); ?></p>
        <table class="form-table">
            <?php $this->generate_settings_html(); ?>
        </table>
        <?php
    }

    public function validate_password_field($key, $value) :string
    {
        if($this->get_option('username') !== '' && $key === 'password'){
            $status = Shipping_Servientrega_PA_WC_SSP::test_quote($this->get_option('username'), $value);
            if(!$status){
                WC_Admin_Settings::add_error("Credenciales inválidas");
                $value = '';
            }
        }

        return $value;
    }

    public function calculate_shipping($package = array()): void
    {
        $weight = 0;
        $height = 0;
        $width  = 0;
        $length = 0;
        $total_valorization = 0;

        foreach ($package['contents'] as $values) {
            $_product = $values['data'];
            $_product_id = $values['data']->get_id();
            $weight   = $weight + (int) $_product->get_weight() * $values['quantity'];
            $height   = $height + (int) $_product->get_height() * $values['quantity'];
            $width    = $width  + (int) $_product->get_width()  * $values['quantity'];
            $length   = $length + (int) $_product->get_length() * $values['quantity'];
            $custom_price_product = get_post_meta($_product_id, '_shipping_custom_price_product_smp', true);
            $total_valorization += $custom_price_product ?: $_product->get_price() * $values['quantity'];
        }

        $wc_countries = new WC_Countries();
        $destination_city  = $package['destination']['city'];
        $destination_city = Shipping_Servientrega_PA_WC_SSP::normalize_string($destination_city);
        $destination_state = $wc_countries->get_states($package['destination']['country'])[$package['destination']['state']];
        $destination_state = Shipping_Servientrega_PA_WC_SSP::normalize_string($destination_state);

        $origin_city = get_option('woocommerce_store_city');
        $origin_city = Shipping_Servientrega_PA_WC_SSP::normalize_string($origin_city);
        $default_country = get_option('woocommerce_default_country');

        list(
            $default_country,
            $default_state
            ) = explode(":", $default_country);
        $origin_state = $wc_countries->get_states($default_country)[$default_state];
        $origin_state = Shipping_Servientrega_PA_WC_SSP::normalize_string($origin_state);

        $data = [
            "tipo" => "obtener_tarifa_nacional",
            'ciu_ori' => $origin_city,
            'provincia_ori' => $origin_state,
            'ciu_des' => $destination_city,
            'provincia_des' => $destination_state,
            'valor_declarado' => $total_valorization,
            'peso' => $weight,
            'alto' => $height,
            'ancho' => $width,
            'largo' => $length,
            'recoleccion' => 'NO',
            "nombre_producto" => "PREMIER-RESIDENCIAL",
        ];

        if ($this->debug === 'yes')
            shipping_servientrega_pa_wc_ssp()->log($data);

        $response = Shipping_Servientrega_PA_WC_SSP::liquidation($data);

        if(!$response || empty($response['gtotal'])) return;

        $rate = array(
            'id' => $this->id,
            'label' => $this->title,
            'cost' => $response['gtotal'],
        );

        $this->add_rate($rate);

    }

    public function districts_options(): array
    {
        if(empty($_GET['section']) || $_GET['section'] !== $this->id) return [];

        $country = 'PA';
        $districts = WC_Districts_Places_Panama::get_districts($country);
        $countries_obj = new WC_Countries();
        $country_states_array = $countries_obj->get_states();
        $states = $country_states_array[$country];

        return array_reduce(array_keys($districts), function ($carry, $key) use ($districts, $states) {
            $province = $states[$key];
            $mapped = array_map(function ($district) use ($key, $province) {
                return ["$key~$district" => "$province - $district"];
            }, $districts[$key]);
            return array_merge($carry, ...$mapped);
        }, []);
    }
}