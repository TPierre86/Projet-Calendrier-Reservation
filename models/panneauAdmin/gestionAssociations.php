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
// Pagination
$parPage = 5; // Nombre de lignes par page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$total = count($associations);
$totalPages = ceil($total / $parPage);
$start = ($page - 1) * $parPage;
$associationsPage = array_slice($associations, $start, $parPage);



require_once('../../templates/headers.php');

?>

<main id="mainGestion">
    <article id="formGestion">
        <h1 id="titreGestion">Gérer les Associations</h1>
        <button class="submitButton" id="nouveau">Nouvelle Association</button>
        <section id="gestionRow">   
            <section id="form-nouveau" style="display: none;">
                <form id="formCreer" method="POST" action="">

                    <label for="name" class="txt" id="labelNom">Nom</label>
                    <input type="text" id="name" name="name" class="form1" required />

                    <button class="submitButton" id="creer" type="submit" name="action" value="creer">Créer</button>
                </form>
            </section>
            <section id="tableContainer">
                    <h2 id="titreListe">Liste des Associations</h2>
                <table id="tableListe">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($associationsPage as $association) { ?> 
                        <tr>
                            <td><?php print $association['nom_association'] ?></td>
                            <td >
                                <article id="rowButton">
                                <!-- Formulaire SUPPRIMER -->    
                                <form method="POST" action="" onsubmit="return confirm('Voulez-vous vraiment supprimer cette association ?');">
                                    <input class="form1" type="hidden" name="id_association" value="<?php echo $association['id_association']; ?>">
                                    <button class="submitButton" id="supprimer" type="submit" name="action" value="supprimer"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                <!-- Formulaire MODIFIER -->
                                    <button id="modifier" type="button" class="modifier-btn submitButton"
                                        data-id="<?= $association['id_association'] ?>"
                                        data-name="<?= htmlspecialchars($association['nom_association']) ?>"
                                    ><i class="fa-solid fa-pen"></i></button>
                                </article>
                            </td>
                        </tr>
                        <?php } ?>
                        
                    </tbody>
                </table>
                <section class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?>">&laquo; Précédent</a>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?= $i ?>" <?= $i === $page ? 'style="font-weight:bold;text-decoration:underline;"' : '' ?>><?= $i ?></a>
                    <?php endfor; ?>
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?= $page + 1 ?>">Suivant &raquo;</a>
                    <?php endif; ?>
                    </section>
            </section>
            <!-- Formulaire HTML de modification -->
            <section id="form-modifier"  style="display: none;">
                <form id="formModifier" method="POST" action="" >
                    <input type="hidden" name="id_association" value="">
                    <label for="name" class="txt">Nom:</label>
                    <input class="form1" type="text" name="name" value=""><br>
                    <button class="submitButton" id="enregistrer" type="submit" name="action" value="enregistrer">Enregistrer les modifications</button>
                </form>
            </section>
        </section>    
    </article>
    <script src="../../public/js/adminAssociations.js"></script>
    <script src="https://kit.fontawesome.com/5bef22b054.js" crossorigin="anonymous"></script>
</main>