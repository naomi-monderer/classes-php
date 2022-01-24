<?php
if(!isset($_SESSION))
{
    session_start();
    var_dump($_SESSION);
}
require 'user.php';
$user = new User();


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <form method="POST">
        <input type="text" name="login" placeholder="identifiant">
        <input type="text" name="firstname" placeholder="prénom">
        <input type="text" name="lastname" placeholder="nom">
        <input type="email" name="email" placeholder="email">
        <input type="password" name="password" placeholder="MDP">
        <input type="password" name="password2" placeholder="confirmez MDP">
        <input type="submit" name="submit" value="valider">
        <?php

        // if (isset($_POST['submit'])) {
        //     $user->register($_POST['login'], $_POST['password'], $_POST['password2'], $_POST['email'], $_POST['firstname'], $_POST['lastname']);
        // }

        ?>
    </form>

    <form action="" methode="post">
        <input type="text" name="login" placeholder="identifiant">
        <input type="password" name="password" placeholder="MDP">
        <input type="submit" name="submitCo" value="valider">
    </form>

    <form action="" methode="post">
    <input type='submit' name='deconnexion' value="deco">
    </form>

    <?php
     if(isset($_POST['submitCo']))
    {
        $_POST['login'] = "mae";
        $_POST['password'] = "mae";
       $retourDeLaFonction = $user->connect($_POST['login'],$_POST['password']);
     }
    
    
    if(isset($_POST['submitCo']))
     {    echo "<h1>".$retourDeLaFonction."</h1>"; 
      }
    

      if(isset($_POST['deconnexion']))
      {
        $retourDeconnexion = $user->disconnect();
      }


      $retourDelete = $user->delete();

    ?>
</body>

</html>