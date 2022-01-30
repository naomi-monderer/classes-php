<?php
if(!isset($_SESSION))
{
    session_start();
    
}
require 'user-pdo.php';
$userpdo = new Userpdo();
?>
 