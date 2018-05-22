<?php
/**
 * Created by PhpStorm.
 * User: lkaybob
 * Date: 19/03/2018
 * Time: 21:37
 */

namespace phpFCMv1;

use \Firebase\JWT\JWT;
use \GuzzleHttp;

class Credentials {
    const SCOPE = 'https://www.googleapis.com/auth/firebase.messaging';
    const TOKEN_URL = 'https://www.googleapis.com/oauth2/v4/token';
    const EXPIRE = 3600;
    const ALG = 'RS256';

    const CONTENT_TYPE = 'form_params';
    const GRANT_TYPE = 'urn:ietf:params:oauth:grant-type:jwt-bearer';
    const METHOD = 'POST';
    const HTTP_ERRORS_OPTION = 'http_errors';

    private $keyFilePath;
    private $DATA_TYPE;

    /**
     * Credentials constructor. Checks whether given path is a valid file.
     * @param string                        $keyFile
     * @throws \InvalidArgumentException    when file is not found
     */
    public function __construct($keyFile = 'service_account.json') {
        if (is_file($keyFile)) {
            $this -> setKeyFilePath($keyFile);
        } else {
            throw new \InvalidArgumentException('Key file could not be found', 1);
        }
    }

    /**
     * @return string Access token for a project
     */
    public function getAccessToken()  {
        $requestBody = array(
            'grant_type' => self::GRANT_TYPE,
            'assertion' => $this -> getTokenPayload()
        );

        $result = $this -> getToken($requestBody);

        if (isset($result['error'])) {
            throw new \RuntimeException($result['error_description']);
        }

        return $result['access_token'];
    }

    /**
     * @return string Signed payload (with private key using algorithm)
     */
    private function getTokenPayload()  {
        $keyBody = json_decode(
            file_get_contents($this -> getKeyFilePath()), true
        );
        $now = (new \DateTime()) -> format('U');
        $iat = intval($now);

        $payload = array(
            'iss' => $keyBody['client_email'],
            'scope' => self::SCOPE,
            'aud' => self::TOKEN_URL,
            'iat' => $iat,
            'exp' => $iat + self::EXPIRE,
            'sub' => null
        );
        $signedJWT = JWT ::encode($payload, $keyBody['private_key'], self::ALG);

        return $signedJWT;
   }

    /**
     * @param $requestBody array    Payload with assertion data (which is signed)
     * @return array                Associative array of cURL result
     * @throws GuzzleHttp\Exception\GuzzleException
     *                              This exception is intentional
     */
    private function getToken($requestBody) {
        $client = new GuzzleHttp\Client();
        $response = $client -> request(self::METHOD, self::TOKEN_URL,
            array(self::CONTENT_TYPE => $requestBody, self::HTTP_ERRORS_OPTION => false));

        return json_decode($response->getBody(), true);
    }

    public function getProjectID() {
        $keyBody = json_decode(
            file_get_contents($this -> getKeyFilePath()), true
        );

        return $keyBody['project_id'];
    }

    /**
     * @return mixed
     */
    public function getKeyFilePath()  {
        return $this -> keyFilePath;
    }

    /**
     * @param mixed $keyFilePath
     */
    public function setKeyFilePath($keyFilePath) {
        $this -> keyFilePath = $keyFilePath;
    }
}