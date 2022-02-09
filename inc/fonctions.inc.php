<?php


// FONCTION INTERNAUTE AUTHENTIFIE
function connect()
{
// Si l'indice 'user' n'est pas defini dans la session, cela veut dire que l'internaute 
// n'est pas passé par la page connexion, donc n'est pas authentifié sur la liste

   if(!isset($_SESSION['user']))
   {

         return false;

   }
    else{ // sino l'indice 'user' est définit dans la session, l'internaute est passé par la page de connexion et est authentifié sur le site

        return true;
    }
}

// FONCTION INTERNAUTE AUTHENTIFIE ET ADMINISTRATEUR DU SITE
 function adminconnect()
 {
//     si l'indice user est definit dans la session (connect()) et si l'indice 'statut' dans la session,
//    donc la bdd a pour valeur 'admin'cela veut direv que linternaute est authentifoié et quil est administrateur du site

     if(connect() && $_SESSION['user']['statut']=='admin')

    {
    return true;

    }
    else {   
        // sino, l'indice 'user' n'est pas définit, donc l'utilisteur n'est pas authentifié ou alors son statut est 'user', donc l'utilisateur n'est pas administrateur

    return false;

    }

 }






