<?php
require_once '../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$config = [];
$config['host']     = $_ENV['DB_HOST'];
$config['login']    = $_ENV['DB_USER'];
$config['password'] = $_ENV['DB_PASS'];
$config['database'] = $_ENV['DB_NAME'];

return $config;
