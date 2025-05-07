<?php
$config['serveur']='localhost';
$config['login'] = 'root';
$config['mdp'] ='kokoloko';
$config['bd'] = 'site commerce';

try{
    $db = new PDO('mysql:host='.$config['localhost'].';dbname='.$config['site commerce'],$config['login'],$config['===']);
}
catch(Exception $e){
    echo $e;
}
echo "connection réussi !";