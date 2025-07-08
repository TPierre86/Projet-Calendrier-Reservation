<?php 


if ($action === 'envoyer') { //Création d'un nouveau commentaire
          $comment = ($_POST['newCommentInput']);
          $reservation_id = $_POST['reservation_id'];
          $utilisateur_id = $_SESSION['connected_user'] ?? null;
          if ($comment && $reservation_id && $utilisateur_id) {
              $dao->NewComment($reservation_id, $utilisateur_id, $comment);
          }
          // Recharge la page pour afficher le nouveau commentaire
          header("Location: ".$_SERVER['REQUEST_URI']);
          exit;
        }
?>

<!-- modal commentaires -->
          <section id="filComments" style="display: none;">
                <article id="commentsData">
                  <?php foreach($comments as $comment) {?>
                    <article class="comment">
                      <p><?= htmlspecialchars($comment['comment']) ?></p>
                      <span class="comment-date"> <?= date('d/m/Y H:i', strtotime($comment['heure_comment'])) ?></span>
                    </article>
                  <?php } ?>
                </article>
                <form id="newComment" onsubmit="return false;">
                  <input type="text" name="id_comment" value="<?= htmlspecialchars($comment['id_comment']) ?>" hidden>
                  <input type="hidden" name="reservation_id" value="<?= htmlspecialchars($reservation_id) ?>">
                  <input name="newCommentInput" type="text" id="newCommentInput" placeholder="Ajouter un commentaire..." required>
                  <button id="envoyer" name="action" value="envoyer" type="submit"><i class="fa-solid fa-paper-plane"></i></button>
                </form>
          </section>