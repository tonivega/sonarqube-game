<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$debug = true; // @TODO only from localhost requests

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

$app['debug'] = $debug;

// YML config
$app->register(new DerAlex\Silex\YamlConfigServiceProvider(__DIR__ . '/../config/settings.yml'));

// URL generation service
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

// Views configuration.
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));

// Future doctrine integration (Reads YML config)
//$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
//    'db.options' => $app['config']['database']
//));

$app->mount('/', \Privalia\SQHeal\Controller\Home::getControllerInstance($app));

$app->run();