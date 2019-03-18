<?php
session_start();
include('config/config.php');
include('../lib/bdd.lib.php');


$vue = 'adduser.phtml';
$prenom = '';
$nom = '';
$email ='';
$roleUser ='';
$valide = null ;
$menuSelected = 'editUser';
try
{
    $dbh = new PDO(DB_SGBD.':host='.DB_SGBD_URL.';dbname='.DB_DATABASE.';charset='.DB_CHARSET, DB_USER, DB_PASSWORD);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    /* 2 : Executer une requête */
    if(array_key_exists('id', $_GET)) {
       
   
        $id = $_GET['id'];
        $sth = $dbh->prepare('SELECT * FROM b_user WHERE u_id = :id');
        
        $sth->bindValue(':id', $id, PDO::PARAM_INT);
        
        $sth->execute();
        $modifUsers = $sth->fetch(PDO::FETCH_ASSOC);
        
      
        $prenom = $modifUsers['u_firstname'];
        $nom = $modifUsers['u_lastname'];
        $email = $modifUsers['u_email'];
        $roleUser = $modifUsers['u_role'];
        $valide = $modifUsers['u_valide'] ;
        $motdepasse = $modifUsers['u_password'];
        $verifmotdepasse = $motdepasse; 
              
        
        
        
    }
    
    
    if(array_key_exists('email', $_POST)) {
        
        $errorForm = []; //Pas d'erreur pour le moment sur les données
        
        $id= $_POST['id']; //on récupère l'id de l'article
    
        $prenom = trim($_POST['prenom']);
        $nom = $_POST['nom'];
        $email = $_POST['email'];
        $motdepasse = $_POST['motdepasse'];
        $verifmotdepasse = $_POST['verifmotdepasse'];
        $valide = $_POST['valide'];
        $roleUser = $_POST['role'];
            
          //le formulaire est posté
        if($prenom == '')
            $errorForm[] = 'Le prénom ne peut-être vide !';

        if($nom == '')
            $errorForm[] = 'Le nom ne peut-être vide !';
            
        if($email == '')
        $errorForm[] = 'L\'email ne peut-être vide !';

        if($motdepasse != $verifmotdepasse || $motdepasse == '')
        $errorForm[] = 'Le mot de passe ou sa confirmation ne sont pas corrects !';
    
        //On vérifie si l'utilisateur n'est pas déjà dans la base avec cet email (champ unique email !!)

        /** On va récupérer les catégories dans la bdd*/
        $sth = $dbh->prepare('SELECT u_email FROM b_user WHERE u_email = :email');
        $sth->bindValue(':email', $email ,PDO::PARAM_STR);
        $user = $sth->fetch(PDO::FETCH_ASSOC);
        if($user != false)
            $errorForm[] = '<br> Un utilisateur existe déjà avec cet email.';
    
    
  
        if(count($errorForm) == 0) {    
            $sth = $dbh->prepare('UPDATE b_user SET u_firstname = :firstname, u_lastname = :lastname, u_email = :email, u_password = :password, 
            u_valide = :valide, u_role = :role WHERE u_id = :id');
            $sth->bindValue('id',$id,PDO::PARAM_INT);
            $sth->bindValue('firstname',$prenom,PDO::PARAM_STR);
            $sth->bindValue('lastname',$nom,PDO::PARAM_STR);
            $sth->bindValue('email',$email,PDO::PARAM_STR);
            $sth->bindValue('password',$motdepasse,PDO::PARAM_STR);
            $sth->bindValue('valide',$valide,PDO::PARAM_INT);
            $sth->bindValue('role',$roleUser,PDO::PARAM_STR);
            $sth->execute();
           
            
            addFlashBag('L\'utilisateur a bien été modifié');

            //redirection vers la liste des articles (PRG - Post Redirect Get)
            header('Location:listeuser.php');
            exit(); //on arrête le script après redirection pour éviter que PHP ne continu son boulot inutilement !
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