<?php

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
            'username' => array(
                'title' => __( 'Usuario' ),
                'type'  => 'text',
                'description' => __( 'Usuario asignado' ),
                'desc_tip' => true
            ),
            'password' => array(
                'title' => __( 'Contraseña' ),
                'type'  => 'password',
                'description' => __( 'No confunda con la de seguimiento de despachos' ),
                'desc_tip' => true
            ),
            'service' => array(
                'title' => __('Servicio'),
                'type'        => 'select',
                'class'       => 'wc-enhanced-select',
                'description' => __('Servicio de envío'),
                'desc_tip' => true,
                'default' => 'PREMIER-RESIDENCIAL',
                'options'     => array(
                    'PREMIER-RESIDENCIAL'    => __( 'PREMIER-RESIDENCIAL'),
                    'PREMIER-CDS A CDS' => __( 'PREMIER-CDS A CDS')
                ),
            ),
            'order_status_generate_guide' => array(
                'title' => __( 'Estado del pedido' ),
                'type' => 'select',
                'options'  => wc_get_order_statuses(),
                'default' => 'wc-processing',
                'description' => __( 'El estado del pedido en el que se genera la guía' ),
                'desc_tip' => false
            ),
            'guide_free_shipping' => array(
                'title'       => __( 'Generar guías cuando el envío es gratuito' ),
                'label'       => __( 'Habilitar la generación de guías para envíos gratuitos' ),
                'type'        => 'checkbox',
                'default'     => 'no',
                'description' => __( 'Permite la generación de guías cuando el envío es gratuito' ),
                'desc_tip' => true
            )
        )
    )
);