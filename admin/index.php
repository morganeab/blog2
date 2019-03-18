<?php
session_start();

if(!isset($_SESSION['connected']) || $_SESSION['connected'] !== true)
    //header('Location:login.php');


/**On inclu d'abord le fichier de configuration */
include('config/config.php');


/** On définie nos variables nécessaire pour la vue et le layout */
$vue = 'index.phtml';   //vue qui sera affichée dans le layout
$menuSelected = 'home';



include('tpl/layout.phtml');


?>