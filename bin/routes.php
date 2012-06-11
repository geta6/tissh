<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

//--------------------------------------------------------------
// Main Routes
//--------------------------------------------------------------
$app->match('/', function () use ($app) {
  exec('/usr/local/bin/sysinfo', $e);
  $sys = str_replace(array('[0m', '[35m'), '', implode("\n", $e))."\n\n";
  return new Response($app['twig']->render('index.twig', array('sys' => $sys)), 418);
});

//--------------------------------------------------------------
// Commands
//--------------------------------------------------------------
$app->match('/exec', function (Request $req) use ($app) {
  include_once __DIR__ . '/cmd.class.php';
  $cmd = new CMD($req->get('cmd'));
  return new Response($cmd->execute(), 200, array( 'Content-Type' => 'tissh/return' ));
})->assert('cmd', '.*');


$app->match('/init', function (Request $req) use ($app) {
  return new Response(json_encode(array(
    'host' => $req->server->get('SERVER_NAME'),
    'user' => preg_replace('/.*?\((.*?)\).*/', '$1', exec('id'))
  )), 200, array( 'Content-Type' => 'application/json' ));
});


//--------------------------------------------------------------
// API
//--------------------------------------------------------------
$app->match('/api', function (Request $req) use ($app) {
  include_once __DIR__ . '/cmd.class.php';
  $cmd = new CMD($req->get('cmd'));
  $cmd = json_decode($cmd->execute());
  return new Response($cmd->text, 200, array( 'Content-Type' => 'text/plain' ));
})->assert('cmd', '.*');
