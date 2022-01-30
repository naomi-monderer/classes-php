<?php
if(!isset($_SESSION))
{
    session_start();
    
}
require 'user-pdo.php';
$user = new Userpdo();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <?php include('user-pdo.php')?>
</head>

<body>
    <form method="POST">
        <input type="text" name="login" placeholder="identifiant">
        <input type="text" name="firstname" placeholder="prénom">
        <input type="text" name="lastname" placeholder="nom">
        <input type="email" name="email" placeholder="email">
        <input type="password" name="password" placeholder="MDP">
        <input type="password" name="password2" placeholder="confirmez MDP">
        <input type="submit" name="submit" value="s'inscrire">
    
    </form>

    <form action="" methode="get">
        <input type="text" name="login" placeholder="identifiant">
        <input type="password" name="password" placeholder="MDP">
        <input type="submit" name="submitCo" value="se connecter">
    </form>

    <form action="" method="post">
        <input type="text" name="login"  value = <?php echo $_SESSION['dataUser']['login'];?> >
        <input type="text" name="firstname" value = <?php echo $_SESSION['dataUser']['firstname'];?> >
        <input type="text" name="lastname"  value=<?php echo $_SESSION['dataUser']['lastname'];?>>
        <input type="email" name="email"  value=<?php echo $_SESSION['dataUser']['email'];?>>
        <input type="submit" name="submitUpdate" value="update">
    </form>

    <form action="" method="post">
    <input type='submit' name='deconnexion' value="deco">
    </form>

    <?php
    if (isset($_POST['submit']))
    {
        $RetourFonctionInscription = $userpdo->register($_POST['login'],$_POST['password'],$_POST['password2'],$_POST['email'],$_POST['firstname'],$_POST['lastname']);
    }

    //  if(isset($_GET['submitCo']))
    // {
    //    //$retourDeLaFonction = 
    //    $user->connect($_GET['login'],$_GET['password']);
    //  }

    // if(isset($_POST['deconnexion']))
    //   {
    //     $retourDeconnexion = $user->disconnect();
    //   }

    // if(isset($_POST['submitUpdate']))
    // {
    //     $user->update($_POST['login'],$_POST['email'],$_POST['firstname'],$_POST['lastname']);
    // }
    
    // $user->getAllInfos();

    ?>
</body>

</html>