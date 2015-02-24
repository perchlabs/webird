<?php

$router->setDefaultModule('web');
$router->addStdModule('admin');
$router->addStdModule('api');

$router->add('/', 'index::index');

$router->add('/signin/redirectoauth/([a-z]+)/([a-zA-Z0-9=+/]+)', [
    'module'      => 'web',
    'controller'  => 'session',
    'action'      => 'signinRedirectOauth',
    'provider'    => 1,
    'nonce'       => 2
]);
$router->add('/signin/oauth/([a-z]+)/:params', [
    'module'      => 'web',
    'controller'  => 'session',
    'action'      => 'signinOauth',
    'provider'    => 1,
    'params'      => 2
]);
$router->add('/signin', 'session::signin');




$router->add('/forgot-password', 'userspublic::forgotPassword');
$router->add('/confirm/{code}', 'userspublic::confirmEmail');
$router->add('/reset-password/{code}', 'userspublic::resetPassword');
