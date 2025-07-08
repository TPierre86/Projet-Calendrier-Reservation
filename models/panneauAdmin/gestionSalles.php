<?php 
require_once('../../database/DAO.php');
session_start();
$dao = new DAOReservation();
$dao->connexion();

$salles =$dao->getSalles();


// activer les boutons spprimer et modifier
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    $action = $_POST['action'];
    
/**
 * ! Création d'un nouvelle Salle
 */
    if ($action === 'creer') {
        //Recuperer les données

            $name = isset($_POST['name'])?($_POST['name']):"";
    
         /*Comparer les données pour ne pas crée de news users doublons*/
         $salleExists= false;
        foreach($salles as $salle){
        
            if ($salle["nom_salle"] == $name ) {
                $salleExists = true;
                break;
            }
        }

            if ($salleExists) {
                $message = "Salle déjà existante.";
                echo "<script type='text/javascript'>alert('$message');</script>";
            } else {
                $success = $dao->NewSalle($name);
        
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
 * ! Supprimer une salle
 */

    if ($action === 'supprimer') {
        $id_salle = $_POST['id_salle'];
        $dao->deleteSalle($id_salle);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
/**
 * ! Modifier une salle
 */
        if ($action === 'enregistrer'){

        $id_salle = isset($_POST['id_salle']) ? $_POST['id_salle'] : '';
        $name = isset($_POST['name']) ? $_POST['name'] : '';
        $dao->updateSalle($id_salle, $name);
        // Redirection ou traitement du formulaire de modification
        header("Location: gestionSalles.php");
        exit;
        }
    
}
// Pagination
$parPage = 5; // Nombre de lignes par page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$total = count($salles);
$totalPages = ceil($total / $parPage);
$start = ($page - 1) * $parPage;
$sallesPage = array_slice($salles, $start, $parPage);



require_once('../../templates/headers.php');

?>

<main id="mainGestion">
    <article id="formGestion">
        <h1 id="titreGestion">Gérer les Salles</h1>
        <button class="submitButton" id="nouveau">Nouvelle Salles</button>
            <section id="gestionRow"> 
                <section id="form-nouveau" style="display: none;">
                    <form id="formCreer" method="POST" action="">

                        <label for="name" class="txt">Nom</label>
                        <input type="text" id="name" name="name" class="form1" required />

                        <button class="submitButton" id="creer" type="submit" name="action" value="creer">Créer</button>
                    </form>
                </section>
                <section id="tableContainer">
                    <h2 id="titreListe">Liste des Salles</h2>
                        <table id="tableListe">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sallesPage as $salle) { ?> 
                                <tr>
                                    <td><?php print $salle['nom_salle'] ?></td>
                                    <td>
                                    <article id="rowButton">
                                    <!-- Formulaire SUPPRIMER -->    
                                    <form method="POST" action="" onsubmit="return confirm('Voulez-vous vraiment supprimer cette salle ?');">
                                        <input class="form1" type="hidden" name="id_salle" value="<?php echo $salle['id_salle']; ?>">
                                        <button class="submitButton" id="supprimer" type="submit" name="action" value="supprimer"><i class="fa-solid fa-trash"></i></button>
                                        </form>
                                    <!-- Formulaire MODIFIER -->
                                        <button id="modifier" type="button" class="modifier-btn submitButton"
                                            data-id="<?= $salle['id_salle'] ?>"
                                            data-name="<?= htmlspecialchars($salle['nom_salle']) ?>"
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
                                <a href="../../controllers.php" class="btn-retour" style="font-weight:bold;text-decoration:underline;">Retour</a>
                            <?php endfor; ?>
                            <?php if ($page < $totalPages): ?>
                                <a href="?page=<?= $page + 1 ?>">Suivant &raquo;</a>
                            <?php endif; ?>
                        </section>
                </section>
                <!-- Formulaire HTML de modification -->
                <section id="form-modifier" style="display: none;">
                    <form id="formModifier"  method="POST" action="" >
                        <input type="hidden" name="id_salle" value="">
                        <label for="name" class="txt">Nom:</label>
                        <input class="form1" type="text" name="name" value=""><br>
                        <button class="submitButton" id="enregistrer" type="submit" name="action" value="enregistrer">Enregistrer les modifications</button>
                    </form>
                </section>
        </section>    
    </article>
    <script src="../../public/js/adminSalles.js"></script>
    <script src="https://kit.fontawesome.com/5bef22b054.js" crossorigin="anonymous"></script>
</main>