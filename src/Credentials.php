<?php
/**
 * Created by PhpStorm.
 * User: lkaybob
 * Date: 19/03/2018
 * Time: 21:37
 */

namespace phpFCMv1;

use \Firebase\JWT\JWT;

class Credentials {
    const SCOPE = 'https://www.googleapis.com/auth/firebase.messaging';
    const TOKEN_URL = 'https://www.googleapis.com/oauth2/v4/token';
    const EXPIRE = 3600;
    const ALG = 'RS256';

    const CONTENT_TYPE = 'application/x-www-form-urlencoded';
    const GRANT_TYPE = 'urn:ietf:params:oauth:grant-type:jwt-bearer';

    private $keyFilePath;

    public function __construct($keyFile = 'service_account.json') {
        if (is_file($keyFile)) {
            $this -> setKeyFilePath($keyFile);
        } else {
            throw new \InvalidArgumentException('Key file could not be found', 1);
        }
    }

    public function getAccessToken() {
        $requestBody = array(
            'grant_type' => self::GRANT_TYPE,
            'assertion' => $this -> getTokenPayload()
        );

        $curl = curl_init(self::TOKEN_URL);
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => array('Content-Type: ' . self::CONTENT_TYPE),
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($requestBody)
        );
        curl_setopt_array($curl, $options);
        $result = curl_exec($curl); // TODO: Error Handling??
        list($header, $body) = explode("\r\n\r\n", $result);

        $resultDecoded = json_decode($body, true);

        if (isset($resultDecoded['error'])) {
            throw new \RuntimeException($resultDecoded['error_description']);
        }

        return $resultDecoded['access_token'];
    }

    private function getTokenPayload() {
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
     * @return mixed
     */
    public function getKeyFilePath() {
        return $this -> keyFilePath;
    }

    /**
     * @param mixed $keyFilePath
     */
    public function setKeyFilePath($keyFilePath): void {
        $this -> keyFilePath = $keyFilePath;
    }

}