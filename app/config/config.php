<?php

/*
 * Modified: prepend directory path of current file, because of this file own different ENV under between Apache and command line.
 * NOTE: please remove this comment.
 */
defined('BASE_PATH') || define('BASE_PATH', getenv('BASE_PATH') ?: realpath(dirname(__FILE__) . '/../..'));
defined('APP_PATH') || define('APP_PATH', BASE_PATH . '/app');
$tokenId    = base64_encode(random_bytes(32));
$issuedAt   = time();
$notBefore  = $issuedAt + 1;             //Adding 1 seconds
$expire     = $notBefore + (60*60);            // Adding 10 seconds
if (isset($_SERVER['SERVER_NAME'])) {
    $serverName = $_SERVER['SERVER_NAME'];
}else{
    $serverName = "Acercate";
}


return new \Phalcon\Config([
    'database' => [
        'adapter'     => 'Mysql',
        'host'        => 'db-acercate-negocios.cmnmnai7kvge.us-east-1.rds.amazonaws.com',
        'username'    => 'admin',
        'password'    => '8PBLgZ4ePGszBAhmbqSpG6N4',
        'dbname'      => 'dia_sin_iva',
        'port' =>3306,
        'charset'     => 'utf8',
    ],
    'application' => [
        'appDir'         => APP_PATH . '/',
        'controllersDir' => APP_PATH . '/controllers/',
        'modelsDir'      => APP_PATH . '/models/',
        'migrationsDir'  => APP_PATH . '/migrations/',
        'viewsDir'       => APP_PATH . '/views/',
        'pluginsDir'     => APP_PATH . '/plugins/',
        'libraryDir'     => APP_PATH . '/library/',
        'cacheDir'       => APP_PATH . '/cache/',
        'componentesDir'       => APP_PATH . '/componentes/',
        'baseUri'        => '/',
    ],
    'jwt' => [
        "key"=>'123456789QWERTYUIOPASDFGHJKLZXCVBNM',
        "data" => array(
            'iat'  => $issuedAt,         // Issued at: time when the token was generated
            'jti'  => $tokenId,          // Json Token Id: an unique identifier for the token
            'iss'  => $serverName,       // Issuer
            'nbf'  => $notBefore,        // Not before
            'exp'  => $expire,            // Expire
            'data'=> null
        )

        ],
    'apiDiaSinIva'=>[
        "url"=>"http://54.161.247.83:3000/",
        "username"=>"ampo99@hotmail.com",
        "password"=>"12345"
    ],
    'mail'=>[
        "token"=>"4bcd18b5-ac96-46ad-9135-fe440020c59f",
        "sender"=>"jsiachoque@acercate.com.co"
    ]
]);
