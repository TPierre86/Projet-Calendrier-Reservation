<?php 
require_once('../../database/DAO.php');
session_start();
$dao = new DAOReservation();
$dao->connexion();

$utilisateurs =$dao->UtilisateurLabelAssociation();
// activer les boutons spprimer et modifier
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $id_utilisateur = $_POST['id_utilisateur'];
    $action = $_POST['action'];

    if ($action === 'supprimer') {
        $dao->deleteUtilisateur($id_utilisateur);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

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
    <link rel="stylesheet" href="/Projet-Calendrier-Reservation/public/styles/components/inscription.css" />
  </head>

<main>
    <article>
        <h1>Gérer les utilisateurs</h1>
        <button id="nouveau">Nouveau Utilisateur</button>
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
                <form id="supprimer" method="POST" action="" onsubmit="return confirm('Voulez-vous vraiment supprimer cet utilisateur ?');">
                    <input type="hidden" name="id_utilisateur" value="<?php echo $utilisateur['id_utilisateur']; ?>">
                    <button type="submit" name="action" value="supprimer">Supprimer</button>
                    </form>
                <!-- Formulaire MODIFIER -->
                <form id="modifier" method="POST" action="" onsubmit="return confirm('Voulez-vous vraiment modifier cet utilisateur ?');">
                    <input type="hidden" name="id_utilisateur" value="<?php echo $utilisateur['id_utilisateur']; ?>">
                    <button type="submit" name="action" value="modifier">Modifier</button>
                    </form>
                </td>
            </tr>
            <?php } ?>
            
        </tbody>
    </table>
    </article>
</main>