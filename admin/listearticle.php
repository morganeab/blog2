<?php
session_start();
include('config/config.php');
include('../lib/bdd.lib.php');
$menuSelected = 'listeArticle';   //menu qui sera sélect dans la nav du layout
$vue = 'listearticle.phtml';
$chemin_destination = 'photos/';

try
{
    /* 1 : Connexion au serveur de Base de Données */
    
    $dbh = new PDO(DB_SGBD.':host='.DB_SGBD_URL.';dbname='.DB_DATABASE.';charset='.DB_CHARSET, DB_USER, DB_PASSWORD);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    /* 2 : Executer une requête */
  
   

    $sth = $dbh->prepare('SELECT * FROM b_article INNER JOIN b_user ON a_author=u_id INNER JOIN b_categorie ON a_categorie = c_id');
    $sth->execute();

    /* 3. Récupère les résultats de la requête - Le jeu d'enregistrement ! */
    $flashbag = getFlashBag();
    $articles = $sth->fetchAll(PDO::FETCH_ASSOC);


    /* 4. Afficher ou traiter l'enregistrement */
 
  

    
}
catch(PDOException $e)
{
    $vue = 'erreur.phtml';
    //Si une exception est envoyée par PDO (exemple : serveur de BDD innaccessible) on arrive ici
    $messageErreur = 'Une erreur de connexion a eu lieu :'.$e->getMessage();
}

include('tpl/layout.phtml');


?>