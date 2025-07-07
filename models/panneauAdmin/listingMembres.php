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

// Pagination
$parPage = 5; // Nombre de lignes par page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$total = count($membres);
$totalPages = ceil($total / $parPage);
$start = ($page - 1) * $parPage;
$membresPage = array_slice($membres, $start, $parPage);

require_once('../../templates/headers.php');
?>


<main id="mainGestion">
    <article id="formGestion">
        <h2 id="titreliste">Liste des Membres</h2>
    <table id="tableListe">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Télephone</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($membresPage as $membre) { ?> 
            <tr>
                <td><?php print $membre['nom_utilisateur'] ?></td>
                <td><?php print $membre['prenom_utilisateur'] ?></td>
                <td><?php print $membre['telephone'] ?></td>
                <td><?php print $membre['email'] ?></td>
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
    </article>
</main>