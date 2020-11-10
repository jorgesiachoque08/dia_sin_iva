<?php
use Phalcon\DI\FactoryDefault;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Cache\Adapter\Redis;
use Phalcon\Storage\SerializerFactory;
use Phalcon\Storage\Exception;
use GuzzleHttp\Client;


$di = new FactoryDefault();
/**
 * Shared configuration service
 */
$di->setShared('config', function () {
    return include APP_PATH . "/config/config.php";
});

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di['db'] = function (){

    $config = $this->getConfig();
    try {
        $connection = new DbAdapter(array(
            "host" => $config->database->host,
            "username" => $config->database->username,
            "password" => $config->database->password,
            "dbname" => $config->database->dbname,
            "charset" => $config->database->charset,
            "port" => $config->database->port,
            "persistent" => true,
            "options"    => [\PDO::ATTR_PERSISTENT => 1]
        ));

        $connection->connect();
        return $connection;
    } catch (\PDOException $e) {
        throw $e;
    }
};

/* $di->set('apiAcercate', function(){
    try {
        $config = $this->getConfig();
        $client  =  new  Client([ 'base_uri'  =>  $config->apiAcercate->url ]); 
        return $client;
    } catch (\Exception $ex) {
        return $ex->getMessage();
    }
    

}); */

$di->set('cache', function(){
    try {
        // Create the Cache setting redis connection options
        $serializerFactory = new SerializerFactory();
        $cache = new Redis($serializerFactory,
            [
                "host"       => "127.0.0.1",
                "port"       => 6379,
                "auth"=>"vrahpK34f5OkvdVlJ9kFHIKkyS8fFxKu3YUmwsVdrCfT7VBelVQHFf7+xafgPkNdQIisn8v78/fiU2v5",
                "persistent" => true,
                "lifetime"=> 432000 //5 dias cada key en la cache
            ]
        );
    } catch (Exception $e) {
        return $e->getMessage();
    }
    return $cache;

});

$di->setShared(
    'response',
    function () {
        $response = new \Phalcon\Http\Response();
        $response->setContentType('application/json', 'utf-8');
  
        return $response;
    }
  );
/* 
  $di->set('url', function() {
    $url = new UrlResolver();
    $config = $this->getConfig();
    $url->setBaseUri($config->application->baseUri);
    return $url;
}); */


/**
 * Correos por PHPMailer
 */
/* $di['mail'] = function () {
    $config = $this->getConfig();
    $client = new Postmark\PostmarkClient($config->mail->token);
    return $client;
}; */


