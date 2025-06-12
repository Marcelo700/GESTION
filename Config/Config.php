<?php
// Obtener el protocolo actual (http o https)
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';

// Obtener el host actual
$host = $_SERVER['HTTP_HOST'];

// Obtener el puerto si es diferente al predeterminado
$port = $_SERVER['SERVER_PORT'];
$port = ($port == '80' || $port == '443') ? '' : ':' . $port;


defined('BASE_URL') || define('BASE_URL', $protocol . $host  . '/gestion/');

const HOST = "localhost";
const USER = "root";
const PASS = "";
const DB = "gestion_archivos";
const CHARSET = "charset=utf8";
?>