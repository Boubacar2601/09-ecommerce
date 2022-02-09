
<?php
require_once('inc/init.inc.php');

/*
    Exercice : 
    1. Contrôler en PHP que l'on receptionne bien toute les données saisie dans le formulaire (print_r)
    2. Faites en sorte d'informer l'internaute si le champ 'pseudo' est laissé vide 
    3. Faites en sorte d'informer l'internaute si le pseudo n'est pas disponible (SELECT + ROWCOUNT)
    4. Faites en sorte d'informer l'internaute si le champ 'email' est laissé vide 
    5. Faites en sorte d'informer l'internaute si le champ 'email' n'est pas du bon format (filter_var)
    6. Faites en sorte d'informer l'internaute si le email est déjà existant en BDD (SELECT + ROWCOUNT)
    7. Faites en sorte d'informer l'internaute si le champ 'password' ou 'confirm_password' sont laissé vide
    8. Faites en sorte d'informer l'internaute si les mot de passe ne correspondent pas 
    9. Si l'internaute a correctement remplit le formulaire, réaliser le traitement PHP + SQL  permettant d'insérer un nouvel utilisateur dans la BDD à la validation du formulaire (PREPARE + BINDVALUE + INSERT + EXECUTE)
*/

// 1. Contrôler en PHP que l'on receptionne bien toute les données saisie dans le formulaire (print_r)

// si l'indice udser estdefinit dans la session (connect()), cela veut dire que l'internaute est identifié sur le site il n'a rien rien à faire sur la page de connexion on le redirige (header()) vers la page profil.php
if(connect())
{
    header('location: profil.php');
}





echo '<pre>';
 print_r($_POST);
  echo '</pre>';


if (isset($_POST['civilite'], $_POST['pseudo'], $_POST['password'], $_POST['confirm_password'], $_POST['email'], 
$_POST['nom'], $_POST['prenom'], $_POST['adresse'], $_POST['ville'], $_POST['code_postal']))
{
    
    // On stock une classe CSS bootstrap dans une variable afin d'affecter une bordure rouge au champ input en cas d'erreur de saisie
    $border = 'border border-danger';



    //3. Faites en sorte d'informer l'internaute si le pseudo n'est pas disponible (SELECT + ROWCOUNT)

    $verifPseudo=$bdd->prepare("SELECT * FROM membre WHERE pseudo=:pseudo");
    $verifPseudo->bindValue(':pseudo',$_POST['pseudo'], PDO::PARAM_STR);
    $verifPseudo->execute();
 

    //  echo "Nbresultat". $verifPseudo->rowCount(). '<hr>';
    // On compte combien de résultats retourne la requete SELECT, si elle retourne 1 résultat, cela veut dire que le pseudo saisi dans le formulaire existe en BDD, si la requete SELECT ne retourne aucun résultat, cela veut dire pseudo n'existe pas en BDD


    if($_POST['password'] != $_POST['confirm_password'])
    {
        $errorPassword = "<small class='fst-italic text-danger'>Vérifier les mots de passe</small>";
        $error= true;
    }




    //  2. Faites en sorte d'informer l'internaute si le champ 'pseudo' est laissé vide
    if(empty($_POST['pseudo']))
    {
        $errorPseudo = "<small class='fst-italic text-danger'>Merci de saisir un nom d'utilisateur.</small>";
        $error= true;
    }
    elseif($verifPseudo->rowCount() > 0)
    {
        $errorPseudo = "<small class='fst-italic text-danger'>Nom d'utilisateur indispensable, merci d'en saisir un nom nouveau.</small>";

    }
   

  
    // 6. Faites en sorte d'informer l'internaute si le email est déjà existant en BDD (SELECT + ROWCOUNT)
    $verifEmail=$bdd->prepare("SELECT * FROM membre WHERE email=:email");
    $verifEmail->bindValue(':email',$_POST['email'], PDO::PARAM_STR);
    $verifEmail->execute();

    // 4. Faites en sorte d'informer l'internaute si le champ 'email' est laissé vide 
    if(empty($_POST['email']))
    {
        $errorEmail = "<small class='fst-italic text-danger'>Merci de saisir un Email.</small>";
        $error= true;

    }
    elseif(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
    {
        //5. Faites en sorte d'informer l'internaute si le champ 'email' n'est pas du bon format (filter_var)
        $errorEmail = "<small class='fst-italic text-danger'>Format Email invalide (ex:exemple@gmail.com).</small>";
        $error= true;

    }

  
    elseif($verifEmail->rowCount() > 0)
    {
        //5. Faites en sorte d'informer l'internaute si le champ 'email' n'est pas du bon format (filter_var)
        $errorEmail = "<small class='fst-italic text-danger'>Compte existant à cette adress Email.</small>";
        $error= true;

    }
if(!isset($_POST['pdc']))
{
    $errorPdc= "<p class='col-7 bg-danger text-white text-center mx-auto p-3'>Vous devez accepter les politiques de confidentialités </p>";
    $error=true;
}


      //7. Faites en sorte d'informer l'internaute si le champ 'password' ou 'confirm_password' sont laissé vide

      if(empty($_POST['password']) || empty($_POST['confirm_password']))
      {
          $errorPassword = "<small class='fst-italic text-danger'>Merci de saisir un mot de pass.</small>";
          $error= true;

      }
   
      elseif ($_POST['password'] !=$_POST['confirm_password'])
        {

        //   8. Faites en sorte d'informer l'internaute si les mot de passe ne correspondent pas 

        $errorPassword = "<small class='fst-italic text-danger'>verifier les mots de pass.</small>";
        $error= true;
  
    }

    // 9. Si l'internaute a correctement remplit le formulaire, réaliser le traitement PHP + SQL  permettant d'insérer un nouvel utilisateur dans la BDD à la validation du formulaire (PREPARE + BINDVALUE + INSERT + EXECUTE)

if(!isset($error))
{
    // On ne conserve jamais le mot de passe en 'clair' dans la BDD, pour cela nous devons créer une clé de hachage
        // password_hash() : focntion prédéfinie permettant de créer une clé de hachage du mot de passe en BDD
        // arguments : 
        // 1. Le mot de passe à haché
        // 2. Le type de cryptage 

$_POST['password']=password_hash($_POST['password'], PASSWORD_BCRYPT);



// Requete SQL d'insertion (PREPARE + BINDVALUE + EXECUTE)
$insertMembre=$bdd->prepare("INSERT INTO membre(pseudo,password,nom,prenom,email,civilite,ville,code_postal,adresse)
VALUES(:pseudo,:password,:nom,:prenom,:email,:civilite,:ville,:code_postal,:adresse)");

// echo '<pre>';
//  print_r($insertMembre);
//   echo '</pre>';
  // on execute autant de fois bindValue que nous avons de ma   rqueurs déclarés
  // on renseigne chaque valeur inserée dans chaque marqueur
$insertMembre->bindValue(':pseudo',$_POST['pseudo'], PDO::PARAM_STR);
$insertMembre->bindValue(':password',$_POST['password'], PDO::PARAM_STR);
$insertMembre->bindValue(':nom',$_POST['nom'], PDO::PARAM_STR);
$insertMembre->bindValue(':prenom',$_POST['prenom'], PDO::PARAM_STR);
$insertMembre->bindValue(':email',$_POST['email'], PDO::PARAM_STR);
$insertMembre->bindValue(':civilite',$_POST['civilite'], PDO::PARAM_STR);
$insertMembre->bindValue(':ville',$_POST['ville'], PDO::PARAM_STR);
$insertMembre->bindValue(':code_postal',$_POST['code_postal'], PDO::PARAM_STR);
$insertMembre->bindValue(':adresse',$_POST['adresse'], PDO::PARAM_STR);

$insertMembre->execute();


$_SESSION['valid_inscription']= "<p class='col-7 bg-success text-white text-center mx-auto p-3'>Félicitation ! Vous etes maintenant inscrit sur le site. Vous pouvez dés à présent vous connecter </p>";


// on redirige l'internaute vers la page de connexion.php aprés l'execution de la requete d'insertion, aprés l'inscription de l'internaute
header('location: connexion.php');

}
}


require_once('inc/inc_front/header.inc.php');
require_once('inc/inc_front/nav.inc.php');
?>

<h1 class="text-center my-5">Créer votre formulaire</h1>

<?php if(isset($errorPdc)) echo $errorPdc; ?>

<form method="post"  class="row g-3 mb-5">
        <div class="col-6">
            <label for="Civilité" class="form-label">Civilité</label>
            <select class="form-select" id="civilite" name="civilite">
                <option value="homme">Monsieur</option>
                <option value="femme">Madame</option>
            </select>
        </div>
        <div class="col-md-6">
            <label for="pseudo" class="form-label">Nom d'utilisateur</label>
            <input type="text" class="form-control <?php if(isset($errorPseudo)) echo $border; ?>" id="pseudo" name="pseudo" value="<?php if(isset($_POST['pseudo'])) echo $_POST['pseudo']; ?>">
        <?php if(isset($errorPseudo)) echo $errorPseudo; ?>
        </div>
        <div class="col-md-6">
            <label for="password" class="form-label">Mot de passe</label>
            <input type="password" class="form-control <?php if(isset($errorPassword)) echo $border; ?>" id="password" name="password">
        </div>
        <div class="col-md-6">
            <label for="confirm_password" class="form-label">Confirmer votre mot de passe</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password">
            <?php if(isset($errorPassword)) echo $errorPassword; ?>
        </div>
        <div class="col-12">
            <label for="email" class="form-label">Email</label>
            <input type="text" class="form-control <?php if(isset($errorEmail)) echo $border; ?>" id="email" name="email" placeholder="Saisir votre adresse email">
            <?php if(isset($errorEmail)) echo $errorEmail; ?>

        </div>
        <div class="col-6">
            <label for="prenom" class="form-label">Prénom</label>
            <input type="text" class="form-control" id="prenom" name="prenom" placeholder="Saisir votre prénom">
        </div>
        <div class="col-md-6">
            <label for="nom" class="form-label">Nom</label>
            <input type="text" class="form-control" id="nom" name="nom" placeholder="Saisir votre nom">
        </div>
        <div class="col-md-6">
            <label for="adresse" class="form-label">Adresse</label>
            <input type="text" class="form-control" id="adresse" name="adresse" placeholder="Saisir votre adresse">
        </div>
        <div class="col-md-4">
            <label for="ville" class="form-label">Ville</label>
            <input type="text" class="form-control" id="ville" name="ville" placeholder="Saisir votre ville">
        </div>
        <div class="col-md-2">
            <label for="code_postal" class="form-label">Code postal</label>
            <input type="text" class="form-control" id="code_postal" name="code_postal">
        </div>
        <div class="col-12">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="pdc" name="pdc" value="checked">
                <label class="form-check-label" for="pdc">
                Accepter les <a href="" class="alert-link text-dark">politiques de confidentialité</a>  
                </label>
            </div>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-dark">Continuer</button>
        </div>
    </form>

       
<?php
require_once('inc/inc_front/footer.inc.php');