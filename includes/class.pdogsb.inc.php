<?php
/**
 * Classe d'accès aux données.
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Nahama Elgrabli
 * @author    Beth Sefer
 */
/**
 * Classe d'accès aux données.
 *
 * Utilise les services de la classe PDO
 * pour l'application GSB
 * Les attributs sont tous statiques,
 * les 4 premiers pour la connexion
 * $monPdo de type PDO
 * $monPdoGsb qui contiendra l'unique instance de la classe
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Cheri Bibi - Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL <jgil@ac-nice.fr>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   Release: 1.0
 * @link      http://www.php.net/manual/fr/book.pdo.php PHP Data Objects sur php.net
 */

class PdoGsb
{
    private static $serveur = 'mysql:host=localhost'; //static: valeur ne change pas pour toute la classe
    private static $bdd = 'dbname=gsb_frais'; 
    private static $user = 'root';
    private static $mdp = '';
    private static $monPdo;
    private static $monPdoGsb = null;

    /**
     * Constructeur privé, crée l'instance de PDO qui sera sollicitée
     * pour toutes les méthodes de la classe
     */
    private function __construct() //creer un objet
    {
        PdoGsb::$monPdo = new PDO( // cree une instance, une place . monpdo devien un objet de la classe pdo
            PdoGsb::$serveur . ';' . PdoGsb::$bdd,
            PdoGsb::$user,
            PdoGsb::$mdp
        );
        PdoGsb::$monPdo->query('SET CHARACTER SET utf8');
    }

    /**
     * Méthode destructeur appelée dès qu'il n'y a plus de référence sur un
     * objet donné, ou dans n'importe quel ordre pendant la séquence d'arrêt.
     */
    public function __destruct()
    {
        PdoGsb::$monPdo = null;
    }

    /**
     * Fonction statique qui crée l'unique instance de la classe
     * Appel : $instancePdoGsb = PdoGsb::getPdoGsb();
     *
     * @return l'unique objet de la classe PdoGsb
     */
    public static function getPdoGsb()
    {
        if (PdoGsb::$monPdoGsb == null) { // si pdoGsb c'est pas une instance de la classe, on l'instancie
            PdoGsb::$monPdoGsb = new PdoGsb();
        }
        return PdoGsb::$monPdoGsb;
    }

    /**
     * Retourne les informations d'un visiteur
     *
     * @param String $login Login du visiteur
     * @param String $mdp   Mot de passe du visiteur
     *
     * @return l'id, le nom et le prénom sous la forme d'un tableau associatif
     */
    public function getInfosVisiteur($login, $mdp)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT visiteur.id AS id, visiteur.nom AS nom, '
            . 'visiteur.prenom AS prenom '
            . 'FROM visiteur '
            . 'WHERE visiteur.login = :unLogin AND visiteur.mdp = :unMdp'
        );
        $requetePrepare->bindParam(':unLogin', $login, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMdp', $mdp, PDO::PARAM_STR);
        $requetePrepare->execute();
        return $requetePrepare->fetch();
    }
    
    /**
     * Retourne les informations d'un comptable
     *
     * @param String $login Login du comptable
     * @param String $mdp   Mot de passe du comptable
     *
     * @return l'id, le nom et le prénom sous la forme d'un tableau associatif
     */
    public function getInfosComptable($login, $mdp)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT comptable.id AS id, comptable.nom AS nom, '
            . 'comptable.prenom AS prenom '
            . 'FROM comptable '
            . 'WHERE comptable.login = :unLogin AND comptable.mdp = :unMdp'
        );
        $requetePrepare->bindParam(':unLogin', $login, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMdp', $mdp, PDO::PARAM_STR);
        $requetePrepare->execute();
        return $requetePrepare->fetch();
    }

    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais
     * hors forfait concernées par les deux arguments.
     * La boucle foreach ne peut être utilisée ici car on procède
     * à une modification de la structure itérée - transformation du champ date-
     *
     * @param String $id ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return tous les champs des lignes de frais hors forfait sous la forme
     * d'un tableau associatif
     */
    public function getLesFraisHorsForfait($id, $mois)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT * FROM lignefraishorsforfait '
            . 'WHERE lignefraishorsforfait.idvisiteur = :unIdVisiteur '
            . 'AND lignefraishorsforfait.mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $id, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute() ;
           
        $lesLignes = $requetePrepare->fetchAll(); //afficher un tableau
        for ($i = 0; $i < count($lesLignes); $i++) {
            $date = $lesLignes[$i]['date'];
            $lesLignes[$i]['date'] = dateAnglaisVersFrancais($date);
        }
        return $lesLignes;
    }

    /**
     * Retourne le nombre de justificatif d'un visiteur pour un mois donné
     *
     * @param String $id ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return le nombre entier de justificatifs
     */
    public function getNbjustificatifs($id, $mois)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT fichefrais.nbjustificatifs as nb FROM fichefrais '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $id, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        return $laLigne['nb'];
    }

    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais
     * au forfait concernées par les deux arguments
     *
     * @param String $id ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return l'id, le libelle et la quantité sous la forme d'un tableau
     * associatif
     */
    public function getLesFraisForfait($id, $mois)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT fraisforfait.id as idfrais, '
            . 'fraisforfait.libelle as libelle, '
            . 'lignefraisforfait.quantite as quantite '
            . 'FROM lignefraisforfait '
            . 'INNER JOIN fraisforfait '
            . 'ON fraisforfait.id = lignefraisforfait.idfraisforfait '
            . 'WHERE lignefraisforfait.id = :unIdVisiteur '
            . 'AND lignefraisforfait.mois = :unMois '
            . 'ORDER BY lignefraisforfait.idfraisforfait'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $id, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        return $requetePrepare->fetchAll();
    }

    /**
     * Retourne tous les id de la table FraisForfait
     *
     * @return un tableau associatif
     */
    public function getLesIdFrais()
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT fraisforfait.id as idfrais '
            . 'FROM fraisforfait ORDER BY fraisforfait.id'
        );
        $requetePrepare->execute();
        return $requetePrepare->fetchAll();
    }

    /**
     * Met à jour la table ligneFraisForfait
     * Met à jour la table ligneFraisForfait pour un visiteur et
     * un mois donné en enregistrant les nouveaux montants
     *
     * @param String $id ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param Array  $lesFrais   tableau associatif de clé idFrais et
     *                           de valeur la quantité pour ce frais
     *
     * @return null
     */
    public function majFraisForfait($id, $mois, $lesFrais)
    {
        $lesCles = array_keys($lesFrais); //tableau
        foreach ($lesCles as $unIdFrais) { // pour chaque ligne 
            $qte = $lesFrais[$unIdFrais];
           
            
            $requetePrepare = PdoGSB::$monPdo->prepare(
                'UPDATE lignefraisforfait '
                . 'SET quantite = :uneQte '
                . 'WHERE lignefraisforfait.id = :unId '
                . 'AND lignefraisforfait.mois = :unMois '
                . 'AND lignefraisforfait.idfraisforfait = :idFrais'
            );
            $requetePrepare->bindParam(':uneQte', $qte, PDO::PARAM_INT);
            $requetePrepare->bindParam(':unId', $id, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
            $requetePrepare->bindParam(':idFrais', $unIdFrais, PDO::PARAM_STR);
            $requetePrepare->execute();
        }
    }
/**
     * Met à jour la table ligneFraisHorsForfait
     * Met à jour la table ligneFraisHorsForfait pour un visiteur et
     * un mois donné en enregistrant les nouveaux montants
     *
     * @param String $id ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param String $libelle libelle du frais
     * @param String $date date du frais
     * @param Integer $montant montant du frais
     * @param String $idFrais id du frais
     *
     * @return null
     */
    public function majFraisHorsForfait($id,$mois,$libelle,$date,$montant,$idFrais)
  {
     $dateFr = dateFrancaisVersAnglais($date);
     $requetePrepare = PdoGSB::$monPdo->prepare(      
              'UPDATE lignefraishorsforfait '
             . 'SET lignefraishorsforfait.date = :uneDateFr, '
             . 'lignefraishorsforfait.montant = :unMontant, '  
             . 'lignefraishorsforfait.libelle = :unLibelle '
             . 'WHERE lignefraishorsforfait.idvisiteur = :unIdVisiteur '
             . 'AND lignefraishorsforfait.mois = :unMois '
             . 'AND lignefraishorsforfait.id = :unIdFrais'      
     );
     $requetePrepare->bindParam(':unIdVisiteur', $id, PDO::PARAM_STR);
     $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
     $requetePrepare->bindParam(':unLibelle', $libelle, PDO::PARAM_STR);
     $requetePrepare->bindParam(':uneDateFr', $dateFr, PDO::PARAM_STR);
     $requetePrepare->bindParam(':unMontant', $montant, PDO::PARAM_INT);
     $requetePrepare->bindParam(':unIdFrais', $idFrais, PDO::PARAM_INT);
     $requetePrepare->execute();
     
  }          
  
    /**
     * Met à jour le nombre de justificatifs de la table ficheFrais
     * pour le mois et le visiteur concerné
     *
     * @param String  $id     ID du visiteur
     * @param String  $mois            Mois sous la forme aaaamm
     * @param Integer $nbJustificatifs Nombre de justificatifs
     *
     * @return null
     */
    public function majNbJustificatifs($id, $mois, $nbJustificatifs)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'UPDATE fichefrais '
            . 'SET nbjustificatifs = :unNbJustificatifs '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(
            ':unNbJustificatifs',
            $nbJustificatifs,
            PDO::PARAM_INT
        );
        $requetePrepare->bindParam(':unIdVisiteur', $id, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Teste si un visiteur possède une fiche de frais pour le mois passé en argument
     *
     * @param String $id ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return vrai ou faux
     */
    public function estPremierFraisMois($id, $mois)
    {
        $boolReturn = false;
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT fichefrais.mois FROM fichefrais '
            . 'WHERE fichefrais.mois = :unMois '
            . 'AND fichefrais.idvisiteur = :unIdVisiteur'
        );
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unIdVisiteur', $id, PDO::PARAM_STR);
        $requetePrepare->execute();
        if (!$requetePrepare->fetch()) {
            $boolReturn = true;
        }
        return $boolReturn;
    }

    /**
     * Retourne le dernier mois en cours d'un visiteur
     *
     * @param String $id ID du visiteur
     *
     * @return le mois sous la forme aaaamm
     */
    public function dernierMoisSaisi($id)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT MAX(mois) as dernierMois '
            . 'FROM fichefrais '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $id, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        $dernierMois = $laLigne['dernierMois'];
        return $dernierMois;
    }
// jusque la
    /**
     * Crée une nouvelle fiche de frais et les lignes de frais au forfait
     * pour un visiteur et un mois donnés
     *
     * Récupère le dernier mois en cours de traitement, met à 'CL' son champs
     * idEtat, crée une nouvelle fiche de frais avec un idEtat à 'CR' et crée
     * les lignes de frais forfait de quantités nulles
     *
     * @param String $id ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return null
     */
    public function creeNouvellesLignesFrais($id, $mois)
   {
       $dernierMois = $this->dernierMoisSaisi($id);
       $laDerniereFiche = $this->getLesInfosFicheFrais($id, $dernierMois);
       if ($laDerniereFiche['idEtat'] == 'CR') {//'CR'=en cours.
           $this->majEtatFicheFrais($id, $dernierMois, 'CL');//'CL'=cloturée.
       }
       $requetePrepare = PdoGsb::$monPdo->prepare(
           'INSERT INTO fichefrais (idvisiteur,mois,nbJustificatifs,'
           . 'montantValide,dateModif,idEtat) '
           . "VALUES (:unIdVisiteur,:unMois,0,0,now(),'CR')"
       );
       $requetePrepare->bindParam(':unIdVisiteur', $id, PDO::PARAM_STR);
       $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
      $requetePrepare->execute();
       $lesIdFrais = $this->getLesIdFrais();
       foreach ($lesIdFrais as $unIdFrais) {
           $requetePrepare = PdoGsb::$monPdo->prepare(
               'INSERT INTO lignefraisforfait (id,mois,'
               . 'idFraisForfait,quantite) '
               . 'VALUES(:unIdVisiteur, :unMois, :idFrais, 0)'
           );
           $requetePrepare->bindParam(':unIdVisiteur', $id, PDO::PARAM_STR);
           $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
           $requetePrepare->bindParam(
               ':idFrais',
               $unIdFrais['idfrais'],
               PDO::PARAM_STR
           );
         $requetePrepare->execute();
   }
   }

    /**
     * Crée un nouveau frais hors forfait pour un visiteur un mois donné
     * à partir des informations fournies en paramètre
     *
     * @param String $id ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param String $libelle    Libellé du frais
     * @param String $date       Date du frais au format français jj//mm/aaaa
     * @param Float  $montant    Montant du frais
     *
     * @return null
     */
    public function creeNouveauFraisHorsForfait(
       $id,
       $mois,
       $libelle,
       $date,
       $montant
   ) {
       $dateFr = dateFrancaisVersAnglais($date);
       $requetePrepare = PdoGSB::$monPdo->prepare(
           'INSERT INTO lignefraishorsforfait '
           . 'VALUES (null, :unIdVisiteur,:unMois, :unLibelle, :uneDateFr,'
           . ':unMontant) '
       );
       $requetePrepare->bindParam(':unIdVisiteur', $id, PDO::PARAM_STR);
       $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
       $requetePrepare->bindParam(':unLibelle', $libelle, PDO::PARAM_STR);
       $requetePrepare->bindParam(':uneDateFr', $dateFr, PDO::PARAM_STR);
       $requetePrepare->bindParam(':unMontant', $montant, PDO::PARAM_INT);
      $requetePrepare->execute();
   }
    /**
     * Supprime le frais hors forfait dont l'id est passé en argument
     *
     * @param String $idFrais ID du frais
     *
     * @return null
     */
    public function supprimerFraisHorsForfait($idFrais)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'DELETE FROM lignefraishorsforfait '
            . 'WHERE lignefraishorsforfait.id = :unIdFrais'
        );
        $requetePrepare->bindParam(':unIdFrais', $idFrais, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Retourne les mois pour lesquel un visiteur a une fiche de frais
     *
     * @param String $id ID du visiteur
     *
     * @return un tableau associatif de clé un mois -aaaamm- et de valeurs
     *         l'année et le mois correspondant
     */
    public function getLesMoisDisponibles($id)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT fichefrais.mois AS mois FROM fichefrais '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'ORDER BY fichefrais.mois desc'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $id, PDO::PARAM_STR);
        $requetePrepare->execute(); 
        //erreur: ca me recupere que la premiere ligne
        $lesMois = array();
        while ($laLigne = $requetePrepare->fetch()) {
            $mois = $laLigne['mois'];
            $numAnnee = substr($mois, 0, 4);
            $numMois = substr($mois, 4, 2);
            $lesMois['$mois'] = array(
                'mois' => $mois,
                'numAnnee' => $numAnnee,
                'numMois' => $numMois
            );
        }
        return $lesMois;
       
    }
    /**
    * Retourne les mois pour lesquel un visiteur a une fiche de frais
    *
    * @param String $idVisiteur ID du visiteur
    *
    * @return un tableau associatif de clé un mois -aaaamm- et de valeurs
    *         l'année et le mois correspondant
    */
   public function getLesMoisDisponibles2($idVisiteur)
   {
       $requetePrepare = PdoGSB::$monPdo->prepare(
           'SELECT fichefrais.mois AS mois FROM fichefrais '
           . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
           . 'ORDER BY fichefrais.mois desc'
       );
       $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
       $requetePrepare->execute();
       $lesMois = array();
       while ($laLigne = $requetePrepare->fetch()) {
           $mois = $laLigne['mois'];
           $numAnnee = substr($mois, 0, 4);
           $numMois = substr($mois, 4, 2);
           $lesMois[] = array(
               'mois' => $mois,
               'numAnnee' => $numAnnee,
               'numMois' => $numMois
           );
       }
       return $lesMois;
   }

    /**
     * Retourne les visiteurs possedant une fiche de frais
     *
     * @param String $id ID du visiteur
     *
     * @return un tableau associatif de clé un mois -aaaamm- et de valeurs
     *         l'année et le mois correspondant
     */
    public function getLesVisiteurs($id)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT fichefrais.mois AS mois FROM fichefrais '
            . 'WHERE fichefrais.id = :unIdVisiteur '
            . 'ORDER BY fichefrais.mois desc'
        );
        $requetePrepare->bindParam(':unId', $id, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesMois = array();
        while ($laLigne = $requetePrepare->fetch()) {
            $mois = $laLigne['mois'];
            $numAnnee = substr($mois, 0, 4);
            $numMois = substr($mois, 4, 2);
            $lesVisiteurs['$visiteurs'] = array(
                'mois' => $mois,
                'numAnnee' => $numAnnee,
                'numMois' => $numMois
            );
        }
        return $lesVisiteurs['$visiteurs'];
    }
    
    /**
     * Retourne les informations d'une fiche de frais d'un visiteur pour un
     * mois donné
     *
     * @param String $id ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return un tableau avec des champs de jointure entre une fiche de frais
     *         et la ligne d'état
     */
    public function getLesInfosFicheFrais($id, $mois)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT ficheFrais.idEtat as idEtat, '
            . 'ficheFrais.dateModif as dateModif,'
            . 'ficheFrais.nbJustificatifs as nbJustificatifs, '
            . 'ficheFrais.montantValide as montantValide, '
            . 'etat.libelle as libEtat '
            . 'FROM fichefrais '
            . 'INNER JOIN Etat ON ficheFrais.idEtat = Etat.id '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $id, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        return $laLigne;
    }

    /**
     * Modifie l'état et la date de modification d'une fiche de frais.
     * Modifie le champ idEtat et met la date de modif à aujourd'hui.
     *
     * @param String $id ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param String $etat       Nouvel état de la fiche de frais
     *
     * @return null
     */
    public function majEtatFicheFrais($id, $mois, $etat)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'UPDATE ficheFrais '
            . 'SET idEtat = :unEtat, dateModif = now() '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unEtat', $etat, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unIdVisiteur', $id, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
           
        
    }
    /**
     * Retourne la liste de tous les visiteurs.
     *
     * @return array     la liste de tous les visiteurs sous forme de tableau associatif.
     */
    public function getListeVisiteur()
   {
       $requetePrepare = PdoGSB::$monPdo->prepare(
           'SELECT  visiteur.id AS id,'
           . 'visiteur.nom as nom,'
           . 'visiteur.prenom AS prenom '
           . 'FROM visiteur'

       );
       $requetePrepare->execute();
       return $requetePrepare->fetchAll();
       
   }
  /**
     * Met à jour le libelle de la table ligneFraisHorsForfait
     * pour le mois et le visiteur concerné pour un frais donné
     *
     * @param String  $id     ID du visiteur
     * @param String  $mois            Mois sous la forme aaaamm
     * @param String $idFrais id du frais
     *
     * @return null
     */ 
   public function majLibelle($id, $mois, $idFrais){
    $requetePrepare = PdoGSB::$monPdo->prepare(      
              'UPDATE lignefraishorsforfait '  
             . 'SET lignefraishorsforfait.libelle = CONCAT ("REFUSE: ", libelle) '
             . 'WHERE lignefraishorsforfait.idvisiteur = :unIdVisiteur '
             . 'AND lignefraishorsforfait.mois = :unMois '
             . 'AND lignefraishorsforfait.id = :unIdFrais'
          //   . 'AND lignefraishorsforfait.libelle NOT LIKE "REFUSE:%" '
     );
     $requetePrepare->bindParam(':unIdVisiteur', $id, PDO::PARAM_STR);
     $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
     $requetePrepare->bindParam(':unIdFrais', $idFrais, PDO::PARAM_INT);
     $requetePrepare->execute();  
   }
   
   /**
     * Retourne montant km d'un visiteur selon son type de voiture
     *
     * @param String $id ID du visiteur
    
     *
     * @return le montant km selon le type
     */

    public function getMontantVehicule($id)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT vehicule.montant'
            . 'FROM vehicule '
            . 'INNER JOIN visiteur ON vehicule.id = visiteur.idVehicule '
            . 'WHERE visiteur.idvisiteur = :unIdVisiteur '
            
        );
        $requetePrepare->bindParam(':unIdVisiteur', $id, PDO::PARAM_STR);
        $requetePrepare->execute(); 
        return $requetePrepare->fetch();
    }

    
/*
public function getMontantUnitaire($id,$idFrais){
    if ($idFrais = 'KM'){
    $montant=$this->getMontantVehicule($id);
   
   }else{
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT fraisforfait.montant'
            . 'FROM fraisforfait '
            . 'WHERE fraisforfait.id = :unidFrais'    
        );
        $requetePrepare->bindParam(':unidFrais', $idFrais, PDO::PARAM_STR);
        if($requetePrepare->execute()){
            echo 'succe';
        } else{
            echo 'echec';
        }
        $montant=$requetePrepare->fetch();
}
return $montant;
}
*/


 /**
     * Met à jour la table Fichefrais pour le montantValide 
     *
     * @param String $id ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param Integer $total
     *
     * @return null
     */
    public function majTotalFichefrais($id, $mois, $total) 
    {

     $requetePrepare = PdoGSB::$monPdo->prepare(
                'UPDATE fichefrais '
                . 'SET montantvalide = :unTotal '
                . 'WHERE fichefrais.idvisiteur = :unIdvisiteur '
                . 'AND fichefrais.mois = :unMois '
            );
            $requetePrepare->bindParam(':unTotal', $total, PDO::PARAM_INT);
            $requetePrepare->bindParam(':unIdvisiteur', $id, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
             if($requetePrepare->execute()){
          echo 'succe';
      }else{
          echo 'echec';
      }
        
       
    }
    /**
     * Calcule la somme des frais forfait pour un visiteur et un mois donné 
     * (produit des quantités par le montant des frais forfait)
     *
     * @param String $id     ID du visiteur
     * @param String $leMois          Mois du frais
     *
     * @return un tableau avec le montant des frais forfait
     */
     public function TotalFF($id,$mois){
      $requetePrepare = PdoGSB::$monPdo->prepare(
          'SELECT SUM(lignefraisforfait.quantite * fraisforfait.montant)'
          .'FROM lignefraisforfait JOIN fraisforfait ON (fraisforfait.id=lignefraisforfait.idfraisforfait)'
          . 'WHERE lignefraisforfait.id = :unIdVisiteur '
          . 'AND lignefraisforfait.mois = :unMois'    
      );
      $requetePrepare->bindParam(':unIdVisiteur', $id, PDO::PARAM_STR);
      $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
      $requetePrepare->execute();
      return $requetePrepare->fetchAll();
     
     }
    /**
     * Calcule la somme des frais hors forfait pour un visiteur et un mois donné 
     * (somme de tous les montants des frais hors forfait)
     *
     * @param String $id     ID du visiteur
     * @param String $leMois          Mois du frais
     *
     * @return un tableau avec la somme des frais hors forfait
     */ 
     public function TotalFHF($id,$mois)
   {  
       $requetePrepare = PdoGSB::$monPdo->prepare(
           'SELECT SUM(lignefraishorsforfait.montant )'
           .'FROM lignefraishorsforfait '
           . 'WHERE lignefraishorsforfait.idvisiteur = :unIdVisiteur '
           . 'AND lignefraishorsforfait.mois = :unMois '  
           . 'AND lignefraishorsforfait.libelle NOT LIKE "REFUSE%" '
    );
      $requetePrepare->bindParam(':unIdVisiteur', $id, PDO::PARAM_STR);
      $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
      $requetePrepare->execute();
      return $requetePrepare->fetchAll();
   }
   
    /**
     * Modifie le champ montantValide de fichefrais, calcule le montant total 
     * des frais  pour le mois et le visiteur donne
     *
     * @param String $id       ID du visiteur
     * @param String $leMois            Mois sous la forme aaaamm
     * @param String $totalFF             montant total des frais forfait pour ce mois
     * @param String $totalFHF             montant total des frais hors forfait pour ce mois
     *
     * @return null
     */
   public function calculMontantValide($id,$leMois,$totalFF,$totalFHF){
        for ($i = 0; $i < count($totalFHF); $i++) {
          $unMontant=$totalFF[$i];
          $unMontantH=$totalFHF[$i];
          for ($k = 0; $k < 1; $k++) {
               $unMontantF=$unMontant[$k];
               $unMontantHF=$unMontantH[$k];
               $requetePrepare = PdoGSB::$monPdo->prepare(
                   'UPDATE fichefrais '
                   . 'SET montantValide = :montantTotalFF+:montantTotalFHF '
                   . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
                   . 'AND fichefrais.mois = :unMois'
               );
               $requetePrepare->bindParam(':unIdVisiteur', $id, PDO::PARAM_STR);
               $requetePrepare->bindParam(':montantTotalFF', $unMontantF, PDO::PARAM_STR);
               $requetePrepare->bindParam(':montantTotalFHF', $unMontantHF, PDO::PARAM_STR);
               $requetePrepare->bindParam(':unMois', $leMois, PDO::PARAM_STR);                      
               $requetePrepare->execute();
            }
      }
      return $requetePrepare;  
   }
   
   /**
     * Retourne la liste de tous les visiteurs qui ont des fiches validées.
     *
     * @return array     la liste de tous les visiteurs sous forme de tableau associatif.
     */
    public function getLesVisiteursDontFicheVA()
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT DISTINCT*'
            .'FROM visiteur join fichefrais on(id=idvisiteur)'
            .'WHERE fichefrais.idetat="VA"'
            .'Group by visiteur.id, visiteur.nom, visiteur.prenom'
           // .'ORDER BY nom'
        );
        if($requetePrepare->execute()){
        echo'succe';} else {
    echo'echec';
}
        return $requetePrepare->fetchAll();
    }

    /**
     * Retourne les mois pour lesquel un visiteur a une fiche de frais validée
     *
     * @param string $idVisiteur ID du visiteur
     *
     * @return un tableau associatif de clé un mois -aaaamm- et de valeurs
     *         l'année et le mois correspondant
     */
    public function getLesMoisDontFicheVA()
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT distinct fichefrais.mois AS mois FROM fichefrais '
            . 'WHERE fichefrais.idetat="VA"'    
            . 'ORDER BY fichefrais.mois desc'
        );
        $requetePrepare->execute();
        $lesMois = array();
        while ($laLigne = $requetePrepare->fetch()) {
            $mois = $laLigne['mois'];
            $numAnnee = substr($mois, 0, 4);
            $numMois = substr($mois, 4, 2);
            $lesMois[] = array(
                'mois' => $mois,
                'numAnnee' => $numAnnee,
                'numMois' => $numMois
            );
        }
        return $lesMois;
    }
}
