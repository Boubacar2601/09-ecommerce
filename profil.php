<?php
require_once('inc/init.inc.php');

// echo '<pre>';
// print_r($_SESSION);
// echo '</pre>';

// si l'indice user n'est pas definit dans la session (connect()), cela veut dire que l'internaute n'est pas identifié sur le site il n'a rien rien à faire sur la page profil on le redirige (header()) vers la page connexion.php
if(!connect())
{
    header('location: connexion.php');
}





require_once('inc/inc_front/header.inc.php');
require_once('inc/inc_front/nav.inc.php')





?>
<h1 class="text-center my-5">Vos informations personneles</h1>

<!-- Exo Afficher l'ensemble des données de l'utilisateur sur la page web en passant par le fichier session 
de l'utilisateur ($_SESSION). Ne pas afficher l'id_membre sur la page web -->
<div class="col-5 mx-auto card mb-5 shadow-sm">
    <div class="card_body">
        <?php

     foreach($_SESSION['user'] as $key=>$value):
        if($key != 'id_membre' && $key != 'statut'):
        ?>

            <p class="d-flex justify-content-between ">
            <strong><?php echo ucfirst($key); ?></strong>
            <strong><?=$value; ?></strong>


            </p>
<?php
    endif;

    endforeach;
?>



</div>
</div>
<?php

require_once('inc/inc_front/footer.inc.php');
