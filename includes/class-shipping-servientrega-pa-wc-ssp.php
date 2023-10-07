<?php

use ServientregaPanama\WebService;
class Shipping_Servientrega_PA_WC_SSP extends WC_Shipping_Method_Shipping_Servientrega_PA_WC_SSP
{
    public WebService $servientrega;

    public function __construct($instance_id = 0)
    {
        parent::__construct($instance_id);

        $this->servientrega = new WebService($this->usu, $this->pwd);
        $this->servientrega->sandbox_mode($this->isTest);
    }

    public static function generate_guide($order_id, $old_status, $new_status, WC_Order $order)
    {
        $guide_servientrega = get_post_meta($order->get_id(), 'guide_servientrega', true);
        $instance = new self();

        if (!(($order->has_shipping_method($instance->id)  ||
                $order->get_shipping_total() == 0) &&
            empty($guide_servientrega) &&
            $new_status === 'completed')
        ) return;

        try{
            $nombre_destinatario = $order->get_shipping_first_name() ? $order->get_shipping_first_name() .
                " " . $order->get_shipping_last_name() : $order->get_billing_first_name() .
                " " . $order->get_billing_last_name();
            $nombre_destinatario = self::normalize_string($nombre_destinatario);
            $direccion_destinatario = $order->get_shipping_address_1() ? $order->get_shipping_address_1() .
                " " . $order->get_shipping_address_2() : $order->get_billing_address_1() .
                " " . $order->get_billing_address_2();
            $city = $order->get_shipping_city() ? $order->get_shipping_city() : $order->get_billing_city();
            $city = self::normalize_string($city);
            $code_state = $order->get_shipping_state() ? $order->get_shipping_state() : $order->get_billing_state();
            $code_country = $order->get_shipping_country() ? $order->get_shipping_country() :  $order->get_billing_country();
            $name_state = self::name_destination($code_country, $code_state);
            $name_state = self::normalize_string($name_state);

            $items = $order->get_items();
            $name_products = [];
            $total_valorization = 0;

            foreach ($items as $values ) {

                $_product_id = $values['product_id'];
                $_product = wc_get_product( $_product_id );

                if ( $values['variation_id'] > 0 && in_array( $values['variation_id'], $_product->get_children() ) )
                    $_product = wc_get_product( $values['variation_id'] );

                $name_products[] = $_product->get_name();

                $custom_price_product = get_post_meta($_product->get_id(), '_shipping_custom_price_product_smp', true);
                $total_price = $custom_price_product > 0 ? wc_format_decimal($custom_price_product, 0) : wc_format_decimal($_product->get_price(), 0);

                $total_valorization += $total_price * $values['quantity'];
            }

            $namesProducts = implode(" ",  $name_products);

            $params = [
                'nombre_destinatario' => $nombre_destinatario,
                'direccion_destinatario' => $direccion_destinatario,
                'distrito_destinatario' => $name_state,
                'provincia_destinatario' => $city,
                'nombre_remite' => '',
                'direccion_remite' => '',
                'distrito_remite' => '',
                'provincia_remite' => '',
                'servicio' => 'PREMIER-RESIDENCIAL',
                'telefono' => $order->get_billing_phone(),
                'peso' => '',
                'piezas' => count($items),
                'volumen' => '',
                'contiene' => substr($namesProducts, 0, 50),
                'transporte' => 'TERRESTRE',
                'valor_declarado' => $total_valorization,
                'info01' => substr($namesProducts, 0, 50),
                'valor_recaudar' => 0,
                'remision' => '',
                'factura' => $order->get_id(),
                'observacion' => substr($namesProducts, 0, 50),
                'guia_cliente' => '',
                'latitud' => '',
                'longitud' => '',
                'mail_destinatario' => $order->get_billing_email(),
                'fecha_programacion' => ''
            ];
            $result = $instance->servientrega->generar_guia($params);
            $number_guide = $result['miembro']['guia'];
            $url_guide = $result['miembro']['url'];
            update_post_meta($order->get_id(), 'guide_servientrega', $number_guide);
            $guide_nota = sprintf( __( 'Guia Servientrega <a target="_blank" href="%1$s">' . $number_guide .'</a>.' ), $url_guide );
            $order->add_order_note($guide_nota);
        }catch (\Exception $exception){
            shipping_servientrega_pa_wc_ssp()->log($exception->getMessage());
        }

    }

    public static  function name_destination($country, $state_destination)
    {
        $countries_obj = new WC_Countries();
        $country_states_array = $countries_obj->get_states();

        $name_state_destination = '';

        if(!isset($country_states_array[$country][$state_destination]))
            return $name_state_destination;

        return $country_states_array[$country][$state_destination];
    }

    public static function normalize_string($string)
    {
        $not_permitted = array ("á","é","í","ó","ú","Á","É","Í",
            "Ó","Ú","ñ");
        $permitted = array ("a","e","i","o","u","A","E","I","O",
            "U","n");
        $text = str_replace($not_permitted, $permitted, $string);
        $text = mb_strtoupper($text);
        return $text;
    }
}