<?php

session_start();

include('config/config.php');
include('../lib/bdd.lib.php');


$vue = 'listecategorie.phtml';
$menuSelected = 'listeCategorie';

try {
    
    /* 1 : Connexion au serveur de Base de Données */
    $dbh = new PDO(DB_SGBD.':host='.DB_SGBD_URL.';dbname='.DB_DATABASE.';charset='.DB_CHARSET, DB_USER, DB_PASSWORD);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sth = $dbh->prepare('SELECT c1.c_id, c1.c_title , c2.c_title as parent FROM b_categorie c1 LEFT JOIN b_categorie c2 ON c1.c_parent = c2.c_id ');
    $sth->execute();
    
    $flashbag = getFlashBag(); 
    
    $categories = $sth->fetchAll(PDO::FETCH_ASSOC);
 
}


catch(PDOException $e)
{
   $vue = 'erreur.phtml';
    //Si une exception est envoyée par PDO (exemple : serveur de BDD innaccessible) on arrive ici
    $messageErreur = 'Une erreur de connexion a eu lieu :'.$e->getMessage();
}



include('tpl/layout.phtml');










?>