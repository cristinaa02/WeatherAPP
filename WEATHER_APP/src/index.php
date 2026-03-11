<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("controller/WeatherController.php");
$controller = new WeatherController();

if (isset($_GET['action'])) {
    $action = $_GET['action'];
} else {
    $action = 'index';
}

if ($action == 'buscar') {
    $controller->buscar();
} elseif ($action == 'actual') {
    $controller->actual();
} elseif ($action == 'porHoras') {
    $controller->porHoras();
} elseif ($action == 'semanal') {
    $controller->semanal();
} else {
    $controller->index();
}
?>