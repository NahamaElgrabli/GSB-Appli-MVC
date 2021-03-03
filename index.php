<?php
/**
 * Index du projet GSB
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Nahama Elgrabli
 * @author    Beth Sefer
 */

require_once 'includes/fct.inc.php'; //REQUIRE erreure fatale , arret    INCLUDE: message    INC:FICHIER A INTEGRER         FCT: FICHIER AVEC TS LES FONCTIONS QUE ON AURA BESOIN          
require_once 'includes/class.pdogsb.inc.php'; //class: pdogsb contiend une classe avec des fonctions ou on vas faire des requettes sql pour la base de donée. 
session_start(); // appelle la session et la fait demarer. la session c'est une variable globale qui contiend plein de variable: superglobale
$pdo = PdoGsb::getPdoGsb();
$estVisiteurConnecte = estVisiteurConnecte();
$estComptableConnecte= estComptableConnecte();

require 'vues/v_entete.php'; // le prgrm lance et si ca marche pas, le prgrm sarette car ya pas include
$uc = filter_input(INPUT_GET, 'uc', FILTER_SANITIZE_STRING); // facon de verifier le contenu qui est dans un element,  uc change tjrs de valeur.
if ($uc && !$estVisiteurConnecte && !$estComptableConnecte ) { // si il est pas connecté et uc est plein
    $uc = 'connexion';
} elseif (empty($uc)) { // si uc est vide ou !empty= si uc est plein
    $uc = 'accueil';
}
switch ($uc) { //swich sur une variable pour dissocier des cas
case 'connexion': // differents cas possibles
    include 'controleurs/c_connexion.php';
    break;// passage au cas suivant
case 'accueil':
    include 'controleurs/c_accueil.php';
    break;
case 'gererFrais':
    include 'controleurs/c_gererFrais.php';
    break;
case 'etatFrais':
    include 'controleurs/c_etatFrais.php';
    break;
case 'validerFrais':
    include 'controleurs/c_validerFrais.php';
    break;
case 'suiviFrais':
    include 'controleurs/c_suiviFrais.php';
    break;
case 'deconnexion':
    include 'controleurs/c_deconnexion.php';
    break;
}
require 'vues/v_pied.php';
