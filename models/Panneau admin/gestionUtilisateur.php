<?php 
require_once('../../database/DAO.php');
session_start();
$dao = new DAOReservation();
$dao->connexion();

$utilisateurs =$dao->UtilisateurLabelAssociation();
$associations =$dao->getAssociations();
// activer les boutons spprimer et modifier
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    $action = $_POST['action'];

    

/**
 * ! Création d'un nouveau Utilisateur
 */
    if ($action === 'creer') {
        //Recuperer les données
        
        $users= $dao->getUtilisateurs();

            $name = isset($_POST['name'])?($_POST['name']):"";
            $firstName = isset($_POST['firstName'])?($_POST['firstName']):"";
            $tel = isset($_POST['tel'])?($_POST['tel']):"";
            $mail = isset($_POST['mail'])?($_POST['mail']):"";
            $pwd = isset($_POST['pwd']) ? password_hash($_POST['pwd'], PASSWORD_DEFAULT) : "";
            $profil = isset($_POST['profil'])?($_POST['profil']):"";
            $association_id = isset($_POST['association_id'])?($_POST['association_id']):"";
    
         /*Comparer les données pour ne pas crée de news users doublons*/
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
    if ($action === 'modifier') {
        // Redirection ou traitement du formulaire de modification
        header("Location: modifier_utilisateur.php?id=$id_utilisateur");
        exit;
    }
}



?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>gestionUtilisateur</title>
    <!-- <link rel="stylesheet" href="/Projet-Calendrier-Reservation/public/styles/components/inscription.css" /> -->
  </head>

<main>
    <article>
        <h1>Gérer les utilisateurs</h1>
        <button id="nouveau">Nouveau Utilisateur</button>
            <section id="form-nouveau" style="display: none;">
                <form method="POST" action="">
                    <label for="name" class="txt">Nom</label>
                    <input type="text" id="name" name="name" class="form1" required />
        
                    <label for="firstName" class="txt">Prénom</label>
                    <input type="text" id="firstName" name="firstName" class="form1" required/>
                    
                    <label for="tel" class="txt">n° Téléphone (format: 0615251168)</label>
                    <input type="text" id="tel" name="tel" class="form1" pattern="0[67][0-9]{8}" required />
                    
                    <label for="mail" class="txt">Adresse e-mail</label>
                    <input type="text" id="mail" name="mail" class="form1" required />
                    
                    <label for="pwd" class="txt">Mots de passe</label>
                    <input type="password" id="pwd" name="pwd" class="form1" required />

                    <label for="profil" class="txt">Profil</label>
                    <select name="profil" id="profil">
                        <option value="Gestionnaire">Gestionnaire</option>
                        <option value="Président d'association">Président d'association</option>
                        <option value="Ménage">Ménage</option>
                        <option value="Membre">Membre</option>
                    </select>
                    
                    <label for="association" class="txt">Associations</label>
                    <select name="association_id">
                        <option value="" disabled selected hidden>Choisissez une association</option>
                        <?php foreach($associations as $association) { ?>
                        <option value="<?php print $association['id_association']; ?>"><?php print $association['nom_association']; ?></option>
                        <?php } ?>
                    </select>

                    <button type="submit" name="action" value="creer">Créer</button>
                </form>
            </section>
    <table>
        <h2>Liste des Utilisateurs</h2>
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
            <?php foreach ($utilisateurs as $utilisateur) { ?> 
            <tr>
                <td><?php print $utilisateur['nom_utilisateur'] ?></td>
                <td><?php print $utilisateur['prenom_utilisateur'] ?></td>
                <td><?php print $utilisateur['telephone'] ?></td>
                <td><?php print $utilisateur['email'] ?></td>
                <td><?php print $utilisateur['profil'] ?></td>
                <td><?php print $utilisateur['nom_association'] ?></td>
                <td>
                <!-- Formulaire SUPPRIMER -->    
                <form method="POST" action="" onsubmit="return confirm('Voulez-vous vraiment supprimer cet utilisateur ?');">
                    <input type="hidden" name="id_utilisateur" value="<?php echo $utilisateur['id_utilisateur']; ?>">
                    <button type="submit" name="action" value="supprimer">Supprimer</button>
                    </form>
                <!-- Formulaire MODIFIER -->
                <form method="POST" action="" onsubmit="return confirm('Voulez-vous vraiment modifier cet utilisateur ?');">
                    <input type="hidden" name="id_utilisateur" value="<?php echo $utilisateur['id_utilisateur']; ?>">
                    <button type="submit" name="action" value="modifier">Modifier</button>
                    </form>
                </td>
            </tr>
            <?php } ?>
            
        </tbody>
    </table>
    </article>
    <script src="../../public/js/adminUtilisateur.js"></script>
</main>