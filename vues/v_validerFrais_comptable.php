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



  
        <h3>Choisir un visiteur : </h3>
    <div class="row">
    <div class="col-md-4">
        <form action="index.php?uc=validerFrais&action=afficheFrais" 
              method="post" role="form">
            
            <div class="form-group">
                <label for="lstVisiteur" accesskey="n">Visiteurs : </label>
                <select id="lstVisiteur" name="lstVisiteurs" class="form-control">
                    
                    <?php
                      foreach ( $listeVisiteur  as $unVisiteur)  {

                             $nom = $unVisiteur['nom'];
                             $prenom =  $unVisiteur['prenom'];
                             $numvisiteur = $unVisiteur['id'];
                             if ($idvisiteur != $visiteurAselectionner) {
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
              </from>
        </div>
        

    </div>

    

               