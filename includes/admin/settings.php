<?php

wc_enqueue_js( "
    jQuery( function( $ ) {
	
	let shipping_servientrega_pa_wc_guides_fields = '#woocommerce_shipping_servientrega_pa_wc_use, #woocommerce_shipping_servientrega_pa_wc_pwd';
	
	let shipping_servientrega_pa_wc_sandbox_guides_fields = '#woocommerce_shipping_servientrega_pa_wc_sandbox_usu, #woocommerce_shipping_servientrega_pa_wc_sandbox_pwd';

	$( '#woocommerce_shipping_servientrega_pa_wc_environment' ).change(function(){

		$( shipping_servientrega_pa_wc_guides_fields + ',' + shipping_servientrega_pa_wc_sandbox_guides_fields ).closest( 'tr' ).hide();

		if ( '0' === $( this ).val() ) {
		    $( '#woocommerce_shipping_servientrega_pa_wc_guides, #woocommerce_shipping_servientrega_pa_wc_guides + p' ).show();
			$( '#woocommerce_shipping_servientrega_pa_wc_sandbox_guides, #woocommerce_shipping_servientrega_pa_wc_sandbox_guides + p' ).hide();
			$( shipping_servientrega_pa_wc_guides_fields ).closest( 'tr' ).show();
			
		}else{
		   $( '#woocommerce_shipping_servientrega_pa_wc_guides, #woocommerce_shipping_servientrega_pa_wc_guides + p' ).hide();	   
		   $( '#woocommerce_shipping_servientrega_pa_wc_sandbox_guides, #woocommerce_shipping_servientrega_pa_wc_sandbox_guides + p' ).show();
		   $( shipping_servientrega_pa_wc_sandbox_guides_fields ).closest( 'tr' ).show();
		}
	}).change();	
});	
");


return apply_filters(
    'shipping_servientrega_pa_wc_ssp_settings',
    array_merge(
    array(
        'enabled' => array(
            'title' => __('Activar/Desactivar'),
            'type' => 'checkbox',
            'label' => __('Activar Servientrega'),
            'default' => 'no'
        ),
        'title'        => array(
            'title'       => __( 'Título método de envío' ),
            'type'        => 'text',
            'description' => __( 'Esto controla el título que el usuario ve durante el pago' ),
            'default'     => __( 'Servientrega' ),
            'desc_tip'    => true
        ),
        'debug'        => array(
            'title'       => __( 'Depurador' ),
            'label'       => __( 'Habilitar el modo de desarrollador' ),
            'type'        => 'checkbox',
            'default'     => 'no',
            'description' => __( 'Enable debug mode to show debugging information on your cart/checkout.' ),
            'desc_tip' => true
        ),
        'environment' => array(
            'title' => __('Entorno'),
            'type'        => 'select',
            'class'       => 'wc-enhanced-select',
            'description' => __('Entorno de pruebas o producción'),
            'desc_tip' => true,
            'default' => '1',
            'options'     => array(
                '0'    => __( 'Producción'),
                '1' => __( 'Pruebas')
            ),
        )
    ),
    array(
        'guides'          => array(
            'title'       => __( 'Generación de guías' ),
            'type'        => 'title',
            'description' => __( 'usu y pwd para el entorno de producción' ),
        ),
        'use' => array(
            'title' => __( 'Usuario' ),
            'type'  => 'text',
            'description' => __( 'Usuario asignado' ),
            'desc_tip' => true
        ),
        'pwd' => array(
            'title' => __( 'Contraseña' ),
            'type'  => 'password',
            'description' => __( 'No confunda con la de seguimiento de despachos' ),
            'desc_tip' => true
        ),
        'sandbox_guides'          => array(
            'title'       => __( 'Generación de guias (pruebas)' ),
            'type'        => 'title',
            'description' => __( 'usu y pwd para el entorno de pruebas' )
        ),
        'sandbox_usu' => array(
            'title' => __( 'Usuario' ),
            'type'  => 'text',
            'description' => __( 'Usuario asignado' ),
            'desc_tip' => true
        ),
        'sandbox_pwd' => array(
            'title' => __( 'Contraseña' ),
            'type'  => 'password',
            'description' => __( 'No confunda con la de seguimiento de despachos' ),
            'desc_tip' => true
        )
    )
)
);