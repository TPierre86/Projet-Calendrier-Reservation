<?php 
require_once('../../database/DAO.php');
session_start();
$dao = new DAOReservation();
$dao->connexion();



  // Gérer l'association de l'utilisateur connecté
if (isset($_SESSION["association_id"])) {
    $association_id = $_SESSION["association_id"];
} else {
    echo "Aucune association connectée.";
}
$membres =$dao->getMembresByAssociation($association_id);
require_once('../../templates/headers.php');
?>


<main>
    <article>
    <table>
        <h2>Liste des Membres</h2>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Télephone</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($membres as $membre) { ?> 
            <tr>
                <td><?php print $membre['nom_utilisateur'] ?></td>
                <td><?php print $membre['prenom_utilisateur'] ?></td>
                <td><?php print $membre['telephone'] ?></td>
                <td><?php print $membre['email'] ?></td>
                <td><?php print $membre['association_id'] ?></td>
            </tr>
            <?php } ?>
            
        </tbody>
    </table>
    </article>
</main>