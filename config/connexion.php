<?php
function connect($config = null){
    if ($config === null) {
        $config = require __DIR__ . '/parametres.php';
    }

    try {
        $db = new PDO(
            'mysql:host=' . $config['host'] . ';dbname=' . $config['database'],
            $config['login'],
            $config['password']
        );
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(Exception $e){
        $db = null;
        echo 'Erreur PDO : ' . $e->getMessage();
    }

    return $db;
}
