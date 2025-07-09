<?php 
require_once('../../database/DAO.php');
session_start();
$dao = new DAOReservation();
$dao->connexion();



  // Gérer l'association de l'utilisateur connecté
if (isset($_SESSION["association_id"])) {
    $id_association = $_SESSION["association_id"];
} else {
    echo "Aucune association connectée.";
}
$reservations =$dao->getReservationByAssociation($id_association);

// Pagination
$parPage = 5; // Nombre de lignes par page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$total = count($reservations);
$totalPages = ceil($total / $parPage);
$start = ($page - 1) * $parPage;
$reservationsPage = array_slice($reservations, $start, $parPage);

require_once('../../templates/headers.php');
?>


<main id="mainGestion">
    <article id="formGestion">
        
        <h2 id="titreliste">Liste des reservations</h2>
    <table id="tableListe">
        <thead>
            <tr>
                <th>Date de début</th>
                <th>Date de fin</th>
                <th>Heure de début</th>
                <th>Heure de fin</th>
                <th>Pièce-jointe</th>
                <th>Salle</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reservationsPage as $reservation) { ?> 
            <tr>
                <td><?php print date('d/m/Y', strtotime($reservation['date_debut'])) ?></td>
                <td><?php print date('d/m/Y', strtotime($reservation['date_fin'])) ?></td>
                <td><?php print date('H\hi', strtotime($reservation['heure_debut'])) ?></td>
                <td><?php print date('H\hi', strtotime($reservation['heure_fin'])) ?></td>
                <td><?php print $reservation['pieces_jointe'] ?></td>
                <td><?php print $reservation['nom_salle'] ?></td>
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
    </article>
</main>