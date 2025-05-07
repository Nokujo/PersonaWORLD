<?php
function connect($config){
try{
    $db = new PDO(
        'mysql:host=' . $config['serveur'] . ';dbname=' . $config['db'],
        $config['login'],
        $config['password']
    );
    
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(Exception $e){
    $db = NULL;
    echo $e;
}
return $db;
}
?>
