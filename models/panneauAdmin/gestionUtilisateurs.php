<?php 
require_once('../../database/DAO.php');
session_start();
$dao = new DAOReservation();
$dao->connexion();

$utilisateurs =$dao->UtilisateurLabelAssociation();
$associations =$dao->getAssociations();
$users= $dao->getUtilisateurs();



// activer les boutons spprimer et modifier
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    $action = $_POST['action'];
    
/**
 * ! Création d'un nouveau Utilisateur
 */
    if ($action === 'creer') {
        //Recuperer les données

            $name = isset($_POST['name'])?($_POST['name']):"";
            $firstName = isset($_POST['firstName'])?($_POST['firstName']):"";
            $tel = isset($_POST['tel'])?($_POST['tel']):"";
            $mail = isset($_POST['mail'])?($_POST['mail']):"";
            $pwd = isset($_POST['pwd']) ? password_hash($_POST['pwd'], PASSWORD_DEFAULT) : "";
            $profil = isset($_POST['profil'])?($_POST['profil']):"";
            $association_id = isset($_POST['association_id'])?($_POST['association_id']):"";
    
         /*Comparer les données pour ne pas crée de news users doublons*/
         $userExists = false;
        foreach($users as $user){
        
            if ($user["nom_utilisateur"] == $name && $user["prenom_utilisateur"] == $firstName || $user["email"] == $mail) {
                $userExists = true;
                break;
            }
        }

            if ($userExists) {
                $message = "Utilisateur déjà inscrit.";
                echo "<script type='text/javascript'>alert('$message');</script>";
            } else {
                $success = $dao->NewUtilisateur($name, $firstName, $tel, $mail, $pwd, $profil, $association_id);
        
                if ($success) {
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit;
                } else {
                    echo "Erreur lors de l'inscription.";
                }
            }
        exit;
    }
/**
 * ! Supprimer un Utilisateur
 */

    if ($action === 'supprimer') {
        $id_utilisateur = $_POST['id_utilisateur'];
        $dao->deleteUtilisateur($id_utilisateur);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
/**
 * ! Modifier un Utilisateur
 */
        if ($action === 'enregistrer'){
        $id_utilisateur = $_POST['id_utilisateur'];
        $users= $dao->getUtilisateurs();
            $name = isset($_POST['name'])?($_POST['name']):"";
            $firstName = isset($_POST['firstName'])?($_POST['firstName']):"";
            $tel = isset($_POST['tel'])?($_POST['tel']):"";
            $mail = isset($_POST['mail'])?($_POST['mail']):"";
                if (!empty($_POST['pwd'])) {
                    $pwd = password_hash($_POST['pwd'], PASSWORD_DEFAULT);
                    $dao->updatePassword($id_utilisateur, $pwd); // une méthode dédiée
                }
            $profil = isset($_POST['profil'])?($_POST['profil']):"";
            $association_id = isset($_POST['association_id'])?($_POST['association_id']):"";    
        
        $dao->updateUtilisateur($id_utilisateur, $name, $firstName, $mail, $tel, $profil, $association_id);
        // Redirection ou traitement du formulaire de modification
        header("Location: gestionUtilisateurs.php");
        exit;
        }
    
}
// Pagination
$parPage = 5; // Nombre de lignes par page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$total = count($utilisateurs);
$totalPages = ceil($total / $parPage);
$start = ($page - 1) * $parPage;
$utilisateursPage = array_slice($utilisateurs, $start, $parPage);



require_once('../../templates/headers.php');

?>      
<main id="mainGestion">
    <article id="formGestion">
        <h1 id="titreGestion">Gérer les utilisateurs</h1>
        <button class="submitButton" id="nouveau">Nouveau Utilisateur</button>
        <section id="gestionRow">
            <section id="form-nouveau" style="display: none;">
                <form id="formCreer" method="POST" action="">
                    <label for="name" class="txt">Nom</label>
                    <input type="text" id="name" name="name" class="form1" required />
        
                    <label for="firstName" class="txt">Prénom</label>
                    <input type="text" id="firstName" name="firstName" class="form1" required/>
                    
                    <label for="tel" class="txt">n° Téléphone (format: 0615251168)</label>
                    <input type="text" id="tel" name="tel" class="form1" pattern="0[67][0-9]{8}" required />
                    
                    <label for="mail" class="txt">Adresse e-mail</label>
                    <input type="email" id="mail" name="mail" class="form1" required />
                    
                    <label for="pwd" class="txt">Mots de passe</label>
                    <input type="password" id="pwd" name="pwd" class="form1" required />

                    <label for="profil" class="txt">Profil</label>
                    <select name="profil" id="profil" class="form1">
                        <option value="Gestionnaire">Gestionnaire</option>
                        <option value="Président d'association">Président d'association</option>
                        <option value="Ménage">Ménage</option>
                        <option value="Membre">Membre</option>
                    </select>
                    
                    <label for="association" class="txt">Associations</label>
                    <select name="association_id" class="form1">
                        <option value="" disabled selected hidden>Choisissez une association</option>
                        <?php foreach($associations as $association) { ?>
                        <option value="<?php print $association['id_association']; ?>"><?php print $association['nom_association']; ?></option>
                        <?php } ?>
                    </select>

                    <button class="submitButton" id="creer" type="submit" name="action" value="creer">Créer</button>
                </form>
            </section>
        <section id="tableContainer">
            <h2 id="titreListe">Liste des Utilisateurs</h2>    
            <table id="tableListe">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Télephone</th>
                        <th>Email</th>
                        <th>Profil</th>
                        <th>Association</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($utilisateursPage as $utilisateur) { ?> 
                    <tr>
                        <td data-label="Nom"><?php print $utilisateur['nom_utilisateur'] ?></td>
                        <td data-label="Prénom"><?php print $utilisateur['prenom_utilisateur'] ?></td>
                        <td data-label="Télephone"><?php print $utilisateur['telephone'] ?></td>
                        <td data-label="Email"><?php print $utilisateur['email'] ?></td>
                        <td data-label="Profil"><?php print $utilisateur['profil'] ?></td>
                        <td data-label="Association"><?php print $utilisateur['nom_association'] ?></td>
                        <td data-label="Action">
                        <article id="rowButton">
                        <!-- Formulaire SUPPRIMER -->    
                        <form method="POST" action="" onsubmit="return confirm('Voulez-vous vraiment supprimer cet utilisateur ?');">
                            <input class="form1" type="hidden" name="id_utilisateur" value="<?php echo $utilisateur['id_utilisateur']; ?>">
                            <button class="submitButton" id="supprimer" type="submit" name="action" value="supprimer"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        <!-- Formulaire MODIFIER -->
                            <button id="modifier" type="button" class="modifier-btn submitButton"
                                data-id="<?= $utilisateur['id_utilisateur'] ?>"
                                data-name="<?= htmlspecialchars($utilisateur['nom_utilisateur']) ?>"
                                data-firstname="<?= htmlspecialchars($utilisateur['prenom_utilisateur']) ?>"
                                data-email="<?= htmlspecialchars($utilisateur['email']) ?>"
                                data-tel="<?= htmlspecialchars($utilisateur['telephone']) ?>"
                                data-password="<?= htmlspecialchars($utilisateur['password']) ?>"
                                data-profil="<?= htmlspecialchars($utilisateur['profil']) ?>"
                                data-association-id="<?= $utilisateur['association_id'] ?>"
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
     <section id="form-modifier"  style="display: none;">
        <form id="formModifier" method="POST" action="" >
            <input type="hidden" name="id_utilisateur" id="modifier-id" value="">

            <label for="name" class="txt">Nom</label>
            <input class="form1" type="text" name="name" value="">

            <label for="firstName" class="txt">Prénom</label>
            <input class="form1" type="text" name="firstName" value="">

            <label for="tel" class="txt">Téléphone</label>
            <input class="form1" type="text" name="tel" value="">

            <label for="mail" class="txt">Email</label>
            <input class="form1" type="email" name="mail" value="">

            <label for="pwd" class="txt">Mot de passe</label>
            <input class="form1" type="password" name="pwd" value="" placeholder="Laissez vide pour ne pas changer">

            <label for="profil" class="txt">Profil</label>
            <select name="profil" id="profil" class="form1">
                <option value="Gestionnaire">Gestionnaire</option>
                <option value="Président d'association">Président d'association</option>
                <option value="Ménage">Ménage</option>
                <option value="Membre">Membre</option>
            </select>

        <label for="association_id" class="txt">Association</label>
            <select class="form1" name="association_id" id="modifier-association" required>
                <option value="" disabled selected hidden>Choisissez une association</option>
                <?php 
                
                    foreach ($associations as $association) { ?>
                <option value="<?php print $association['id_association']; ?>">
                    <?php print $association['nom_association']; ?>
                </option>
            <?php } ?>
        </select>

            <button class="submitButton" id="enregistrer" type="submit" name="action" value="enregistrer">Enregistrer les modifications</button>
        </form>
    </section>    
    </section>
    </article>
    <script src="../../public/js/adminUtilisateur.js"></script>
    <script src="https://kit.fontawesome.com/5bef22b054.js" crossorigin="anonymous"></script>

</main>
