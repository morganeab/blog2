<?php
session_start();
/** On veut modifier juste la publication de l'article
 * On pourrait même appeler cette page en AJAX mais pour le moment on le faire avec une requête HTTP classique
 */

/**On inclu d'abord le fichier de configuration */
include('config/config.php');
/**On inclu ensuite nos librairies dont le programme a besoin */



/** On définie nos variables nécessaire pour la vue et le layout */
$vue = 'adduser.phtml';    //vue qui sera affichée dans le layout - m^me vue que l'ajout




/** Maintenant on gère le fonctionnement de la page
 * Block try pour essayer tout le programme
 */
try
{
    /**On récupère l'id de l'article à modifier */
    if(array_key_exists('id',$_GET))
    {
        /**Connexion à la bdd */
        $dbh = new PDO(DB_SGBD.':host='.DB_SGBD_URL.';dbname='.DB_DATABASE.';charset='.DB_CHARSET, DB_USER, DB_PASSWORD);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $id = $_GET['id'];

        /** On recherche l'article dans la base de données */
        $sth = $dbh->prepare('SELECT u_valide FROM b_user WHERE u_id = :id');
        $sth->bindValue('id',$id,PDO::PARAM_INT);
        $sth->execute();
        $article = $sth->fetch(PDO::FETCH_ASSOC);

        $sth = $dbh->prepare('UPDATE b_user SET u_valide=:valide WHERE u_id=:id');
        $sth->bindValue('id',$id,PDO::PARAM_INT);
        $sth->bindValue('valide',!$article['a_valide'],PDO::PARAM_INT);
        $sth->execute();

       // $valide = $sth->fetch(PDO::FETCH_ASSOC);
        header('Location:listeuser.php');
        exit();
    }
}
catch(PDOException $e)
{
    $vue = 'erreur.phtml';
    //Si une exception est envoyée par PDO (exemple : serveur de BDD innaccessible) on arrive ici
    $messageErreur = 'Une erreur de connexion a eu lieu :'.$e->getMessage();
}

include('tpl/layout.phtml');
