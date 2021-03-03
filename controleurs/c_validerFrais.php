<?php
/**
 * Validation des frais
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Nahama Elgrabli
 * @author    Beth Sefer
 */

    
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
$id = $_SESSION['id']; 


     
switch ($action) {
    case'choixVisiteurEtMois':
      $listeVisiteur = $pdo->getListeVisiteur();//RESULTAT DE LA REQUETTE DS LA VARIABLE
      $clesvisiteur = array_keys($listeVisiteur);//
      $visiteurAselectioner = $clesvisiteur[0];   
           
    $leMois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);
    $mois = getMois(date('d/m/Y'));
    $lesMois = getles12derniersmois($mois);
    $lesCles = array_keys($lesMois);
    $moisASelectionner = $lesCles[0];
    include 'vues/v_validerFrais_comptable.php';
   
    break ;
    case 'afficheFrais':
       
        $listeVisiteur = $pdo->getListeVisiteur();
        $clesvisiteur = array_keys($listeVisiteur);
        $visiteurAselectioner = $clesvisiteur[0]; 
      
       $mois = getMois(date('d/m/Y'));
       $lesMois = getles12derniersmois($mois);
       $lesCles = array_keys($lesMois);
    // $moisASelectionner = $lesCles[0]; 
      
       $id = filter_input(INPUT_POST, 'lstVisiteurs', FILTER_SANITIZE_STRING);
       $leVisiteurASelectionner= $id;
       $leMois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);
       $moisASelectionner=$leMois;
       $lesFraisForfait = $pdo->getLesFraisForfait($id, $leMois);
       $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($id, $leMois);
       $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($id, $leMois);
       $nbJustificatifs= $pdo->getNbjustificatifs($id, $leMois);
        
         if(!is_array($lesInfosFicheFrais)){
           ajouterErreur('Pas de fiche de frais pour ce visiteur ce mois');
           include 'vues/v_erreurs.php';
           include 'vues/v_validerFrais_comptable.php';
       }
       else{
           include 'vues/v_afficheFrais_comptable.php';
       }
      
     break ;
     
    case 'corrigerFrais':
     
       $listeVisiteur = $pdo->getListeVisiteur();
       $clesvisiteur = array_keys($listeVisiteur);
       $visiteurAselectioner = $clesvisiteur[0]; 
       $mois = getMois(date('d/m/Y'));
       $lesMois = getles12derniersmois($mois);
     
      
     $lesCles = array_keys($lesMois);
        
     $id = filter_input(INPUT_POST, 'lstVisiteurs', FILTER_SANITIZE_STRING);
     $leVisiteurASelectionner= $id;   
     $leMois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);
     $moisASelectionner= $leMois;   
     $lesFrais = filter_input(INPUT_POST, 'lesFrais', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
   
     $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($id, $leMois);
     
      if (lesQteFraisValides( $lesFrais)) {
      $pdo->majFraisForfait($id, $leMois,  $lesFrais);
  } else {
      ajouterErreur('Les valeurs des frais doivent être numériques');
      include 'vues/v_erreurs.php';
  }

    //$pdo->modifierFraisHorsForfait($idFrais);
    $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($id, $leMois);
    $lesFraisForfait = $pdo->getLesFraisForfait($id, $leMois);
    
    $nbJustificatifs= $pdo->getNbjustificatifs($id, $leMois);
    
   include 'vues/v_afficheFrais_comptable.php';
   break ;
     
    case 'corrigerFraisHF':
       $listeVisiteur = $pdo->getListeVisiteur();
       $clesvisiteur = array_keys($listeVisiteur);
       $visiteurAselectioner = $clesvisiteur[0]; 
       $mois = getMois(date('d/m/Y'));
       $lesMois = getles12derniersmois($mois);
     
      
     $lesCles = array_keys($lesMois);
        
     $id = filter_input(INPUT_POST, 'lstVisiteurs', FILTER_SANITIZE_STRING);
     $leVisiteurASelectionner= $id;   
     $leMois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);
     $moisASelectionner= $leMois;   
     $lesFrais = filter_input(INPUT_POST, 'lesFrais', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
     $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($id, $leMois);
     
     $laDate = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING);
     $leMontant = filter_input(INPUT_POST, 'montant', FILTER_VALIDATE_FLOAT);
     $leLibelle = filter_input(INPUT_POST, 'libelle', FILTER_SANITIZE_STRING);
     $idFHF = filter_input(INPUT_POST, 'idFHF', FILTER_SANITIZE_NUMBER_INT);
    
  if (isset($_POST['Corriger'])){ 
     valideInfosFrais($laDate, $leLibelle, $leMontant);
     if (nbErreurs() != 0) {
       include 'vues/v_erreurs.php';
     } else {
       $pdo->MajFraisHorsForfait($id,$leMois,$leLibelle,$laDate,$leMontant,$idFHF);
     }
 }
 if (isset($_POST['Reporter'])){
       $mois=getMoisSuivant($leMois);
       if ($pdo->estPremierFraisMois($id, $mois)){
       $pdo->creeNouvellesLignesFrais($id, $mois);
       }
       var_dump($id,$mois,$leLibelle,$laDate,$leMontant);
       $pdo->creeNouveauFraisHorsForfait($id,$mois,$leLibelle,$laDate,$leMontant);
       $pdo-> majLibelle($id,$leMois, $idFHF); 
 }
   
    $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($id, $leMois);
    $lesFraisForfait = $pdo->getLesFraisForfait($id, $leMois);
    $nbJustificatifs= $pdo->getNbjustificatifs($id, $leMois);
   include 'vues/v_afficheFrais_comptable.php'; 
   break;

   
    case 'validerFrais' :
      $listeVisiteur = $pdo->getListeVisiteur();//RESULTAT DE LA REQUETTE DS LA VARIABLE
      $clesvisiteur = array_keys($listeVisiteur);//
      $visiteurAselectioner = $clesvisiteur[0];   
           
    $leMois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);
    $mois = getMois(date('d/m/Y'));
    $lesMois = getles12derniersmois($mois);
    $lesCles = array_keys($lesMois);
    $moisASelectionner = $lesCles[0];
    $id = filter_input(INPUT_POST, 'lstVisiteurs', FILTER_SANITIZE_STRING);
    $leMois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);
         
        if (isset($_POST['Valider'])){ 
        $dernierMois = $pdo->dernierMoisSaisi($id);
        $laDerniereFiche = $pdo->getLesInfosFicheFrais($id, $dernierMois);
        if ($laDerniereFiche['idEtat'] == 'CL') {
            $pdo->majEtatFicheFrais($id, $dernierMois, 'VA');
        }
        include 'vues/v_validerFrais_comptable.php';
        }
break;
}
