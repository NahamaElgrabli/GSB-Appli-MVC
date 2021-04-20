<?php
/**
 * Vue paiement pour le suivi des frais
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
              action="index.php?uc=suiviFrais&action=paiement" 
              role="form">
    <input name="lstMois" type="hidden" id="lstMois" class="form-control" value="<?php echo $moisASelectionner ?>">
    <input name="lstVisiteurs" type="hidden" id="lstVisiteurs" class="form-control" value="<?php echo $visiteurASelectionner ?>">
    <input id="ok" type="submit" value="Mise en paiement" class="btn btn-success" 
            role="button">
</form>