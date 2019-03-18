<?php
session_start();


if(isset($_SESSION['connected']) && $_SESSION['connected'] === true)
    header('Location:index.php');

//Si l'utilisateur a les droits !!! 
$_SESSION['connected'] = true;
$_SESSION['user'] = ['id'=>10,'prenom'=>'Morgane'];


?>