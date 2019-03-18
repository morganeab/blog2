<?php
session_start();
include('config/config.php');
include('../lib/bdd.lib.php');
$menuSelected = 'editArticle'; 

$vue = 'addarticle.phtml';
$titre = '';
$date = '';
$heure ='';
$contenu = '';
$category = '';
$auteur = '';
$chemin_destination = 'photos/';
$id= null;
$photo = '';
$datePubli = new DateTime();
$pictureDisplay = false;
$valideArticle = 1;
$roleUser = 'ROLE_AUTHOR';



try
{
    /* 1 : Connexion au serveur de Base de Données */
    
    $dbh = new PDO(DB_SGBD.':host='.DB_SGBD_URL.';dbname='.DB_DATABASE.';charset='.DB_CHARSET, DB_USER, DB_PASSWORD);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sth = $dbh->prepare('SELECT * FROM b_categorie  ORDER BY c_title ASC');
    $sth->execute();
    $categories = $sth->fetchAll(PDO::FETCH_ASSOC);


    /* 2 : Executer une requête */
    if(array_key_exists('id', $_GET)) {
   
        $id = $_GET['id'];
        $sth = $dbh->prepare('SELECT * FROM b_article WHERE a_id = :id');
        
        $sth->bindValue(':id', $id, PDO::PARAM_INT);
        
        $sth->execute();
        $article = $sth->fetch(PDO::FETCH_ASSOC);
      
      
        
        $titre = $article['a_title'];
        $datePubli = new DateTime($article['a_date_published']);
        $contenu = $article['a_content']; 
        $category = $article['a_categorie'];
        $auteur = $article['a_author'];
        $photo = $article['a_picture'];
        $roleUser = $article['a_role'];;
       
        
    }
    
    
    if(array_key_exists('title', $_POST)) {
        
        $errorForm = []; //Pas d'erreur pour le moment sur les données
        
        $id= $_POST['id']; //on récupère l'id de l'article
    
        $titre = trim($_POST['title']);
        $date = $_POST['date'];
        $heure = $_POST['heure'];
        $datePubli = new DateTime($date.$heure);
        $contenu = trim($_POST['content']);
        $category = $_POST['category'];
        $auteur = $_POST['author']; 
        $photo = isset($_POST['oldPicture'])?$_POST['oldPicture']:null; //On met l'image à NULL en attendant de voir si une image est transmise ! 
        $roleUser = $_POST['role'];
          //le formulaire est posté
        if($titre == '')
            $errorForm[] = 'Le titre ne peut-être vide !';

        if($datePubli===false)
            $errorForm[] = 'La date de publication est erronée ou antérieur à la date du jour !';
            
       /*IMAGE */
       
        if($_FILES['photo']['name'] != '') {
            if ($_FILES['photo'] ['error']) {
                switch ($_FILES['photo'] ['error']) {
                    case 1: // UPLOAD ERR_INI_SIZE
                    $errorForm[] = "La taille du fichier est plus grande que la limite autorisée par le serveur (paramètre upload_max_filesize du fichier php.ini).";
                    break;
                
                    case 2: //UPLOAD_ERROR_FORM_SIZE
                   $errorForm[] = "La taille du fichier est plus grande que la limite autorisée par le formulaire (paramètre post_max_size du fichier php.ini)";
                    break;
                
                    case 3: //UPLOAD_ERR_PARTIAL
                    $errorForm[] = "L'envoi du fichier a été interrompu pendant le transfert.";
                    break;
                
                    case 4: //UPLOAD_ERR_NO_FILE
                    $errorForm[] = "La taille du fichier que vous avez envoyé est nulle.";
                    break;
                }
            }
            
            if((isset($_FILES['photo']['name'])&&($_FILES['photo']['error']== UPLOAD_ERR_OK))) {
                $tmp_name = $_FILES["photo"]["tmp_name"];
                $tmpNewPhoto = uniqid().'-'.basename($_FILES["photo"]["name"]);
                move_uploaded_file($tmp_name, $chemin_destination.$tmpNewPhoto);
                
                if(file_exists($chemin_destination.$photo) && $photo != '' ) {
                    unlink($chemin_destination.$photo);
                }
                $photo = $tmpNewPhoto;
            }
            else {
                $errorForm[] = "Le fichier n'a pas pu être copié dans le répertoire photos.";
            }
        }  
        
      /* FIN IMAGE */  
      
        if(count($errorForm) == 0)
        {    
           
            //echo 'yfujdsf';exit();
            $sth = $dbh->prepare('UPDATE b_article SET a_title = :title, a_date_published = :date, a_content = :content, a_picture = :picture, 
            a_categorie = :categories, a_author = :author, a_valide = :valide, a_role = :role WHERE a_id = :id');
            $sth->bindValue('id',$id,PDO::PARAM_INT);
            $sth->bindValue('title', $titre, PDO::PARAM_STR);
            $sth->bindValue('date', $datePubli->format('Y-m-d H:i:s'));
            $sth->bindValue('content', $contenu, PDO::PARAM_STR);
            $sth->bindValue('picture', $photo, PDO::PARAM_STR);
            $sth->bindValue('categories', $category, PDO::PARAM_INT);
            $sth->bindValue('author', $auteur, PDO::PARAM_INT);
            $sth->bindValue('valide',$valideArticle,PDO::PARAM_BOOL);
            $sth->bindValue(':role',$roleUser,PDO::PARAM_STR);
            
            //$sth->debugDumpParams();
            $sth->execute();
          //
            
            
            addFlashBag('L\'article a bien été modifé');
            header('Location:listearticle.php');
            exit(); //on arrête le script après redirection pour éviter que PHP ne continu son boulot inutilement !
        
            
        }
    }   
    
    /** On vérifie si l'image existe sur le disque pour la passer à la vue */
    if(file_exists($chemin_destination.$photo) && $photo != null)
        $pictureDisplay = true;
}

catch(PDOException $e)
{
    $vue = 'erreur.phtml';
    //Si une exception est envoyée par PDO (exemple : serveur de BDD innaccessible) on arrive ici
    $messageErreur = 'Une erreur de connexion a eu lieu :'.$e->getMessage();
}

include('tpl/layout.phtml');


?>