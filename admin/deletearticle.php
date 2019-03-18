<?php
session_start();
include('config/config.php');
include('../lib/bdd.lib.php');
$chemin_destination = 'photos/';
$menuSelected = '';   //menu qui sera sélect dans la nav du layout
try
{
    /* 1 : Connexion au serveur de Base de Données */
    
    $dbh = new PDO(DB_SGBD.':host='.DB_SGBD_URL.';dbname='.DB_DATABASE.';charset='.DB_CHARSET, DB_USER, DB_PASSWORD);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    /* 2 : Executer une requête */
    if(array_key_exists('id', $_GET)) {
        $number = $_GET['id'];
        // Nous allons chercher la photo dans la base de données pour pouvoir la supprimer par la suite
        $sth = $dbh->prepare('SELECT a_picture FROM b_article WHERE a_id = :numb');
        $sth->bindValue(':numb', $number, PDO::PARAM_INT);
        $sth->execute();
        
        // On la récupère de la base et on la supprime en local
        $picture = $sth->fetch(PDO::FETCH_ASSOC);
        $suppPicture = $picture['a_picture'];
        
        if(file_exists($chemin_destination.$suppPicture) && $suppPicture != '' ) {
            unlink($chemin_destination.$suppPicture);
        }
        
        // On supprime toute la ligne de la base de données
        $sth = $dbh->prepare('DELETE FROM b_article WHERE a_id = :numb');
        $sth->bindValue(':numb', $number, PDO::PARAM_INT);
        $sth->execute();
        
        addFlashBag('L\'article a bien été supprimé');
        header('Location:listearticle.php');
        exit(); //Toujours après header location
    }
    
    
}
catch(PDOException $e)
{
    $vue = 'erreur.phtml';
    //Si une exception est envoyée par PDO (exemple : serveur de BDD innaccessible) on arrive ici
    $messageErreur = 'Une erreur de connexion a eu lieu :'.$e->getMessage();
}



?>