<?php
/**
 * Vue choix pour suivis de frais
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Nahama Elgrabli
 * @author    Beth Sefer
 */
?>

<h2 style="color:orange">Suivre le paiement des fiches de frais</h2>
<div class="row">
    <div class="col-md-4">
        <form action="index.php?uc=suiviFrais&action=afficheFrais" 
              method="post" role="form">
            <?php//liste déroulante des mois?>
            <select id="lstVisiteur" name="lstVisiteur" type="hidden" class="form-control">
           <?php
                      foreach ($listeVisiteur as $unVisiteur)  {

                             $nom = $unVisiteur['nom'];
                             $prenom =  $unVisiteur['prenom'];
                             $numvisiteur = $unVisiteur['id'];
                             if ($unVisiteur = $visiteurAselectioner) {
                             ?>
                             <option selected value="<?php echo $numvisiteur?>">
                             <?php echo $nom .'   ' .$prenom?> </option> -->
                             <?php
                               } else {
                               ?>    
                            <option value="<?php echo $numvisiteur?>">
                            <?php echo $nom .'   ' .$prenom?> </option> -->
                          <?php
                             }

                             }
                            ?>
                         </select>
                 
            
                 <label for="lstMois" accesskey="n">Mois : </label>
                <select id="lstMois" name="lstMois" type="hidden" class="form-control">
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
                        }else{
                            ?>
                            <option value="<?php echo $mois ?>">
                                <?php echo $numMois . '/' . $numAnnee ?> </option>
                            <?php
                        }
                    }
                    ?> 
                </select>        
            </div>
            <br>
             <input id="ok" type="submit" value="Valider" class="btn btn-success"
              role="button">
        </form>
    </div>
</div>


