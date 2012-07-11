<!--
	javascript:q=(escape(document.location.href));(function(){window.open('http://localhost/elkartrukatu/web/index.php/gozatu?url='+q);})();
-->
<?php 
error_reporting(E_ALL);
ini_set('display_errors','On');

// Funcion de que se llama al hacer new del objeto __autoload()
// http://www.php.net/manual/es/function.spl-autoload-register.php
spl_autoload_register(function () {
 //   echo 'AAAA'.$class_name;
    require_once __DIR__."/../Generator/gozatzen/model/gozatzen_model.php";
    require_once __DIR__."/../Generator/generator.php";
    require_once __DIR__."/../vendor/autoload.php";
});

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();

// options
$app['debug'] = true;

// database configuration
// /elkartrukatu/vendor/silex/silex/src/Silex/Provider/DoctrineServiceProvider.php
// datuak BBDDtik atera edo sartzeko 
// /elkartrukatu/vendor/silex/dbal/linb/Doctrine/DBal/Portability/Connection.php

// TODO: sartu hau database.php fitxategi baten barruan
// TODO: sartu titulu link bakoitzeko. Hortarako title-a scrapeatu
// TODO: sartu register guztiak bootstrap klase batetan. Hemen $app['bootstrap'] = 
// bootstrap klase metodoa. Register metodoa, routing, errorHandling,RegisterService eta horiek run metodo 
// publiko batetik
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
    	'dbname' => 'elkartrukatu',
	    'user' => 'root',
	    'password' => 'aitiba',
	    'host' => 'localhost',
        'driver'   => 'pdo_mysql',
    ),
));

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));

$app->register(new Silex\Provider\SessionServiceProvider());

// http://localhost/elkartrukatu/web/index.php/gozatu

// TODO: Gozatua sartzen dunaren IPa sartu
// TODO: created datetime bihurtu

$app->match('/gozatu', function () use ($app){
    $output = include "../gozatu.html";
    if($_GET) {
    	//botoia sakatu da
    	$data = array(
    		'url' => $_GET['url'],
    		'created' => date("Y-d-m H:i:s") ,
    		'ip' => '127.0.0.1'
    		);
    
    	$app['db']->insert('links', $data);

     	//return new Response('Thank you for your feedback!', 201);
    	return new Response($app->redirect($_GET['url']));
    } 
    return $output;
})
->method('GET|POST');

$app->match('/gozatzen', function () use ($app){

/*    function __autoload($class_name) {
    echo 'AAAA'.$class_name;
    include $class_name . '.php';
}*/




    $gozatzen_model = new Gozatzen\Model\gozatzen_model($app);
    $data= $gozatzen_model->getLinks();
    //$sql = "SELECT * FROM links";
    //$data = $app['db']->fetchAll($sql);

    //var_dump($app['session']->get('schema'));

    if ($data) {
    	 return $app['twig']->render('gozatzen.twig', array(
        'data' => $data,
    ));
    	/*foreach ($data as $d) {
    		$id = $d['id'];
    		$url = $d['url'];
    		$created = $d['created'];
    		$ip = $d['ip'];

    		echo "ID: ".$id. "   URL: ".$url.
    		"   CREATED: ".$created."   IP:".$ip."<br />";
    	}*/
    }

    //return new Response('Thank you for your feedback!', 201);
})
->method('GET');

$app->match('/generator', function () use ($app){
    
   
    /*foreach ($app['session']->get('schema') as $schema) {
        $data = array(
            "field" => $schema["Field"],
            "type" => $schema["Type"],
            "null" => $schema["Null"],
            "key" => $schema["Key"],
            "default" => $schema["Default"],
            "extra" => $schema["Extra"]);
        var_dump($data);

    }*/
    $generator = new Generator\generator($app);
    $generator->generate("model", "modela");

    $generator->generate("controller", "controller");

    $generator->generate("view", "view");

    $generator->generate(null, "null");

    // TODO. Meterlo en _generate_model()
    /*mkdir("../generator/".$app_name."/"); 
    $model = fopen("../generator/".$app_name."/".$db_name."_model.php", "w");

    if(!$model) die("unable to create model file");

  
*/
})
->method('GET');

$app->run();