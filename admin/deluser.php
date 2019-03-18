<?php
session_start();
include('config/config.php');
include('../lib/bdd.lib.php');

$menuSelected = '';

try
{
    /* 1 : Connexion au serveur de Base de Données */
    $dbh = new PDO(DB_SGBD.':host='.DB_SGBD_URL.';dbname='.DB_DATABASE.';charset='.DB_CHARSET, DB_USER, DB_PASSWORD);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    

    /* 2 : Executer une requête */
    if(array_key_exists('id', $_GET)) {
        

        $id = $_GET['id'];
        // On récupère les USERS 
        $sth = $dbh->prepare('SELECT * FROM b_user WHERE u_id = :id');
        $sth->bindValue(':id', $id, PDO::PARAM_INT);
        $sth->execute();
        $user = $sth->fetch(PDO::FETCH_ASSOC);
        
        // On récupère tout les articles
        $sth = $dbh->prepare('SELECT COUNT(*) as nombre FROM b_article WHERE a_author = :id');
        $sth->bindValue(':id', $id, PDO::PARAM_INT);
        $sth->execute();
        $nombreArticle = $sth->fetch(PDO::FETCH_ASSOC);
        
        if($nombreArticle['nombre'] > 0) {
            addFlashBag('L\'utilisateur n\'a pas pu être supprimé');
        }
        else {
        // On supprime toute la ligne de la base de données
        $sth = $dbh->prepare('DELETE FROM b_user WHERE u_id = :id');
        $sth->bindValue(':id', $id, PDO::PARAM_INT);
        $sth->execute();
        
        addFlashBag('L\'utilisateur a bien été supprimé');
        
        }
        
    }
    header('Location:listeuser.php');
    exit(); //Toujours après header location

}

catch(PDOException $e)
{
    $vue = 'erreur.phtml';
    //Si une exception est envoyée par PDO (exemple : serveur de BDD innaccessible) on arrive ici
    $messageErreur = 'Une erreur de connexion a eu lieu :'.$e->getMessage();
}

?>