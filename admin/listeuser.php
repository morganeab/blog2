<?php
session_start();
/** On veut lister les articles. On doit soit :
 * 1. Récupérer tous les articles en bdd
 * 2. Afficher les articles ! 
 */

/**On inclu d'abord le fichier de configuration */
include('config/config.php');
/**On inclu ensuite nos librairies dont le programme a besoin */
include('../lib/bdd.lib.php');
$menuSelected = 'listeUser';   //menu qui sera sélect dans la nav du layout
/** On définie nos variables nécessaire pour la vue et le layout */
$vue = 'listeuser.phtml';      //vue qui sera affichée dans le layout


try
{
    $dbh = new PDO(DB_SGBD.':host='.DB_SGBD_URL.';dbname='.DB_DATABASE.';charset='.DB_CHARSET, DB_USER, DB_PASSWORD);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sth = $dbh->prepare('SELECT * FROM b_user');
    $sth->execute();
   
    $flashbag = getFlashBag();
    $users = $sth->fetchAll(PDO::FETCH_ASSOC);

}
catch(PDOException $e)
{
    
    $vue = 'erreur.phtml';
    //Si une exception est envoyée par PDO (exemple : serveur de BDD innaccessible) on arrive ici
    $messageErreur = 'Une erreur de connexion a eu lieu :'.$e->getMessage();
}

include('tpl/layout.phtml');
