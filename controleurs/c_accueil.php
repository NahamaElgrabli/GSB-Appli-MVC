<?php
/**
 * Gestion de l'accueil
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Nahama Elgrabli
 * @author    Beth Sefer
 */
$estVisiteurConnecte =estVisiteurConnecte();
$estComptableConnecte =estComptableConnecte();

if ($estVisiteurConnecte) {
    include 'vues/v_accueil.php';
} elseif ($estComptableConnecte) {
     include 'vues/v_accueil_Comptable.php';
} else {
    include 'vues/v_connexion.php';
}
