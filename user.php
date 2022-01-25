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
    {   
        $bdd = mysqli_connect("localhost", "root", "root", "classes");
        $this->bdd = $bdd;
      
  
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
            if (mysqli_num_rows($req) == 0)
             {

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

        // foreach($req as $user)
        // {
        //     echo "<pre>";
        //     echo $req['login'] . " ". $req['password'];
        //     echo "</pre>";

        // }
    }

    public function connect($login, $password)
    {
        $bdd = $this->bdd;
        $error="";
        
        $_login = htmlspecialchars($login);
        $_password = htmlspecialchars($password);
    
        $login = trim($_login);
        $password = trim($_password);
        

        if (!empty($login) && !empty($password)) {
           
            $req= "SELECT * FROM utilisateurs WHERE login = '$login'";
            $userExist =mysqli_query($bdd,$req);
            $dataUser=mysqli_fetch_assoc($userExist); // mysqli_fetch_assoc(mysqli_result($result));?
            // var_dump($dataUser);

                if(isset($dataUser['login']))// je pense que les infos de l'utilisateurs sont contenues dans le resultat de ma requête  
                {
               
                    $passwordhash = $dataUser['password'];
                    //(ou bien $passworhash = $dataUser['password'];) ?

                    if(password_verify($password,$passwordhash))
                    {
                        $_SESSION['dataUser'] = $dataUser;
                        $this->id = $dataUser['id'];
                        
                        
                        $this->login = $login;
                        $this->password = $password;
                        $this->email = $dataUser['email'];
                        $this->firstname = $dataUser['firstname'];
                        $this->lastname = $dataUser['lastname'];
                        return "vous êtes connecté";
                       
                    }
                    else
                    {
                        return "le mot de passe est incorrect.";
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

        if (isset($_SESSION['dataUser']) && isset($_POST['deconnexion'])) 
        {
            session_destroy();
        }
        // Où est ce que je créais un boutton deconnexion? pour faire le test
        //comment j'initialise une session

    }

    public function delete()
    {
        if(isset($_SESSION['dataUser']))
        {
        $this->id = $_SESSION['dataUser']['id'];
        $query = mysqli_query($this->bdd, "DELETE FROM utilisateurs WHERE id='$this->id'");
        echo "OK, l'utilisateur de la sesssion en cours a bien été effacé de la bdd.";
        session_destroy();
        }
    }

    public function update($login, $email, $firstname, $lastname)
    {
        if(isset($_SESSION['dataUser']))
        {

            // $query = mysqli_query($bdd,"SELECT * FROM utilisateurs");
            // $result = mysqli_fetch_all($query);
            $this->id = $_SESSION['dataUser']['id'];
            $this->login = $login;
            $this->email = $email;
            $this->firstname = $firstname;
            $this->lastname = $lastname;

            $req = "UPDATE utilisateurs SET login = '$this->login',email='$this->email',firstname = '$this->firstname',lastname =' $this->lastname' WHERE id = '$this->id'";
            $query2 = mysqli_query($this->bdd, $req);
            $req2 = "SELECT * FROM utilisateurs where id = $this->id";
            $query = mysqli_query($this->bdd, $req2);
            $result2 = mysqli_fetch_all($query);
        }

        
        
//actualiser la session
        return $result2;
    }

    public function isConnected() // retourne un booleen permettant de savoir si un utilisateur est connecté ou non
    {
       
            $userExist = mysqli_query($this->bdd, "SELECT * FROM utilisateurs WHERE login='$this->login'");
            $resultUser = mysqli_fetch_all($userExist);
            // comment transmettre à ma requete que je veux l'argument $login?
            if (isset($_POST['login']))
            {
                $_SESSION['dataUser'] = $resultUser;

            }
            return true;
        
    }

    public function getAllInfos()
    {
        $req = mysqli_query ($this->bdd,"SELECT * FROM utilisateurs WHERE id = '$this->id'");
        $resultUser = mysqli_fetch_assoc($req);
       
      
            foreach($resultUser as $user)
            {
                echo '<pre>';
                echo $user;
                echo '</pre>';

            }
            //return ?
        
        
    }

    public function getLogin() //retourne le login de l’utilisateur
    {
        $login = $this->login;
        echo $login;
        
        return $login;
    }

    public function getFirstname() //retourne Firstname de l’utilisateur
    {
        $firstname = $this->firstname;
        echo $firstname;
        
        return $firstname;
    }

    public function getEmail() //retourne le l'email de l'utilisateur
    {
       $email = $this->email;
       echo $email;
       return $email;
    }

    public function getLastname() //retourne le Lastname de l’utilisateur
    {
       $lastname = $this->lastname;
       echo $lastname;
       return $lastname; 
    }


}





$user = new User();
$user->register('nao','naomiette','naomiette','naomi@gmail.com','naomi','monderer');
$user->connect('nao','naomiette');

// $user->disconnect();
// $user->update('naomiette','naomi@ttt.com','lola','monderer');
// $user->delete();
//$user->isConnected();
// $user->getAllInfos();
$user->getLogin();
$user->getEmail();
$user->getFirstname();
$user->getLastname();

?>

