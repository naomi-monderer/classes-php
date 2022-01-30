<?php
// session_start();
class Userpdo
{
    private $id;
    public $login;
    public $password;
    public $email;
    public $firstname;
    public $lastname;
    private $bdd;

    public function __construct()
    {
    //connexion à la BDD
        $serveur = "localhost";
        $dbname = "classes";
        $username = "root";
        $password = "root";
   
        
        try { $bdd = new PDO ("mysql:host=$serveur;dbname=$dbname", $username, $password);
                $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // echo "connected  successfully";
            $this->bdd = $bdd;

        }catch(PDOException $e){

            echo "connection failed" . $e->getMessage(); 
        } 
    }

    public function register($login,$password,$email,$firstname,$lastname)
    // insère les données utilisateurs dans la bdd.
    { 

        $this->login = $login;
        $this->password = $password;
        $this->email = $email;
        $this->firstname = $firstname;
        $this->lastname = $lastname;

        $login =  trim($login);
        $password = trim($password);
        $email = trim($email);
        $firstname = trim($firstname);
        $lastname =  trim($lastname);
    
        
        $query = "INSERT INTO utilisateurs(login, password, email, firstname, lastname)
        VALUES(:login, :password, :email, :firstname, :lastname)";
        $result=$this->bdd->prepare($query);
        $result->bindvalue(':login', $login);
        $result->bindvalue(':password', $password);
        $result->bindvalue(':email', $email);
        $result->bindvalue(':firstname', $firstname);
        $result->bindvalue(':lastname', $lastname);
        $result->execute(array(
            ":firstname" => $firstname,
            ":lastname" => $lastname,
            ":login" => $login,
            ":password" => $password,
            ":email" => $email
        ));

        // pour afficher les infos du user dans un tableau 
        $infos = "SELECT * FROM utilisateurs WHERE login = :login";
        $tab = $this->bdd->prepare($infos);
        $tab->bindvalue(':login',$login);
        $tab->setFetchMode(PDO:: FETCH_ASSOC);// j'utilise fetch_assoc pour récuperer les key d'un tableau associatif 
        $tab->execute();
        $users = $tab->fetchAll();
        // echo '<pre>';
        // var_dump($users);
        // echo '</pre>';
        foreach ($users as $user){
            echo '<pre>';
            echo $user['id'] . " " . $user['login'] . " " . $user['password']." " . $user['email']." ". $user['firstname']." " . $user['lastname'];
            echo '</pre>';
        }
        return $user;
    }

    public function connect($login,$password) 
    //connecte l’utilisateur = ok 
    // et donne aux attributs de la classe les valeurs correspondantes à celles de l’utilisateur connecté? 
    {
        
        $this->login = $login;
        $this->password = $password;
        
        $query = "SELECT * FROM utilisateurs WHERE  login = :login && password = :password";
        $result = $this->bdd->prepare($query);
        $result->setFetchMode(PDO:: FETCH_ASSOC);
        $result ->execute(array(
            ":login" => $login,
            ":password" => $password));
        $connectUsers = $result->fetchAll();
        $_SESSION['Users'] = $connectUsers;
      
        echo '<pre>';
        var_dump($connectUsers);
        echo '</pre>';
      
  
        foreach($connectUsers as $connectUser)
            {
                echo $connectUser['login'] . " " . $connectUser['password'] . "</br>";
                // comme je ne passe pas tous les attributs en param, je dois quand même remplir ma session 
                // avec les informations de l'utilisateur pour qu'elles existent. 
                $this->email = $connectUser['email'];
                $this->lastname=$connectUser['lastname'];
                $this->firstname= $connectUser['firstname'];
                $this->id= $connectUser['id'];
            }
        

    }
  

    public function disconnect()
    //deconnecte l'utilisateur
    { 
        if(isset($_SESSION['Users']))
        {session_destroy();}
    }
    
    public function delete($id)
    //efface l'utilisateur connecté
    {   

        $query2 = "DELETE FROM utilisateurs WHERE id =$id";
        $result2 = $this->bdd->prepare($query2);
        $result2->execute();
        echo "cet utilisateur a été supprimé";
        if(isset($_SESSION['Users']))
        {session_destroy();}
        
    }

    public function update($login,$password,$email,$firstname,$lastname)
    // met à jour les modifications des données.
    {
        $previouslogin=$this->login;
        $this->password = $password;
        $this->email = $email;
        $this->firstname = $firstname;
        $this->lastname = $lastname;

        $query = "UPDATE utilisateurs SET login = :login, password = :password, email= :email, firstname = :firstname, lastname = :lastname WHERE login = :previouslogin ";
        $result = $this->bdd->prepare($query);
        
        $user = $result->execute(array(
                            ":login" => $login,
                            ":password" => $password,
                            ":email" => $email,
                            ":firstname" =>$firstname,
                            ":lastname" =>$lastname,
                            ":previouslogin"=>$previouslogin
                        ));
        $query2 = "SELECT * FROM utilisateurs WHERE email = :email";
        $result2 =$this->bdd->prepare($query2);
        $result2->setFetchMode(PDO:: FETCH_ASSOC);
        $result2->execute(array(":email" =>$email));
        $user = $result2->fetchAll();
        echo "les informations de l'utilisateurs ont bien été modifiées";
    }

    public function isConnected()
    // verifie si l'utilisateur est connecté
    {   

        if(isset($this->login))
        {   
            echo "l'utilisateur est connecté";
        }
        return true;
    }

    public function getAllInfos()
    // recupère les données utilisateurs de la session 
    {
        $query = "SELECT * FROM utilisateurs WHERE email= :email";
        $result =$this->bdd->prepare($query);
        $result->setFetchMode(PDO:: FETCH_ASSOC);
        $result->execute(array(":email" => $this->email));
        $datas = $result->fetchAll();

        // foreach($datas as $data)
        // {  
        //     echo  $data['id'] . "  " . $data['login'] . "  " . $data['email'] . "  " .$data['firstname']. "  " .$data['lastname'];
        // }

      
        $login = $this->login;
        $password = $this->password;
        $email = $this->email;
        $firstname = $this->firstname;
        $lastname = $this->lastname;

        return[$login,$password,$email,$firstname,$lastname];
    } 

    // public function recup_id($id)
    // {   $id =$this->id;
    //     $query = "SELECT id FROM utilisateurs";
    //     $result = $this->bdd->prepare($query);
    //     $result->setFetchMode(PDO:: FETCH_ASSOC);
    //     $result->execute();
    //     $dataId = $result->fetchAll();
    //     $id = $dataId[0]['id'];
    //     echo "<pre>";
    //     var_dump($id);
    //     echo "</pre>";
        
    //    return $dataId;
    // }

    public function getLogin()
    {
        $login = $this->login;
      
        return $login;
    }
    public function getEmail()
    {
        $email = $this->email;
        return $email;
    }
    public function getFirstname()
    {
        $firstname = $this->firstname;
        return $firstname;
    }
    public function getLastname()
    {
        $lastname = $this->lastname;  
        return $lastname; 
    }
}


$userpdo = new Userpdo();
//$userpdo->register('jojo','nono','joris@gmail.com','joris','v');
$userpdo->connect('jojo','nono');

// $userpdo->disconnect();
//  $userpdo->delete(65);
// $userpdo->update('jojo','nono','joris@gmail.com','joris','v');
// $userpdo->isConnected();
// $userpdo-> getAllInfos();
// $userpdo-> getLogin();
// $userpdo-> getFirstname();
// $userpdo-> getLastname();
// $userpdo-> getEmail();
// $userpdo->recup_id($id);


?> 