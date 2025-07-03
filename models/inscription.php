<?php
require_once ('../database/DAO.php');
  $dao = new DAOReservation();
  $dao->connexion();
  $associations =$dao->getAssociations();
  
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  //Recuperer les données

  $users= $dao->getUtilisateurs();
  
  $name = isset($_POST['name'])?($_POST['name']):"";
  $firstName = isset($_POST['firstName'])?($_POST['firstName']):"";
  $tel = isset($_POST['tel'])?($_POST['tel']):"";
  $mail = isset($_POST['mail'])?($_POST['mail']):"";
  $pwd = isset($_POST['pwd']) ? password_hash($_POST['pwd'], PASSWORD_DEFAULT) : "";
  $profil = isset($_POST['profil'])?($_POST['profil']):"";
  $association_id = isset($_POST['association_id'])?($_POST['association_id']):"";
  $userExists = false;
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
            header("Location: refresh.php");
            exit;
        } else {
            echo "Erreur lors de l'inscription.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Inscription</title>
    <link rel="stylesheet" href="/Projet-Calendrier-Reservation/public/styles/components/inscription.css" />
  </head>
  <body id="bodyFormInscription">
    <article id="formInscription">
      <h1 id="titreFormInscription">Inscription</h1>
      <form method="POST" id="formulaireInscription" action="">
        <label for="name" class="txt">Nom</label>
        <input type="text" id="name" name="name" class="inputForm" required />
        <label for="firstName" class="txt">Prénom</label>
        <input
          type="text"
          id="firstName"
          name="firstName"
          class="inputForm"
          required
        />
        <label for="tel" class="txt">n° Téléphone (format: 0615251168)</label>
        <input type="text" id="tel" name="tel" class="inputForm" pattern="0[67][0-9]{8}" required />
        <label for="mail" class="txt">Adresse e-mail</label>
        <input type="text" id="mail" name="mail" class="inputForm" required />
        <label for="pwd" class="txt">Mots de passe</label>
        <input type="password" id="pwd" name="pwd" class="inputForm" required />
        <input type="hidden" name="profil" class="inputForm" value="Membres">
        <label for="association" class="txt">Associations</label>
        <select name="association_id" id="selectAssociation">
          <option value="" disabled selected hidden>
            Choisissez une association
          </option>
          <?php foreach($associations as $association) { ?>
            <option value="<?php print $association['id_association']; ?>"><?php print $association['nom_association']; ?></option>
          <?php } ?>
        </select>
        <button type="submit" id="submitButton">
          <span class="circle1"></span>
          <span class="circle2"></span>
          <span class="circle3"></span>
          <span class="circle4"></span>
          <span class="circle5"></span>
          <span class="text">S'incrire</span>
        </button>
      </form>
    </article>
  </body>
</html>
