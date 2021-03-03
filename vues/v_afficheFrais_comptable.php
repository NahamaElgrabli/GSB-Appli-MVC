<?php
/**
 * Vue valider les frais
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Nahama Elgrabli
 * @author    Beth Sefer
 */
?>


<form method="post" 
              action="index.php?uc=validerFrais&action=corrigerFrais" 
              role="form">
  
     <div class="row">  
         <div>
    <h2 style="color:orange">&nbsp;Valider la fiche de frais</h2>
         <div class="form-group" style="display: inline-block">
           <label for="lstVisiteurs" accesskey="n">Choisir le visiteur : </label>
           <select id="lstVisiteurs" name="lstVisiteurs" class="form-control">
               <?php
               foreach ($listeVisiteur as $unVisiteur) {
                   $id = $unVisiteur['id'];
                   $nom = $unVisiteur['nom'];
                   $prenom = $unVisiteur['prenom'];
                   if ($id == $leVisiteurASelectionner) {
                       ?>
                       <option selected value="<?php echo $id ?>">
                           <?php echo $nom . ' ' . $prenom ?> </option>
                       <?php
                   } else {
                       ?>
                       <option value="<?php echo $id ?>">
                           <?php echo $nom . ' ' . $prenom ?> </option>
                       <?php
                   }
               }
               ?>    

           </select>
       </div>
         
       <?php//liste déroulante des mois?>          
       &nbsp;<div class="form-group" style="display: inline-block">
           <label for="lstMois" accesskey="n">Mois : </label>
           <select id="lstMois" name="lstMois" class="form-control">
               <?php
               foreach ($lesMois as $unMois) {
                   $mois = $unMois['mois'];
                   $numAnnee = $unMois['numAnnee'];
                   $numMois = $unMois['numMois'];
                   if ($mois == $moisASelectionner) {
                       ?>
                       <option selected value="<?php echo $mois ?>">
                           <?php echo $numMois . '/' . $numAnnee ?> </option>
                       <?php
                   } else {
                       ?>
                       <option value="<?php echo $mois ?>">
                           <?php echo $numMois . '/' . $numAnnee ?> </option>
                       <?php
                   }
               }
               ?>    

           </select>
       </div>
   </div>
    <div>
    <h3>Eléments forfaitisés</h3>
    <div >
    <div class="col-md-4">
        
               <br><br>
            <fieldset>       
                <?php
                foreach ($lesFraisForfait as $unFrais) {
                    $idFrais = $unFrais['idfrais'];
                    $libelle = htmlspecialchars($unFrais['libelle']);
                    $quantite = $unFrais['quantite']; ?>
                    <div class="form-group">
                        <label for="idFrais"><?php echo $libelle ?></label>
                        <input type="text" id="idFrais" 
                               name="lesFrais[<?php echo $idFrais ?>]"
                               size="10" maxlength="5" 
                               value="<?php echo $quantite ?>" 
                               class="form-control">
                    </div>
                    <?php
                }
                ?>
                <button class="btn btn-success" type="submit">Corriger</button>
                <button class="btn btn-danger" type="reset">Renitialiser</button>
            </fieldset>
    </div>
   </div>
    </div>
   </div>      
</form>

<hr>
<div  class="row">
    <div>
    <div  class="panel panel-infoC">
                <div   class="panel-headingC">Descriptif des éléments hors forfait</div>
                <form  method="post" 
              action="index.php?uc=validerFrais&action=corrigerFraisHF"
              role="form"> 
                <input name="lstMois" type="hidden" id="lstMois" class="form-control" value="<?php echo $moisASelectionner ?>">
                          <input name="lstVisiteurs" type="hidden" id="lstVisiteurs" class="form-control" value="<?php echo $leVisiteurASelectionner ?>">


                          <table  class="table table-bordered table-responsive" id="lstFrais" name="lstFrais" class="form-control">
                       <thead>
                           <tr >
                               <th class="date">Date</th>
                               <th class="libelle">Libellé</th>  
                               <th class="montant">Montant</th>  
                               <th class="action">&nbsp;</th> 
                           </tr>
                       </thead>  
                       <tbody>
                       <?php
                       foreach ($lesFraisHorsForfait as $unFraisHorsForfait) {
                           $libelle = htmlspecialchars($unFraisHorsForfait['libelle']);
                           $date = $unFraisHorsForfait['date'];
                           $montant = $unFraisHorsForfait['montant'];
                           $idFHF = $unFraisHorsForfait['id']; ?>           
                           <tr>


                                <td><input type="text" size="10" maxlength="10" id="date" name="date" 
                                          value="<?php echo $date ?>" class="form-control">  </td>
                                 <td><input type="text" id="libelle" name="libelle"
                                          value="<?php echo $libelle ?>"   class="form-control">  </td>
                               <td><input type="text" size="10"  id="montant" name="montant"
                                          value="<?php echo $montant ?>"   class="form-control">  </td>
                               <td><input type="hidden" size="10"  id="idFHF" name="idFHF"
                                          value="<?php echo $idFHF ?>"   class="form-control">  </td>
                               <td> <button class="btn btn-success" type="edit" name="Corriger">Corriger</button>
                                    <button class="btn btn-danger" type="reset">Renitialiser</button> 
                                    <button   class="btn btn-success" type="edit" name="Reporter">Reporter</button>

                               </td>
                           </tr>
                           <?php
                       }
                       ?>

                       </tbody>  
                       </table>
                    </form>
      </div>
    </div>
    </div>
  
  
  
  <form method="post"action="index.php?uc=validerFrais&action=validerFrais"role="form">
      <input type="hidden" id="lstMois" name="lstMois" value="<?= $moisASelectionner ?>">
      <input type="hidden" id="lstVisiteurs" name="lstVisiteurs" value="<?= $leVisiteurASelectionner ?>">
      Nombre de justificatifs: <input type="text" id="nbJust" name="nbJust" size="4" value="<?= $nbJustificatifs ?>">
      <br><br>
      <button class="btn btn-success" type="submit" name="Valider">Valider</button>
      <button class="btn btn-danger" type="reset">Effacer</button>
  </form>

