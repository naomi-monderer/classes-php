<?php
/* CREER UNE CLASSE */
// $id,$login,$password,$email,$firstname,$lastname,$bdd


class User
{

    // ici ce sont mes attributs, ce sont concrètement dss variables global qui peuvent être appeler 
    // dans n'importe quelle fonction et qui n'est pas propre à une fonction.
    // pour récupérer la valeur d'un attribut : $maVariable = $this->monAttribut
    // pour attribué une valeur à mon attribut : $this->monAttribut = $maVariable
    private $id;
    public $login;
    public $password;
    public $email;
    public $firstname;
    public $lastname;
    private $bdd;



    // ici dans mon construct c'est une fonction qui est automatiquement 
    // lancée quand la classe est instanciée dans la page où je souhaite avoir accès à mes fonctions.
    public function __construct()
    {   $bdd = mysqli_connect("localhost", "root", "root", "classes");
        // $this->id = $id;
        // $this->login = $login;
        // $this->email = $email;
        // $this->firstname = $firstname;
        // $this->lastname = $lastname;
        // $this->bdd = $bdd;
  
    }
        // ici je stock ma variable bdd ou il y a ma fonction de connexion à la bdd
        // et je la stocke ensuite dans mes attributs pour m'en servir comme pipeline pour les autres fonction
        


    /**
     * Ma fonction register qui permet aux utilisateurs de s'incrire sur la BDD
     */
    public function register($login, $password, $passwordConfirm, $email, $firstname, $lastname)
    {
        // je récupère la connexion à la base de donnée qui est set dans le construct
        $bdd = $this->bdd;

        // j'utilise la fonction htmlspecialchars sur les données que le user m'envoie pour
        // s'enregistrer pour echapper tout les charactères spéciaux.
        $_login = htmlspecialchars($login);
        $_password = htmlspecialchars($password);
        $_passwordConfirm = htmlspecialchars($passwordConfirm);
        $_email = htmlspecialchars($email);
        $_firstname = htmlspecialchars($firstname);
        $_lastname = htmlspecialchars($lastname);

        // j'utilise la fonction trim sur les données que le user m'envoie pour
        // s'enregistrer pour supprimer le premier et le derniere espace si il y en a.
        $login = trim($_login);
        $passwordConfirm = trim($_passwordConfirm);
        $password = trim($_password);
        $email = trim($_email);
        $firstname = trim($_firstname);
        $lastname = trim($_lastname);

        // j'utilise la fonction password_hash sur le mot de passe pour le hasher /!!!!!\ hashé != crypté
        $passwordhash = password_hash($password, PASSWORD_BCRYPT);

        // ici je commence par faire une requete pour récupérer tout les users en bdd
        // qui ont le meme email que celui envoyer pour ensuite vérifier si il n'y aucun user qui a le même identifiant
        $query = "SELECT * FROM utilisateurs WHERE email = '$email'";
        $req = mysqli_query($bdd, $query);

        // ici je vérifie que le user à bien rempli tout les champs
        if (!empty($login) && !empty($password) && !empty($passwordConfirm) && !empty($email) && !empty($firstname) && !empty($lastname)) {
            
            // ici je vérifie bien que la requete que jexecute un peu plus haut à la ligne 65 me retourne aucune ligne,
            // si il me retourne aucune ligne c'est que en bdd il n'existe encore aucun user qui à cet adresse mail comme identifiant,
            // et à l'inverse si il m'en retourne c'est pas bon et du coup je le renvoie le message d'erreur un peu plus bas.
            if (mysqli_num_rows($req) == 0) {

                // ici je vérifie que les mot de passe entrer par le user sont bien concordant (password est bien égale au password confirm)
                if ($password == $passwordConfirm) {

                    // une fois avoir effectué toute les vérifications dont j'ai besoin pour m'assurer de la sécurité de mon site,
                    // je lance la requete qui persiste les donnéese en base de données
                     mysqli_query($bdd, "INSERT INTO `utilisateurs` (`login`, `password`, `email`, `firstname`, `lastname`)
                                        VALUES ('$login', '$passwordhash', '$email', '$firstname', '$lastname')");
                              'tu es inscris';
                } else {
                    return 'les mots de passes ne sont pas similaires.';
                }
            } else {
                return 'cet email existe déjà.';
            }
        } else {
            return 'veuiller remplir tout les champs.';
        }

        foreach($req as $user)
        {
            echo "<pre>";
            echo $req['login'] . " ". $req['password'];
            echo "</pre>";

        }
    }

    public function connect($login, $password)
    {
        $bdd = $this->bdd;
        $error="";
        
        $_login = htmlspecialchars($login);
        $_password = htmlspecialchars($password);
    
        $login = trim($_login);
        $password = trim($_password);
        
        $login = "mae";
        $password = "mae";

        if (!empty($login) && !empty($password)) {
            echo "yes0";
            $req= "SELECT * FROM utilisateurs WHERE login = '$login'";
            $userExist =mysqli_query($bdd,$req);
            $dataUser=mysqli_fetch_assoc($userExist); // mysqli_fetch_assoc(mysqli_result($result));?
            // var_dump($dataUser);

                if(isset($dataUser['login']) == 1)// je pense que les infos de l'utilisateurs sont contenues dans le resultat de ma requête  
                {
                    echo "yes";

                    $passwordhash = $dataUser['password'];
                    //(ou bien $passworhash = $dataUser['password'];) ?

                    if(password_verify($password,$passwordhash))
                    {
                        $_SESSION['dataUser'] = $dataUser; 
                        echo "<pre>";
                        var_dump($dataUser);
                        echo "</pre>";
                        $this->login = $login;
                        $this->password = $password;
                        return "vous êtes connecté";
                    }
                    else
                    {
                        return "le mot de passe est incorect.";
                    }
                }
                else
                {
                    $error = "Cet identifiant n'existe pas.";
                }
            }
            else
            {
                $error = "Veuillez remplir tous les champs.";
            }

            return $error;

    }

    public function disconnect()
    {   $bdd = $this->bdd;

        if (isset($_SESSION['user']) && isset($_POST['deconnexion'])) {
            session_destroy();
        }
        // Où est ce que je créais un boutton deconnexion? pour faire le test
        //comment j'initialise une session

    }

    public function delete()
    {
        $bdd = $this->bdd;
        
        $query = mysqli_query($bdd, "DELETE FROM utilisateurs WHERE id='$this->login'");
        $result = mysqli_fetch_assoc($query);
        if ($query == 1) {
            session_destroy();
        }
    }

    public function update($id, $login, $password, $email, $firstname, $lastname)
    {
        
        // $query = mysqli_query($bdd,"SELECT * FROM utilisateurs");
        // $result = mysqli_fetch_all($query);
        $req = "UPDATE utilisateurs SET login='$login',password='$password',email='$email',firstname='$firstname',lastname='$lastname' WHERE id='$id'";
        $query2 = mysqli_query($bdd, $req);
        $req = "SELECT * FROM utilisateurs where id='$id'";
        $query = mysqli_query($bdd, $req);
        $result2 = mysqli_fetch_all($query);

        $this->id = $id;
        $this->login = $login;
        $this->email = $email;
        $this->firstname = $firstname;
        $this->lastname = $lastname;

        return $result2;
    }

    public function isConnected() // est ce que je dois utiliser des post 
    {
        if (isset($_POST['submit'])) {
            $bdd = mysqli_connect("localhost", "root", "root", "classes");
            $userExist = mysqli_query($bdd, "SELECT * FROM utilisateurs WHERE login='$this->login'");
            $resultUser = mysqli_fetch_all($userExist);
            // comment transmettre à ma requete que je veux l'argument $login?
            if (isset($_POST['login']) == 1) {
                $_SESSION['user'] = $resultUser;
            }
        }
    }

    public function getAllInfos()
    {
    }

    // dans la fonction delete , dans ta query au lieu d'écrire $login t'écrira $this->login
    // $query=mysqli_query($bdd,"SELECT login,password FROM utilisateurs WHERE id='$this->id'");

}

// $objet = new User();
// // $result = $objet->register(2,'Naomixx','1234','naomi@gmail.com','Naomi','Monderer');
// // $result = $objet->register(3,'lala','1234','naomi@gmail.com','lala','lololo');

// $result2 = $objet->update(3, 'lala', '1234', 'naomi@gmail.com', 'lala', 'lololo');
// var_dump($result2);
// // $objet->delete()


?>

