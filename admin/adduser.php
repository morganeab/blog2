<?php
session_start();
/** On veut ajouter un article. On doit soit :
 * 1. Afficher le formulaire d'ajout
 * 2. Récupérer les données du formulaire et les enregistrer ou afficher les erreurs 
 */

/**On inclu d'abord le fichier de configuration */
include('config/config.php');
/**On inclu ensuite nos librairies dont le programme a besoin */
include('../lib/bdd.lib.php');
$menuSelected = 'addUser';   //menu qui sera sélect dans la nav du layout

/** On définie nos variables nécessaire pour la vue et le layout */
$vue = 'adduser.phtml';      //vue qui sera affichée dans le layout



/** Variables permettant de réinjectées les données de formulaire
 * On les définies à vide pour le premier affichage du formulaire (pas de Notice not defined ;) )
 */
$id=null; //pas d'id
$prenom = '';
$nom = '';
$email  = '';
$motdepasse = '';
$valide = 1;
$roleUser = 'ROLE_AUTHOR';

/** Maintenant on gère le fonctionnement de la page
 * Block try pour essayer tout le programme
 */
try
{


    
   
    /**S'il a des données en entrée */
    if(array_key_exists('email',$_POST))
    {
        
        $dbh = new PDO(DB_SGBD.':host='.DB_SGBD_URL.';dbname='.DB_DATABASE.';charset='.DB_CHARSET, DB_USER, DB_PASSWORD);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $errorForm = []; //Pas d'erreur pour le moment sur les données

        /* Récupération des données de l'article */
        $prenom = trim($_POST['prenom']);
        $nom = $_POST['nom'];
        $email = $_POST['email'];
        $motdepasse = $_POST['motdepasse'];
        $verifmotdepasse = $_POST['verifmotdepasse'];
        $valide = $_POST['valide'];
        $roleUser = $_POST['role'];

        //le formulaire est posté
        if($email == '')
            $errorForm[] = 'L\'email ne peut-être vide !';

        if($motdepasse != $verifmotdepasse || $motdepasse == '')
            $errorForm[] = 'Le mot de passe ou sa confirmation ne sont pas corrects !';
        
        //On vérifie si l'utilisateur n'est pas déjà dans la base avec cet email (champ unique email !!)

        /** On va récupérer les catégories dans la bdd*/
        $sth = $dbh->prepare('SELECT u_email FROM b_user WHERE u_email = :email');
        $sth->bindValue('email',$email,PDO::PARAM_STR);
        $user = $sth->fetch(PDO::FETCH_ASSOC);
        if($user != false)
            $errorForm[] = '<br> Un utilisateur existe déjà avec cet email.';
    
        /** Si j'ai pas d'erreur j'insert dans la bdd */
        if(count($errorForm) == 0)
        {
            //HAsh du mot de passe
            $motdepasse = password_hash($motdepasse,PASSWORD_DEFAULT);
            
            //Préparation requête
            $sth = $dbh->prepare('INSERT INTO b_user 
            (u_id,u_firstname,u_lastname,u_email, u_password,u_valide,u_role)
            VALUES (NULL,:firstname,:lastname,:email, :password,:valide,:role)');

            //Liage (bind) des valeurs
            $sth->bindValue('firstname',$prenom,PDO::PARAM_STR);
            $sth->bindValue('lastname',$nom,PDO::PARAM_STR);
            $sth->bindValue('email',$email,PDO::PARAM_STR);
            $sth->bindValue('password',$motdepasse,PDO::PARAM_STR);
            $sth->bindValue('valide',$valide,PDO::PARAM_INT);
            $sth->bindValue('role',$roleUser,PDO::PARAM_STR);
            $sth->execute();

            addFlashBag('L\'utilisateur a bien été ajouté');

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