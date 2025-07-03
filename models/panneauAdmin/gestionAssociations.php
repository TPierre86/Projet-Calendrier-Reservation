<?php 
require_once('../../database/DAO.php');
session_start();
$dao = new DAOReservation();
$dao->connexion();

$associations =$dao->getAssociations();


// activer les boutons spprimer et modifier
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    $action = $_POST['action'];
    
/**
 * ! Création d'un nouvelle Association
 */
    if ($action === 'creer') {
        //Recuperer les données

            $name = isset($_POST['name'])?($_POST['name']):"";
    
         /*Comparer les données pour ne pas crée de news users doublons*/
         $associationExists= false;
        foreach($associations as $association){
        
            if ($association["nom_association"] == $name ) {
                $associationExists = true;
                break;
            }
        }

            if ($associationExists) {
                $message = "Association déjà existante.";
                echo "<script type='text/javascript'>alert('$message');</script>";
            } else {
                $success = $dao->NewAssociation($name);
        
                if ($success) {
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit;
                } else {
                    echo "Erreur lors de la création.";
                }
            }
        exit;
    }
/**
 * ! Supprimer une association
 */

    if ($action === 'supprimer') {
        $id_association = $_POST['id_association'];
        $dao->deleteAssociation($id_association);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
/**
 * ! Modifier une association
 */
        if ($action === 'enregistrer'){

        $id_association = isset($_POST['id_association']) ? $_POST['id_association'] : '';
        $name = isset($_POST['name']) ? $_POST['name'] : '';
        $dao->updateAssociation($id_association, $name);
        // Redirection ou traitement du formulaire de modification
        header("Location: gestionAssociations.php");
        exit;
        }
    
}


require_once('../../templates/headers.php');

?>

<main>
    <article>
        <h1>Gérer les Associations</h1>
        <button id="nouveau">Nouvelle Association</button>
            <section id="form-nouveau" style="display: none;">
                <form id="form2" method="POST" action="">

                    <label for="name" class="txt">Nom</label>
                    <input type="text" id="name" name="name" class="form1" required />

                    <button type="submit" name="action" value="creer">Créer</button>
                </form>
            </section>
    <table>
        <h2>Liste des Associations</h2>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($associations as $association) { ?> 
            <tr>
                <td><?php print $association['nom_association'] ?></td>
                <td>
                <!-- Formulaire SUPPRIMER -->    
                <form method="POST" action="" onsubmit="return confirm('Voulez-vous vraiment supprimer cette association ?');">
                    <input type="hidden" name="id_association" value="<?php echo $association['id_association']; ?>">
                    <button type="submit" name="action" value="supprimer">Supprimer</button>
                    </form>
                <!-- Formulaire MODIFIER -->
                    <button type="button" class="modifier-btn"
                        data-id="<?= $association['id_association'] ?>"
                        data-name="<?= htmlspecialchars($association['nom_association']) ?>"
                    >Modifier</button>
               </td>
            </tr>
            <?php } ?>
            
        </tbody>
    </table>
    <!-- Formulaire HTML de modification -->
     <section id="form-modifier"  style="display: none;">
<form  method="POST" action="" >
    <input type="hidden" name="id_association" value="">
    <label>Nom:</label>
    <input type="text" name="name" value=""><br>
    <button type="submit" name="action" value="enregistrer">Enregistrer les modifications</button>
</form>
</section>
    </article>
    <script src="../../public/js/adminAssociations.js"></script>
</main>