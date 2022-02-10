    <?php
    require_once('../inc/init.inc.php');

    // echo '<pre style="margin-left: 250px">';
    // print_r($_POST);
    // echo'</pre>';

    // echo '<pre style="margin-left: 250px">';
    // print_r($_FILES);
    // echo'</pre>';
    // Si l'internaute son statut n'est pas 'admin' dans la session donc dans la BDD il n'a rien à faire sur cette page, on le redirige vers la page connexion
    if(!adminConnect())
    {
        //    // ex: http://localhost/PHP/09-ecommerce/connexion.php

        header('location:' . URL .  'connexion.php');
    }
    // SUPPRESSION PRODUIT

    if(isset($_GET['action']) && $_GET['action']=='suppression')
    {

        // echo "<p style='margin-left:250px;'>Je veux supprimer ce produit</p>";
    // Exo: realiser le traitement PHP+SQL permettant de supprimer le produit dans la BDD en fonction de l'id_produit transmit dans l'url (prepare+bindValue+execute)
    $delete=$bdd->prepare("DELETE FROM produit WHERE id_produit=:id_produit");
    $delete->bindValue( ':id_produit', $_GET['id_produit'],PDO::PARAM_INT);
    $delete->execute();
// on redéfinit la valeur de l'indice 'action' dans l'URL' afin d'enreer dans la condition IF permettant d'afficher des articles ($_GET['action']=='affichage')
    $_GET['action']='affichage';
$msg= "<p class='col-7 bg-success text-white text-center mx-auto p-3 my-3' > L'article n° <strong>$_GET[id_produit]</strong> a été supprimer avec succés</p>";
    }




    if(isset($_POST['reference'],$_POST['categorie'],$_POST['titre'],$_POST['description'],$_POST['couleur'],$_POST['taille'],$_POST['public'],$_POST['prix'],$_POST['stock']))
    {
        $photoBdd='';
        if(isset($_GET['action']) && $_GET['action']== 'modification')
        {
            $photoBdd=$_POST['photo_actuelle'];
        }
    // TRAITEMENT/ ENREGISTREMENT DE LA PHOTO
    if(!empty($_FILES['photo']['name']))
    {
        // On renomme l'image de l'enregistrement on cocatene la reference saisi dans la formulaire avec le nom de l'image recupere dans $_FILES 
        $nomPhoto=$_POST['reference']. '-' . $_FILES['photo']['name'];

        echo "<p style='margin-left: 250px'>$nomPhoto</p><hr>";

        // URL DE L'IMAGE
        // ex: http://localhost/PHP/09-ecommerce/asset/uploads/26B01-tee-shirt4.jpg


        $photoBdd=URL . "assets/uploads/$nomPhoto";
        // echo "<p style='margin-left: 250px'>$photoBdd</p><hr>";
    // CHEMIN PHYSIQUE DE L'IMAGE SUR LE SERVEUR
    //EX: C:/wamp64/www/PHP/09-ecommerce/asset/uploads/26B01-tee-shirt5.png

        $photoDossier= RACINE_SITE . "assets/uploads/$nomPhoto";
        // echo "<p style='margin-left: 250px'>$photoDossier</p><hr>";

        // COPIE DE L'IMAGE DANS LE DOSSIER UPLOADS
        // copy(): fonction prédéfinie permettant de copier un fichier uplodé dans un dossier sur le serveur
        // argumets:
        // 1. Le fichier temporaire de l'image disponible dans $_FILES
        // 2. Le chemin physique de l'image ou elle doit etre enregistrée sur le serveur
        copy($_FILES['photo']['tmp_name'], $photoDossier);

        // Exo Réaliser le traitement PHP + SQL permetant d'inserer un produit à la valiadation du formulaire( prepare + binDvalue + execute
    }
    if(isset($_GET['action']) && $_GET['action']=='ajout')
    {// ENREGISTREMENT PRODUIT
        $data=$bdd->prepare("INSERT INTO produit(reference,categorie,titre,description,couleur,taille,photo,public,prix,stock)VALUES (:reference,:categorie,:titre,:description,:couleur,:taille,:photo,:public,:prix,:stock)");

        $_GET['action']= 'affichage';

        $validInsert= "<p class='col-7 bg-success text-white text-center mx-auto p-3 my-3' > L'article référence<strong>$_POST[reference]</strong> a été enregistrer avec succés</p>";
    }
    else{// MODIFICATION PRODUIT
        $data=$bdd->prepare("UPDATE produit SET reference= :reference, categorie= :categorie, titre= :titre, description= :description, couleur=:couleur, taille= :taille, photo= :photo, public= :public,  prix= :prix,stock= :stock WHERE id_produit= :id_produit");
        $data->bindValue(':id_produit', $_GET['id_produit'], PDO::PARAM_STR);
        $_GET['action']= 'affichage';

        $validInsert= "<p class='col-7 bg-success text-white text-center mx-auto p-3 my-3' > L'article référence<strong>$_POST[reference]</strong> a été modifié</p>";

    }



    
        $data->bindValue(':reference', $_POST['reference'], PDO::PARAM_STR);
        $data->bindValue(':categorie', $_POST['categorie'], PDO::PARAM_STR);
        $data->bindValue(':titre', $_POST['titre'], PDO::PARAM_STR).
        $data->bindValue(':description', $_POST['description'], PDO::PARAM_STR);
        $data->bindValue(':couleur', $_POST['couleur'], PDO::PARAM_STR);
        $data->bindValue(':taille', $_POST['taille'], PDO::PARAM_STR);
        $data->bindValue(':photo', $photoBdd, PDO::PARAM_STR);
        $data->bindValue(':public', $_POST['public'], PDO::PARAM_STR);
        $data->bindValue(':prix', $_POST['prix'], PDO::PARAM_INT);
        $data->bindValue(':stock', $_POST['stock'], PDO::PARAM_INT);


        $data->execute();

    


    }
// MODIFICATION ARTICLE

if(isset($_GET['action']) && $_GET['action']=='modification')
{
// echo "<p style='margin-left: 200px;'>Je veux modifier ce produit</p>";

$update=$bdd->prepare("SELECT * FROM produit WHERE id_produit= :id_produit" );
$update->bindValue(':id_produit', $_GET['id_produit'], PDO::PARAM_INT);
$update->execute();

$produitActuel=$update->fetch(PDO::FETCH_ASSOC);
$reference=(isset($produitActuel['reference'])) ? $produitActuel['reference'] : '';
$categorie=(isset($produitActuel['categorie'])) ? $produitActuel['categorie'] : '';
$description=(isset($produitActuel['description'])) ? $produitActuel['description'] : '';
$titre=(isset($produitActuel['titre'])) ? $produitActuel['titre'] : '';
$couleur=(isset($produitActuel['couleur'])) ? $produitActuel['couleur'] : '';
$prix=(isset($produitActuel['prix'])) ? $produitActuel['prix'] : '';
$taille=(isset($produitActuel['taille'])) ? $produitActuel['taille'] : '';
$stock=(isset($produitActuel['stock'])) ? $produitActuel['stock'] : '';
$photo=(isset($produitActuel['photo'])) ? $produitActuel['photo'] : '';
$public=(isset($produitActuel['public'])) ? $produitActuel['public'] : '';


}





    require_once('../inc/inc_back/header.inc.php');
    require_once('../inc/inc_back/nav.inc.php');

    ?>

    <!-- 
    Exo : afficher sous forme de tableau de HTML l'ensemble des produits stockés en BDD
    1. requete de selection (query)
    2. Afficher le nombre de produit selectionnés en BDD (rowCount())
    3. Récupérer les informations sous forme de tableau (fetchAll)
    4. Déclarer le tableau HTML (<table>)
    5. Afficher les entêtes du tableau (<th>) en passant par le résultat du fetchAll()
    6. Afficher tout les produits de la BDD à l'aide de boucle (foreach) dans des lignes (<tr>) et cellules (<td>) du tableau 
    7. Prévoir un lien de modification / suppression pour chaque produit dans le tableau HTML
    -->

    <!-- LIENS PRODUITS -->


    <div class="mt-3 text-center">
        <a href="?action=ajout" class="btn btn-secondary">Nouveau articles</a>
        <a href="?action=affichage" class=" btn btn-secondary">Affichage des articles</a>

    </div>

    <!-- Si l'indice 'action' est définit dans l'url et qu'il a pour valeur 'Affichage4, cela veut dire que l'internaute a cliqué sur le lien 'Affichage des articles ' consequent traitement dans l'URL 'action=affichage', alors on entre dans la condition IF et on execute le code d'afficahge des articles -->
    <?php if(isset($_GET['action']) && $_GET['action']=='affichage'): ?>

    <!-- Affichage des produits -->
    <h1 class="text-center my-5">Affichages de produits</h1> 

    <?php
    // Affichage message d'utilisateur
if(isset($msg)) echo $msg;
    $data=$bdd->query("SELECT * FROM produit");

    echo '<h5><span class="badge bg-success">' . $data->rowCount() . '</span> article(s) enregistrés</h5>';


    $products=$data->fetchall(PDO:: FETCH_ASSOC);
    // echo '<pre>';
    // print_r($products);
    // echo '</pre>';

    echo '<table class="table table-bordered"><tr>';

    foreach($products[0] as $key => $value)
    {
        echo "<th class='text-center'>" . ucfirst($key) . "</th>";
    
    }
        echo "<th class='text-center'>Action</th>";
    echo '<tr>';
    foreach($products as $key=>$tab)
    {
        echo '<tr>';
        foreach($tab as $key2 => $value)
        {
            if($key2=='photo')
            echo "<td><img src='$value' alt='$tab[titre]' class='img-products'></td>";
            elseif($key2=='couleur')
            echo "<td style='background-color:$value;' class='text-white'>$value</td>";
            elseif($key2== 'prix')
            echo "<td><strong>" . $value . "€</strong></td>";

            elseif($key2== 'description')
            echo "<td>$value</td>";
            else
            echo "<td>$value</td>";
        }

        echo '<td class=text-center>';
        echo "<a href='?action=modification&id_produit=$tab[id_produit]' class='btn btn-primary mb-3' ><i class='bi bi-pencil-square'></i></a>";
        echo "<a href='?action=suppression&id_produit=$tab[id_produit]' class='btn btn-danger mb-3' onclick='return(confirm(\"en etes-vous certain?\"));' ><i class='bi bi-trash'></i></a>";
        echo '</td>';

        echo '</tr>';
    }

    echo '</table>';

    endif;

    // Si l'indice 'action' est définit dans l'url et qu'il a pour valeur 'Affichage', cela veut dire que l'internaute a cliqué sur le lien 'ajout ' consequent traitement dans l'URL 'action=ajout', alors on entre dans la condition IF et on execute le code d'ajout des articles.

    if(isset($_GET['action']) && ($_GET['action']=='ajout'    || $_GET['action']=='modification')): 

    ?> 


    <h1 class="text-center my-5" ><?php echo  $_GET['action']=='ajout' ?  'Ajout produit': 'modification produit'?></h1>

    <?php if(isset($validInsert)) echo $validInsert ?>

    <!-- enctype="multipart/form-data" permet de recuperer les données d'un fichier uploadé(nom,extension,taille, etc) accessible en php via la superglobale $_FILES -->

    <form method="post" enctype="multipart/form-data" class="row g-3">
        <div class="col-md-6">
            <label for="reference" class="form-label">Référence</label>
            <input type="text" class="form-control" id="reference" name="reference" value="<?php if(isset($reference)) echo $reference; ?>">
        </div>
        <div class="col-md-6">
            <label for="categorie" class="form-label">Catégorie</label>
            <input type="text" class="form-control" id="categorie" name="categorie" value="<?php if(isset($categorie)) echo $categorie; ?>">
        </div>
        <div class="col-12">
            <label for="titre" class="form-label">Titre</label>
            <input type="text" class="form-control" id="titre" name="titre" value="<?php if(isset($titre)) echo $titre; ?>">
        </div>
        <div class="col-12">
            <label for="description" class="form-label">Description</label>
            <textarea type="text" class="form-control" id="description" name="description" rows="10" ><?php if(isset($description)) echo $description; ?></textarea>
        </div>
        <div class="col-4">
            <label for="couleur" class="form-label">Couleur</label>
            <input type="color" class="form-control input-couleur" id="couleur" name="couleur"  value="<?php if(isset($couleur)) echo $couleur; ?>">
        </div>
        <div class="col-4">
            <label for="taille" class="form-label">Taille</label>
            <select id="taille" name="taille" class="form-select"  value="<?php if(isset($taille)) echo $taille; ?>">
                <option value="s" <?php if(isset($taille) && $taille=='s') echo 'selected'; ?>>S</option>

                <option value="m" <?php if(isset($taille) && $taille=='m') echo 'selected'; ?>>M</option>

                <option value="l" <?php if(isset($taille) && $taille=='l') echo 'selected'; ?>>L</option>

                <option value="xl" <?php if(isset($taille) && $taille=='xl') echo 'selected'; ?>>XL</option>
            </select>
        </div>
        <div class="col-4">
            <label for="public" class="form-label">Public</label>
            <select id="public" name="public" class="form-select">
                <option value="homme"<?php if(isset($public) && $public=='homme') echo 'selected'; ?>>homme</option>
                <option value="femme"> <?php if(isset($public) && $public=='femme') echo 'selected'; ?>Femme</option>
                <option value="mixte" <?php if(isset($public) && $public=='mixte') echo 'selected'; ?>>Mixte</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="photo" class="form-label">Photo</label>
            <input type="file" class="form-control" id="photo" name="photo" >
            <input type="text" name="hiden" id="input" value="<?php if(isset($photo)) echo $photo; ?>">
        </div>
        <div class="col-4">
            <label for="prix" class="form-label">Prix</label>
            <input type="text" class="form-control" id="prix" name="prix"  value="<?php if(isset($prix)) echo $prix; ?>">
        </div>
        <div class="col-4">
            <label for="stock" class="form-label">Stock</label>
            <input type="text" class="form-control" id="stock" name="stock"  value="<?php if(isset($stock)) echo $stock; ?>">
        </div>

        <?php if(isset($photo) && !empty($photo)): ?>

            <div class="col-7 mx-auto d-flex flex-column align-items-center rounded shadow-sm border">
                <small class="fst-italic mt-3">Photo actuelle de l'article. Vous pouvez uploader une nouvelle photo si vous souhaiter la modifier</small>

                <img src="<?= $photo ?>" alt="" class="img-product-update">
            </div>

<?php endif; ?>

        <div class="col-12 text-center">
            <button type="submit" class="btn btn-info mb-5"> <?php echo  $_GET['action']=='ajout' ?  'Ajout produit': 'modification produit'?></button>
        </div>
    </form>
    <?php

    endif;
    require_once('../inc/inc_back/footer.inc.php');

