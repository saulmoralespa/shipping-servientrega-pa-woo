<?php

use Saulmoralespa\ServientregaPanama\WebService;

class Shipping_Servientrega_PA_WC_SSP extends WC_Shipping_Method_Shipping_Servientrega_PA_WC_SSP
{
    const DIR_GUIDES = 'guides-servientrega';

    public static ?WebService $servientrega = null;
    private static $shipping_settings = null;

    public static function test_quote($user, $token): bool
    {
        try{
            $servientrega = new WebService($user, $token);
            $data = array(
                "tipo" => "obtener_tarifa_nacional",
                'ciu_ori' => "24 DE DICIEMBRE",
                'provincia_ori' => "PANAMA",
                'ciu_des' => "BAGALA",
                'provincia_des' => "CHIRIQUI",
                'valor_declarado' => "200.5",
                'peso' => 5,
                'alto' => 20,
                'ancho' => 25,
                'largo' => 30,
                'recoleccion' => 'NO',
                "nombre_producto" => "PREMIER-RESIDENCIAL",
            );
            $servientrega->quote($data);
        }catch (Exception $exception){
            return false;
        }
        return true;
    }

    public static function get_instance(): ?WebService
    {
        if(isset(self::$shipping_settings) && isset(self::$servientrega)) return self::$servientrega;

        self::$shipping_settings = get_option('woocommerce_shipping_servientrega_pa_wc_settings');

        if(!isset(self::$shipping_settings)) return null;

        self::$shipping_settings = (object)self::$shipping_settings;

        if(!self::$shipping_settings->enabled) return null;

        if(self::$shipping_settings->username &&
            self::$shipping_settings->password){
            self::$servientrega = new WebService(self::$shipping_settings->username, self::$shipping_settings->password);
        }

        return self::$servientrega;
    }

    public static function liquidation(array $data): ?array
    {
        if (!self::get_instance()) return null;

        $res = null;

        try {
            $res = self::get_instance()->quote($data);
        }catch (\Exception $exception){
            shipping_servientrega_pa_wc_ssp()->log($exception->getMessage());
        }

        return $res;
    }

    public static function generate_guide($order_id, $old_status, $new_status, WC_Order $order): void
    {
        if (!self::get_instance() || wc_get_order_status_name($new_status) !== wc_get_order_status_name(self::$shipping_settings->order_status_generate_guide)) return;

        $guide_servientrega = get_post_meta($order->get_id(), '_guide_servientrega', true);

        $method_id = 'shipping_filters_by_cities_sfbc';

        if (!(($order->has_shipping_method($method_id)  ||
                $order->get_shipping_total() == 0 &&
                self::$shipping_settings->guide_free_shipping === 'yes') &&
            empty($guide_servientrega))
        ) return;

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
        $name_state = self::name_destination($code_state);
        $name_state = self::normalize_string($name_state);
        $district_sender = self::$shipping_settings->district_sender;
        $district_arr = explode("~", $district_sender);
        $province_code = $district_arr[0];
        $provincia_remite = $province_code ? self::name_destination($province_code) : '';
        $provincia_remite = self::normalize_string($provincia_remite);
        $distrito_remite = $district_arr[1] ?? '';
        $distrito_remite = self::normalize_string($distrito_remite);

        $items = $order->get_items();
        $name_products = [];

        foreach ($items as $values ) {

            $_product_id = $values['product_id'];
            $_product = wc_get_product( $_product_id );

            if ( $values['variation_id'] > 0 && in_array( $values['variation_id'], $_product->get_children() ) )
                $_product = wc_get_product( $values['variation_id'] );

            $name_products[] = $_product->get_name();
        }

        $namesProducts = implode(" ",  $name_products);
        $service = str_contains($order->get_shipping_method(), 'Sucursal de Servientrega') ? 'PREMIER-CDS A CDS' : 'PREMIER-RESIDENCIAL';

        /*if(str_contains($service, 'CDS')){
            $direccion_destinatario = "CDS $name_state $direccion_destinatario";
        }*/

        $direccion_remite =  self::$shipping_settings->sender_address ?: get_option( 'woocommerce_store_address' ) .
            " " .  get_option( 'woocommerce_store_address_2' ) .
            " " . get_option( 'woocommerce_store_city' );

        $params = [
            'nombre_destinatario' => $nombre_destinatario,
            'direccion_destinatario' => $direccion_destinatario,
            'distrito_destinatario' => $city,
            'provincia_destinatario' => $name_state,
            'nombre_remite' => self::$shipping_settings->sender_name,
            'direccion_remite' => $direccion_remite,
            'distrito_remite' => $distrito_remite,
            'provincia_remite' => $provincia_remite,
            'servicio' => $service,
            'telefono' => $order->get_billing_phone(),
            'peso' => '',
            'piezas' => 1,
            'volumen' => '',
            'contiene' => self::$shipping_settings->dice_contener ? 'MERCANCIA FRAGIL' : substr($namesProducts, 0, 50),
            'transporte' => 'TERRESTRE',
            'valor_declarado' => $order->get_total(),
            'info01' => '',
            'valor_recaudar' => 0,
            'remision' => '',
            'factura' => ($order->get_total() - $order->get_shipping_total()),
            'observacion' => '',
            'guia_cliente' => '',
            'latitud' => '',
            'longitud' => '',
            'mail_destinatario' => $order->get_billing_email(),
            'fecha_programacion' => ''
        ];

        try{
            $result = self::get_instance()->generarGuia($params);
            $number_guide = $result['miembro']['guia'];
            $url_guide = $result['miembro']['url'];
            $base64 = $result['miembro']['autoscan64'];
            $bin = base64_decode($base64, true);
            self::save_guide($number_guide, $bin);
            update_post_meta($order->get_id(), '_guide_servientrega', $number_guide);
            $guide_nota = sprintf( __( 'Guía Servientrega <a target="_blank" href="%1$s">' . $number_guide .'</a>.' ), $url_guide );
            $order->add_order_note($guide_nota);
            do_action('shipping_servientrega_pa_wc_generated_guide', $order_id);
        }catch (\Exception $exception){
            shipping_servientrega_pa_wc_ssp()->log($params);
            shipping_servientrega_pa_wc_ssp()->log($exception->getMessage());
        }
    }

    public static  function name_destination($state_destination, $country = 'PA')
    {
        $countries_obj = new WC_Countries();
        $country_states_array = $countries_obj->get_states();

        $name_state_destination = '';

        if(!isset($country_states_array[$country][$state_destination]))
            return $name_state_destination;

        return $country_states_array[$country][$state_destination];
    }

    public static function normalize_string($string): string
    {
        $not_permitted = array ("á","é","í","ó","ú","Á","É","Í",
            "Ó","Ú","ñ");
        $permitted = array ("a","e","i","o","u","A","E","I","O",
            "U","n");
        $text = str_replace($not_permitted, $permitted, $string);
        return mb_strtoupper($text);
    }

    protected static function save_guide($number_guide, $guide): void
    {
        $upload_dir = wp_upload_dir();
        $dir = trailingslashit($upload_dir['basedir']) . trailingslashit(self::DIR_GUIDES);

        if (!is_dir($dir)) {
            mkdir($dir,0755);
        }

        $filename = $dir . "$number_guide.pdf";

        file_put_contents($filename, $guide);
    }

    public static function delete_old_pdfs(): void
    {
        $upload_dir = wp_upload_dir();
        $dir = trailingslashit($upload_dir['basedir']) . trailingslashit(self::DIR_GUIDES);
        $files = glob($dir . '/*');
        $now = time();

        foreach ($files as $file) {
            if (!is_file($file)) continue;

            $fileLastModified = filemtime($file);
            if ($now - $fileLastModified >= 30 * 24 * 60 * 60) {
                unlink($file);
            }
        }
    }
}