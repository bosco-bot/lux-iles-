<?php

/**
 * Point d'entrée à la racine - Gère les routes Laravel sans /public dans l'URL
 * 
 * Ce fichier permet d'accéder au site sans /public dans l'URL
 * quand le .htaccess ne fonctionne pas ou n'est pas pris en compte.
 */

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/vendor/autoload.php';

// Bootstrap Laravel
/** @var Application $app */
$app = require_once __DIR__.'/bootstrap/app.php';

// Ajuster l'URI si l'application est dans un sous-dossier /lux-iles/
$basePath = '/lux-iles';
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$pathInfo = parse_url($requestUri, PHP_URL_PATH);

// Si le chemin commence par le sous-dossier, l'enlever
if (strpos($pathInfo, $basePath) === 0) {
    $pathInfo = substr($pathInfo, strlen($basePath));
    $pathInfo = $pathInfo ?: '/';
    
    // Mettre à jour $_SERVER avant de capturer la requête
    $_SERVER['REQUEST_URI'] = $pathInfo . (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '');
    $_SERVER['SCRIPT_NAME'] = $basePath . '/index.php';
}

// Créer la requête après avoir ajusté $_SERVER
$request = Request::capture();

// Gérer la requête
$app->handleRequest($request);

