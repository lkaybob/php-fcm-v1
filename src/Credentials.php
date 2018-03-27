<?php
/**
 * Created by PhpStorm.
 * User: lkaybob
 * Date: 19/03/2018
 * Time: 21:37
 */

namespace phpFCMv1;

use \Firebase\JWT\JWT;
use \GuzzleHttp\Client;

class Credentials {
    const SCOPE = 'https://www.googleapis.com/auth/firebase.messaging';
    const TOKEN_URL = 'https://www.googleapis.com/oauth2/v4/token';
    const EXPIRE = 3600;
    const ALG = 'RS256';

    const CONTENT_TYPE = 'application/x-www-form-urlencoded';
    const GRANT_TYPE = 'urn:ietf:params:oauth:grant-type:jwt-bearer';

    private $keyFilePath;
    private const METHOD = 'POST';
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
    public function getAccessToken() : string {
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
    private function getTokenPayload() : string {
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
     */
    private function getToken($requestBody) : array {
        $this -> DATA_TYPE = 'form_params';

        $client = new Client();
        $response = $client -> request(self::METHOD, self::TOKEN_URL,
            array($this -> DATA_TYPE => $requestBody));

        return json_decode($response->getBody(), true);
    }

    /**
     * @return mixed
     */
    public function getKeyFilePath() : string {
        return $this -> keyFilePath;
    }

    /**
     * @param mixed $keyFilePath
     */
    public function setKeyFilePath($keyFilePath): void {
        $this -> keyFilePath = $keyFilePath;
    }
}