<?php

class WC_Shipping_Method_Shipping_Servientrega_PA_WC_SSP extends WC_Shipping_Method
{
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
            //'shipping-zones'
        );

        $this->init();
        $this->debug = $this->get_option( 'debug' );
        $this->isTest = (bool)$this->get_option( 'environment' );

        if ($this->isTest){
            $this->usu = $this->get_option( 'sandbox_usu' );
            $this->pwd = $this->get_option( 'sandbox_pwd' );

        }else{
            $this->usu  = $this->get_option( 'use' );
            $this->pwd = $this->get_option( 'pwd' );
        }
    }

    public function is_available($package)
    {
        return parent::is_available($package) &&
            $this->usu &&
            $this->pwd;
    }

    public function init()
    {
        // Load the settings API.
        $this->init_form_fields(); // This is part of the settings API. Override the method to add your own settings.
        $this->init_settings(); // This is part of the settings API. Loads settings you previously init.
        // Save settings in admin if you have any defined.
        add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
    }

    public function init_form_fields()
    {
        $this->form_fields = include(dirname(__FILE__) . '/admin/settings.php');
    }

    public function admin_options()
    {
        ?>
        <h2><?php echo esc_html($this->title); ?></h2>
        <p><?php echo wp_kses_post(wpautop($this->method_description)); ?></p>
        <table class="form-table">
            <?php $this->generate_settings_html(); ?>
        </table>
        <?php
    }
}