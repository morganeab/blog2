<?php

/**
 * CONFIGURATION
 */
const DB_SGBD = 'mysql';
const DB_SGBD_URL = 'localhost';
const DB_DATABASE = 'morganeab_';
const DB_CHARSET = 'utf8';
const DB_USER = 'morganeab';
const DB_PASSWORD = 'OWNiN2ZlZWNjZGRlODVkNTgzNzVlYzhj3Wa!';

?>

<!-- A mettre dans le php pour appeler MyAdmin -->

<?php 
include('config/config.php');


 $dbh = new PDO(DB_SGBD.':host='.DB_SGBD_URL.';dbname='.DB_DATABASE.';charset='.DB_CHARSET, DB_USER, DB_PASSWORD);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);