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
                'description' => __( 'usuario y contraseña para el entorno de producción' ),
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
            'print_type' => array(
                'title' => __('Formato de impresión'),
                'type'        => 'select',
                'class'       => 'wc-enhanced-select',
                'description' => __('Seleccione el formato de impresión'),
                'desc_tip' => true,
                'default' => 'carta',
                'options'     => array(
                    'carta'    => __( 'Carta'),
                    'sticker' => __( 'Sticker')
                ),
            ),
            'sender'  => array(
                'title' => __( 'Remitente' ),
                'type'  => 'title',
                'description' => __( 'Información requerida del remitente' )
            ),
            'sender_name' => array(
                'title'       => __( 'Nombre remitente' ),
                'type'        => 'text',
                'description' => __( 'Debe ir la razon social o el nombre comercial' ),
                'default'     => get_bloginfo('name'),
                'desc_tip'    => true
            ),
            'district_sender' => array(
                'title' => __('Distrito del remitente (donde se encuentra ubicada la tienda)'),
                'type'        => 'select',
                'class'       => 'wc-enhanced-select',
                'description' => __('Se recomienda selecionar distritos centrales'),
                'desc_tip' => true,
                'default' => true,
                'options'     => $this->districts_options()
            ),
            'sender_address' => array(
                'title'       => __( 'Dirección remitente' ),
                'type'        => 'text',
                'description' => __( 'Dirección del remitente' ),
                'default'     => '',
                'desc_tip'    => true
            ),
            'dice_contener' => array(
                'title' => __( 'Dice contener' ),
                'type'  => 'select',
                'class' => 'wc-enhanced-select',
                'description' => __( 'Opciones para el campo de dice contener' ),
                'desc_tip' => true,
                'default' => 0,
                'options'  => array(
                    0    => __( 'Nombres de productos'),
                    1    => __( 'Mercancía frágil')
                )
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
            ),
            'status_guide'  => array(
                'title' => __( 'Estado de guía' ),
                'type'  => 'title'
            ),
            'button_changed_order_status_to_processing'  => array(
                'title' => 'Actualizar ordenes a completado',
                'type'  => 'button',
                'class' => 'button-secondary shipping-servientrega-status-to-processing',
                'description' => "Actualiza a completado las ordenes según estado de envío",
                'text' => 'Actualizar ahora',
                'custom_attributes' => array(
                    'data-nonce' => wp_create_nonce( 'shipping_servientrega_order_status_changed_state_guide' )
                )
            ),
        )
    )
);