<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', '');
    $pdo->exec('CREATE DATABASE IF NOT EXISTS e_randis_php_testing');
    echo "DATABASE e_randis_php_testing CREATED OR ALREADY EXISTS SUCCESSFULLY.\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
