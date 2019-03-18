<?php
session_start();
include('config/config.php');
include('../lib/bdd.lib.php');

$menuSelected = 'addCategorie';   //menu qui sera sélect dans la nav du layout

$vue = 'addcategorie.phtml';
$id = null;
$titrecategorie = ''; 
$parentcategorie = '';

try
{
    /* 1 : Connexion au serveur de Base de Données */
    $dbh = new PDO(DB_SGBD.':host='.DB_SGBD_URL.';dbname='.DB_DATABASE.';charset='.DB_CHARSET, DB_USER, DB_PASSWORD);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    
    

    if(array_key_exists('titlecategorie', $_POST)) {
            
        $errorForm = [];
            
        $titrecategorie = $_POST['titlecategorie'];
            
        if($titrecategorie == '') {
            $errorForm[] = "Veuillez saisir un titre pour votre catégorie";
        }
            
        $parentcategorie = $_POST['parentcategorie'];
            
        if($parentcategorie == '') {
            $errorForm[] = "Veuillez saisir un parent";
        }
            

        
    
        
        if(count($errorForm) == 0) {
            
            $sth = $dbh->prepare("INSERT INTO b_categorie (c_title, c_parent) VALUES (:title, :parent)"); 
                
            $sth->bindValue(':title', $titrecategorie, PDO::PARAM_STR);
            $sth->bindValue(':parent', $parentcategorie, PDO::PARAM_STR);
            $sth->execute();
        
            addFlashBag('La catégorie a bien été ajoutée');
                
            header('Location:listecategorie.php');
            exit();
        }

    }

}

catch(PDOException $e)
{
   $vue = 'erreur.phtml';
    //Si une exception est envoyée par PDO (exemple : serveur de BDD innaccessible) on arrive ici
    $messageErreur = 'Une erreur de connexion a eu lieu :'.$e->getMessage();
}



include('tpl/layout.phtml');
?>


