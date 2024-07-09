<?php
/**
 * Created by PhpStorm.
 * User: smp
 * Date: 27/01/19
 * Time: 11:08 AM
 */

namespace Saulmoralespa\ServientregaPanama;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Utils;
use Exception;
use nusoap_client;

class WebService
{
    const URL_QUOTE = 'https://ws-servientrega.appsiscore.com/cotizador/ws_cotizador.php';

    const SANDBOX_URL_GUIDES = 'https://ws-servientrega.appsiscore.com/test/generar_guia.php?wsdl';

    const URL_GUIDES = 'https://ws-servientrega.appsiscore.com/generar_guia_carta.php?wsdl';

    const URL_TRACKING_DISPATCHES = 'https://ws-servientrega.appsiscore.com/server_wst.php?wsdl';


    private static bool $sandbox = false;


    public function __construct(
        private $usu,
        private $pwd
    ) {
    }

    protected function client(): GuzzleClient
    {
        return new GuzzleClient([
            'base_uri' => self::URL_QUOTE
        ]);
    }


    public function sandboxMode($status = false): static
    {
        if ($status) {
            self::$sandbox = true;
        }
        return $this;
    }

    public static function getUrlGuides(): string
    {
        if (self::$sandbox) {
            return self::SANDBOX_URL_GUIDES;
        }
        return self::URL_GUIDES;
    }

    /**
     * @throws Exception
     */
    public function quote(array $data): array
    {
        $data = array_merge(
            [
                'usuingreso' => $this->usu,
                'contrasenha' => $this->pwd
            ],
            $data
        );

        $endpoint = self::URL_QUOTE;

        try {
            $res = $this->client()->request('POST', $endpoint, [
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'json' => $data
            ]);
            $content = $res->getBody()->getContents();
            $res = self::responseArray($content);
            $errorMessage = 'Usuario o Clave Incorrectos';

            if (!empty($res['proceso']) && str_contains($res['proceso'], $errorMessage)) {
                throw new Exception($errorMessage);
            }

            return $res;
        } catch (RequestException|\Exception  $exception) {
            $errorMessage = $exception->getResponse()->getBody()->getContents();
            throw new  \Exception($errorMessage);
        } catch (GuzzleException $exception) {
            throw new  \Exception($exception->getMessage());
        }
    }

    /**
     * @param $params
     * @return \$1|false|\SimpleXMLElement
     * @throws Exception
     */
    public function generarGuia(array $params): array
    {
        $endpoint = self::getUrlGuides();
        return $this->callSoap($params, $endpoint);
    }

    /**
     * @throws Exception
     */
    public function tracking(array $params): array
    {
        $endpoint = self::URL_TRACKING_DISPATCHES;
        return $this->callSoap($params, $endpoint);
    }

    /**
     * @throws Exception
     */
    private function callSoap(array $params, string $endpoint): array
    {

        $params = array_merge(
            [
                'usu' => $this->usu,
                'pwd' => $this->pwd
            ],
            $params
        );

        try {
            $client = new nusoap_client($endpoint, "wsdl");
            $result = $client->call('getXML', $params);

            if ($client->fault) {
                throw new \Exception($client->getError());
            }

            $xml = simplexml_load_string($result);

            if (!$xml) {
                throw new \Exception('No se pudo obtener la respuesta del servidor');
            }

            $json = json_encode($xml);
            return json_decode($json, true);
        } catch (Exception $exception) {
            throw new  \Exception($exception->getMessage());
        }
    }

    public static function responseArray(string $content): array
    {
        $json = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $content);
        return Utils::jsonDecode($json, true);
    }
}