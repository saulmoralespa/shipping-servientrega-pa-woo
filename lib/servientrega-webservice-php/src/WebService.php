<?php
/**
 * Created by PhpStorm.
 * User: smp
 * Date: 27/01/19
 * Time: 11:08 AM
 */

namespace ServientregaPanama;

use Exception;
use nusoap_client;

class WebService
{

    const SANDBOX_URL_GUIDES = 'http://ws-servientrega.appsiscore.com/test/generar_guia.php?wsdl';

    const URL_GUIDES = 'http://ws-servientrega.appsiscore.com/generar_guia_carta.php?wsdl';

    private $usu;

    private $pwd;

    private static $sandbox = false;


    public function __construct($usu, $pwd)
    {
        $this->usu = $usu;
        $this->pwd = $pwd;
    }

    public function sandbox_mode($status = false)
    {
        if ($status){
            self::$sandbox = true;
        }
        return $this;
    }

    public static function get_url_guides()
    {
        if (self::$sandbox)
            return self::SANDBOX_URL_GUIDES;
        return self::URL_GUIDES;
    }

    /**
     * @param $params
     * @return \$1|false|\SimpleXMLElement
     * @throws Exception
     */
    public function generar_guia($params)
    {
        $params = array_merge(
            [
                'usu' => $this->usu,
                'pwd' => $this->pwd
            ],
            $params
        );

        $endpoint = self::get_url_guides();

        try {
            $client = new nusoap_client($endpoint, "wsdl");
            $result = $client->call('getXML', $params);

            if ($client->fault) {
                throw new \Exception($client->getError());
            }

            $result = simplexml_load_string($result);
            $json = json_encode($result);
            return json_decode($json, true);
        }catch (Exception $exception){
            throw new  \Exception($exception->getMessage());
        }
    }
}