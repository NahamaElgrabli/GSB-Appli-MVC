<?php
/**
 * Suivi des frais
 *
* PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Nahama Elgrabli
 * @author    Beth Sefer
 */
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
$mois = getMois(date('d/m/Y'));
$id = $_SESSION['id']; 
switch ($action) {
    case 'choixFiche':
        $listeVisiteur= $pdo->getLesVisiteursDontFicheVA();
        $clesvisiteur = array_keys($listeVisiteur);//
        //var_dump($clesvisiteur);
        $visiteurAselectioner = $clesvisiteur[0]; 
       // var_dump($visiteurAselectioner);
      
    $lesMois = $pdo->getLesMoisDontFicheVA();
    $lesCles2 = array_keys($lesMois);
    $moisASelectionner = $lesCles2[0];
    include 'vues/v_suiviFrais_comptable.php';
        break;
    
    case 'afficheFrais':
       $id = filter_input(INPUT_POST,'lstVisiteur', FILTER_SANITIZE_STRING);
      // var_dump($id);
       $lesVisiteurs=$pdo->getLesVisiteursDontFicheVA();
      // var_dump($lesVisiteurs);
       $visiteurASelectionner=$id; 
      // var_dump($visiteurASelectionner);
       $leMois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);//on recupere ce qui a ete selectionné ds la liste deroulante de nummois(qui se trouve dans v_listemois).
       //var_dump($leMois);
       $lesMois = $pdo->getLesMoisDisponibles($id);
      // var_dump($leMois);
       $moisASelectionner = $leMois;
       //var_dump($leMois);
       
    $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($id, $leMois);
   // var_dump($lesFraisHorsForfait);
    $lesFraisForfait = $pdo->getLesFraisForfait($id, $leMois);
   // var_dump($lesFraisForfait);
    $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($id, $leMois);
   // var_dump($lesInfosFicheFrais);
    $numAnnee = substr($leMois, 0, 4);
   // var_dump($numAnnee);
    $numMois = substr($leMois, 4, 2);
   // var_dump($numMois);
    $libEtat = $lesInfosFicheFrais['libEtat'];
   // var_dump($libEtat);
    $montantValide = $lesInfosFicheFrais['montantValide'];
    //var_dump($montantValide);
    $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
   // var_dump($nbJustificatifs);
    $dateModif = dateAnglaisVersFrancais($lesInfosFicheFrais['dateModif']);
   // var_dump($dateModif);
    
       
        if(!is_array($lesInfosFicheFrais)){
            ajouterErreur('Pas de fiche de frais validée pour ce visiteur ce mois');
            include 'vues/v_erreurs.php';
           include 'vues/v_suiviFrais_comptable.php';
        }
        else{
           include 'vues/v_etatFrais.php';
          include 'vues/v_suiviFrais_comptable_paiement.php';
        }
        break;
     case 'paiement':
        $id = filter_input(INPUT_POST, 'lstVisiteurs', FILTER_SANITIZE_STRING);
      //  $lesVisiteurs=$pdo->getLesVisiteurs($id);
        $visiteurASelectionner=$id;  
        
        $leMois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);//on recupere ce qui a ete selectionné ds la liste deroulante de nummois(qui se trouve dans v_listemois).
        $lesMois = $pdo->getLesMoisDisponibles($id);
        $moisASelectionner = $leMois;
        
        $etat='RB';
        $pdo->majEtatFicheFrais($id, $leMois, $etat);
        echo "La fiche a bien été remboursée.";
        break;
}
