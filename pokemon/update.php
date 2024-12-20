<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
if(!isset($_SESSION['user'])) {
    header('Location:.');
    exit;
}
try {
    $connection = new \PDO(
      'mysql:host=localhost;dbname=productdatabase',
      'productuser',
      'productpassword',
      array(
        PDO::ATTR_PERSISTENT => true,
        PDO::MYSQL_ATTR_INIT_COMMAND => 'set names utf8')
    );
} catch(PDOException $e) {
    //echo 'no connection';
    header('Location: ..');
    exit;
}
if(isset($_POST['id'])) {
    $id = $_POST['id'];
} else {
    //echo 'no id';
    header('Location: ..');
    exit;
}

//control 
$user = null;
if(isset($_SESSION['user'])){
$user = $_SESSION['user'];
}
if(($user === 'even' && $id % 2 != 0)||
($user === 'odd' && $id % 2 == 0)){
    header('Location:.');
    exit;
}


if(isset($_POST['name'])) {
    $name = trim($_POST['name']);
} else {
    header('Location: .');
    //echo 'no name';
    exit;
}
if(isset($_POST['price'])) {
    $price = $_POST['price'];
} else {
    header('Location: .');
    //echo 'no price';
    exit;
}
//debería meter la misma validación que antes en store.php
$sql = 'update pokemon set name = :name, peso = :peso, tipo = :tipo, numero = :numero where id = :id';
$sentence = $connection->prepare($sql);
$parameters = ['name' => $name, 'price' => $price, 'id' => $id];
foreach($parameters as $nombreParametro => $valorParametro) {
    $sentence->bindValue($nombreParametro, $valorParametro);
}
try {
    $sentence->execute();
    $resultado = $sentence->rowCount();
    $url = '.?op=editpokemon&result=' . $resultado;
} catch(PDOException $e) {
    $resultado = 0;
    $_SESSION['old']['name'] = $name;
    $_SESSION['old']['price'] = $price;
    $url = 'edit.php?id=' . $id . '&op=editpokemon&result=' . $resultado;
}
header('Location: ' . $url);