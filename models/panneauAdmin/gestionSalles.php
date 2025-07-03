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


require_once('../../templates/headers.php');

?>

<main>
    <article>
        <h1>Gérer les Salles</h1>
        <button id="nouveau">Nouvelle Salles</button>
            <section id="form-nouveau" style="display: none;">
                <form id="form2" method="POST" action="">

                    <label for="name" class="txt">Nom</label>
                    <input type="text" id="name" name="name" class="form1" required />

                    <button type="submit" name="action" value="creer">Créer</button>
                </form>
            </section>
    <table>
        <h2>Liste des Salles</h2>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($salles as $salle) { ?> 
            <tr>
                <td><?php print $salle['nom_salle'] ?></td>
                <td>
                <!-- Formulaire SUPPRIMER -->    
                <form method="POST" action="" onsubmit="return confirm('Voulez-vous vraiment supprimer cette salle ?');">
                    <input type="hidden" name="id_salle" value="<?php echo $salle['id_salle']; ?>">
                    <button type="submit" name="action" value="supprimer">Supprimer</button>
                    </form>
                <!-- Formulaire MODIFIER -->
                    <button type="button" class="modifier-btn"
                        data-id="<?= $salle['id_salle'] ?>"
                        data-name="<?= htmlspecialchars($salle['nom_salle']) ?>"
                    >Modifier</button>
               </td>
            </tr>
            <?php } ?>
            
        </tbody>
    </table>
    <!-- Formulaire HTML de modification -->
     <section id="form-modifier"  style="display: none;">
<form  method="POST" action="" >
    <input type="hidden" name="id_salle" value="">
    <label>Nom:</label>
    <input type="text" name="name" value=""><br>
    <button type="submit" name="action" value="enregistrer">Enregistrer les modifications</button>
</form>
</section>
    </article>
    <script src="../../public/js/adminSalles.js"></script>
</main>