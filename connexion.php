<?php
require_once('inc/init.inc.php');

// echo '<pre>';
// print_r($_GET);
// echo '</pre>';


// si l'indice udser estdefinit dans la session (connect()), cela veut dire que l'internaute est identifié sur le site il n'a rien rien à faire sur la page de connexion on le redirige (header()) vers la page profil.php
if(connect())
{
    header('location: profil.php');
}




// si l'indice 'action' est definit dans l'url et qu'il a pour valeur 'deconnexion' cela veut dire que l'internaute a cliqué sur le lien 'deconnexion' et donc transmit dans l'url les parametres 'action=deconnexion' alors on entre dans la condition IF et on supprime l'indice 'user' dans la session afin qu'il en soit plus authentifié sur le site


if(isset($_GET['action']) && $_GET['action']=='deconnexion')

{
// echo "je veux me deconnecter<hr>";
unset($_SESSION['user']);

}

if(isset($_POST['pseudo_email'], $_POST['password'], $_POST['submit']))
{

$verifUser=$bdd->prepare("SELECT * FROM membre WHERE pseudo= :pseudo OR email= :email");
$verifUser->bindValue(':pseudo', $_POST['pseudo_email'], PDO::PARAM_STR).
$verifUser->bindValue(':email', $_POST['pseudo_email'], PDO::PARAM_STR).
$verifUser->execute();

echo "nb resultat :" . $verifUser->rowCount(). '<hr>';


if($verifUser->rowCount() >0 )
{
    // echo "pseudo ou email ok ! <hr>";

$user=$verifUser->fetch(PDO:: FETCH_ASSOC);
echo '<pre>';
print_r($user);
echo '</pre>';
//Controler le mot de pass
// password_verify() : fonction prédéfinie permettant de comparer un clé de hachage (le mot de passe crypté en BDD)
// à une chiane de caractére (le mot de passe saisi dans le formulaire)
//1: le mot de passe en clair non haché non crypté
//1: la clé hachage le mot de passe crypté dans la BDD
if(password_verify($_POST['password'], $user['password']))
{

// echo "mot de passe OK !";
foreach($user as $key=>$value)
{
    if($key != 'password')
    {
    $_SESSION['user'][$key]=$value;
    }
}
// $_SESSION ['user']['nom]=........

header('location:profil.php');

}
else 
{
    $error= "<p class='col-7 bg-danger text-white text-center mx-auto p-3'>Identifiant invalide </p>";
}
}

else
{
    // echo " erreur pseudo ou email <hr>";

    $error= "<p class='col-7 bg-danger text-white text-center mx-auto p-3'>Identifiant invalide </p>";


}


}

require_once('inc/inc_front/header.inc.php');
require_once('inc/inc_front/nav.inc.php');
?>

<?php
 if(isset($_SESSION['valid_inscription'])) echo $_SESSION['valid_inscription']; 
 if(isset($error)) echo $error; 
?>

            <h1 class="text-center my-5">Identifiez-vous</h1>

            <form action="" method="post" class="col-12 col-sm-10 col-md-7 col-lg-5 col-xl-4 mx-auto">
                <div class="mb-3">
                    <label for="pseudo_email" class="form-label">Nom d'utilisateur / Email</label>
                    <input type="text" class="form-control" id="pseudo_email" name="pseudo_email" placeholder="Saisir votre Email ou votre nom d'utilisateur">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Saisir votre mot de passe">
                </div>
                <div>
                    <p class="text-end mb-0"><a href="" class="alert-link text-dark">Pas encore de compte ? Cliquez ici</a></p>
                    <p class="text-end m-0 p-0"><a href="" class="alert-link text-dark">Mot de passe oublié ?</a></p>
                </div>
                <input type="submit" name="submit" value="Continuer" class="btn btn-dark">
            </form>

            <?php
 // on supprime dans la session l'indice 'valid_inscription' afin d'eviter que le message ne s'affiche tout lme temps
 // sur la page connexion         
unset($_SESSION['valid_inscription']);           
require_once('inc/inc_front/footer.inc.php');

      