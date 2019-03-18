<?php
session_start();
include('config/config.php');
include('../lib/bdd.lib.php');

$menuSelected = 'addArticle';   //menu qui sera sélect dans la nav du layout

$vue = 'addarticle.phtml';

$id=null; //pas d'id
$titre = '';
$date = '';
$heure ='';
$contenu = '';
$category = '';
$auteur = '';
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
    
    $sth = $dbh->prepare('SELECT * FROM b_user WHERE u_role = "ROLE_AUTHOR" OR u_role = "ROLE_ADMIN" ');
    $sth->execute();
    $users = $sth->fetchAll(PDO::FETCH_ASSOC);
    
    

    if(array_key_exists('title', $_POST)) {
            
        $errorForm = [];
            
        $titre = $_POST['title'];
            
        if($titre == '') {
            $errorForm[] = "Veuillez saisir un titre";
        }
            
        $date = $_POST['date'];
        $heure = $_POST['heure'];
        $datePubli = new DateTime($date.$heure);
            
        if($datePubli == '') {
            $errorForm[] = "Veuillez saisir la date et l'heure";
        }
            
        $contenu = $_POST['content'];
            
        if($contenu =='') {
            $errorForm[] = "Veuillez saisir un contenu";
        }
            
        $category = $_POST['category'];
             
        if($category =='') {
            $errorForm[] = "Veuillez choisir une catégorie";
        }
            
        $auteur = $_POST['author']; 
             
        if($auteur == '') {
            $errorForm[] = "Veuillez saisir l'auteur de l'article";
        }
            
        $valideArticle = isset($_POST['valide'])?true:false;
        
        $roleUser = $_POST['role'];
        
        
        
    
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
        
        else {
           
                
            if((isset($_FILES['photo']['name'])&&($_FILES['photo']['error']== UPLOAD_ERR_OK))) {
                $chemin_destination = 'photos/';
                $tmp_name = $_FILES['photo']['tmp_name'];
                $photo = uniqid().'-'.basename($_FILES["photo"]["name"]);
                // déplacement du fichier du répertoire temporaire stocké par défaut dans le répertoire de Destination
                move_uploaded_file($tmp_name , $chemin_destination.$photo);
                
            }
                
            else {
                $errorForm[] = "Le fichier n'a pas pu être copié dans le répertoire photos.";
            }
                
        }
        
 
        if(count($errorForm) == 0) {
            
            $sth = $dbh->prepare("INSERT INTO b_article (a_title, a_date_published, a_content, a_picture, a_categorie, a_author, a_valide, a_role) VALUES (:title, :date, :content, :picture, :categories, :author, :valide, :role)"); 
                
            $sth->bindValue(':title', $titre, PDO::PARAM_STR);
            $sth->bindValue(':date', $datePubli->format('Y-m-d H:i:s'));
            $sth->bindValue(':content', $contenu, PDO::PARAM_STR);
            $sth->bindValue(':picture', $photo, PDO::PARAM_STR);
            $sth->bindValue(':categories', $category, PDO::PARAM_INT);
            $sth->bindValue(':author', $auteur, PDO::PARAM_INT);
            $sth->bindValue(':valide',$valideArticle,PDO::PARAM_BOOL);
            $sth->bindValue(':role',$roleUser,PDO::PARAM_STR);
            $sth->execute();
        
            addFlashBag('L\'article a bien été ajouté');
                
            header('Location:listearticle.php');
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


