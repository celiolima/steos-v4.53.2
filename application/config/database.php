<?php

defined('BASEPATH') or exit('No direct script access allowed');

$active_group = 'default';
$query_builder = true;
$db['default'] = [
    'dsn' => $_ENV['DB_DSN'] ?? '',
    'hostname' => $_ENV['DB_HOSTNAME'] ?? $_ENV['MYSQL_HOST'] ?? 'steosMySql',
    'username' => $_ENV['DB_USERNAME'] ?? $_ENV['MYSQL_USER'] ?? 'steos',
    'password' => $_ENV['DB_PASSWORD'] ?? $_ENV['MYSQL_PASSWORD'] ?? 'steos',
    'database' => $_ENV['DB_DATABASE'] ?? $_ENV['MYSQL_DATABASE'] ?? 'steos',
    'dbdriver' => $_ENV['DB_DRIVER'] ?? 'mysqli',
    'dbprefix' => $_ENV['DB_PREFIX'] ?? '',
    'pconnect' => false,
    'db_debug' => (ENVIRONMENT !== 'production'),
    'cache_on' => false,
    'cachedir' => '',
    'char_set' => $_ENV['DB_CHARSET'] ?? 'utf8mb4',
    'dbcollat' => $_ENV['DB_COLLATION'] ?? 'utf8mb4_unicode_ci',
    'swap_pre' => '',
    'encrypt' => false,
    'compress' => false,
    'stricton' => false,
    'failover' => [],
    'save_queries' => true,
];
