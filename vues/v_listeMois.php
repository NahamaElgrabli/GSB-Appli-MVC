<?php
/**
 * Vue Liste des mois
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Nahama Elgrabli
 * @author    Beth Sefer
 */
?>
<h2>Mes fiches de frais</h2>
<div class="row">
    <div class="col-md-4">
        <h3>Sélectionner un mois : </h3>
    </div>
    <div class="col-md-4"> <!--  -->
        <form action="index.php?uc=etatFrais&action=voirEtatFrais" 
              method="post" role="form"> <?php // formulaire ( liste deroulante, case a cocher, bouton... ?>
            <div class="form-group"> <?php // organiser code?>
                <label for="lstMois" accesskey="n">Mois : </label> <?php // afficher ?>
                <select id="lstMois" name="lstMois" class="form-control"> <?php // balise de liste deroulante ya l'id et class pr css, name qui ns permet de recuperer les info ds controleur avec filter imput, yaure dedan ce que j'ai choisi  ?>
                    <?php //on met php pour fr le code, si c pas des balise
                    foreach ($lesMois as $unMois) { //parcourt ligne par ligne et la renome unmois pe l'utiliser
                        $mois = $unMois['mois']; //on prend le tableau a l'indice mois
                        $numAnnee = $unMois['numAnnee'];
                        $numMois = $unMois['numMois'];
                        if ($mois == $moisASelectionner) { // si le mois est a la position curseur 0, que c la premiere dc cellui par defaut
                            ?>
                            <option selected value="<?php echo $mois ?>"> <?php // options de la liste deroulante quand c'etait deja par defaut ?>
                                <?php echo $numMois . '/' . $numAnnee ?> </option>
                            <?php
                        } else {
                            ?>
                            <option value="<?php echo $mois ?>"><?php // options de la liste deroulante quand on a choisi autre chose ?>
                                <?php echo $numMois . '/' . $numAnnee ?> </option>
                            <?php
                        }
                    }
                    ?>    

                </select>
            </div>
            <input id="ok" type="submit" value="Valider" class="btn btn-success" 
                   role="button"><!--  type submit qui envoie cellon l'action declaree en haut  -->
            <input id="annuler" type="reset" value="Effacer" class="btn btn-danger" 
                   role="button"> <!-- type reset qui met ts a zero -->
        </form>
    </div>
</div