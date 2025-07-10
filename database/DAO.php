<?php

class DAOReservation {
private $host="localhost";
private $dbname="reservation-salles";
private $username="root";
private $password="";
private $dbh;

public function __construct() {

}

public function connexion() {
try {
$this->dbh = new PDO('mysql:host='.$this->host.';dbname='.$this->dbname, $this->username, $this->password);
 //print "Connexion réussie"; // À supprimer
} catch (PDOException $e) {
 //print $e->getMessage(); // À supprimer
 //print "Oups ! connexion échouée."; // À supprimer
throw $e; // Laisse l'exception remonter
}
}

public function deconnection() {
$this->dbh=null;
}

/** Avoir les donnees */
public function getAssociations() {
$associations = $this->dbh->prepare("SELECT * FROM associations ORDER BY nom_association");
$associations->execute();
return $associations->fetchAll(PDO::FETCH_ASSOC);
}

public function getReservationById($id_reservation) {
    $stmt = $this->dbh->prepare("SELECT * FROM reservations WHERE id = ?");
    $stmt->execute([$id_reservation]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

public function getMembresByAssociation($id_association) {
    $membres = $this->dbh->prepare("SELECT * FROM utilisateurs WHERE association_id=?");
    $membres->execute([$id_association]);
    return $membres->fetchAll(PDO::FETCH_ASSOC);
}

public function getReservation() {
    $reservations =$this->dbh->prepare("SELECT *, nom_association FROM reservations INNER JOIN associations ON associations.id_association=reservations.association_id ORDER BY date_debut, heure_debut");
    $reservations->execute();
    return $reservations->fetchAll(PDO::FETCH_ASSOC);
}

public function getReservationByAssociation($id_association) {
    $reservation = $this->dbh->prepare("SELECT * FROM reservations INNER JOIN utilisateurs ON (utilisateurs.id_utilisateur=reservations.utilisateur_id) INNER JOIN salles ON reservations.salle_id=salles.id_salle WHERE reservations.association_id = ? ORDER BY date_debut, heure_debut");
    $reservation->execute([$id_association]);
    return $reservation->fetchAll(PDO::FETCH_ASSOC);
}

public function getSalles() {
    $salles = $this->dbh->prepare("SELECT * FROM salles");
    $salles->execute();
    return $salles->fetchAll(PDO::FETCH_ASSOC);
}

public function getUtilisateurs() {
    $utilisateurs=$this->dbh->prepare("SELECT * FROM utilisateurs");
    $utilisateurs->execute();
    return $utilisateurs->fetchAll(PDO::FETCH_ASSOC);
}

public function getUtilisateursById($id_utilisateur) {
    $utilisateurs=$this->dbh->prepare("SELECT * FROM utilisateurs WHERE id_utilisateur = ?");
    $utilisateurs->execute([$id_utilisateur]);
    return $utilisateurs->fetch(PDO::FETCH_ASSOC);
}

public function UtilisateurLabelAssociation() {
    $usersLabelAssociation=$this->dbh->prepare("SELECT id_utilisateur, nom_utilisateur, prenom_utilisateur, telephone, email, profil, password, association_id, nom_association FROM `utilisateurs` INNER JOIN associations ON utilisateurs.association_id = associations.id_association ORDER BY nom_utilisateur");
    $usersLabelAssociation->execute();
    return $usersLabelAssociation->fetchAll(PDO::FETCH_ASSOC);
}

public function getMail($email) {
    $getMail = $this->dbh->prepare("SELECT * FROM utilisateurs WHERE email=:email");
    $getMail->execute([':email' => $email]);
    return $getMail->fetchAll(PDO::FETCH_ASSOC);
}

public function setMenage($id_reservation, $checked) {
    $stmt = $this->dbh->prepare("UPDATE reservations SET menage = ? WHERE id_reservation = ?");
    return $stmt->execute([$checked, $id_reservation]);
}

public function getCommentsByReservationId($reservation_id) {
    $comments = $this->dbh->prepare("SELECT commentaires.id_comment, commentaires.comment, commentaires.heure_comment, utilisateurs.nom_utilisateur FROM commentaires INNER JOIN utilisateurs ON commentaires.utilisateur_id=utilisateurs.id_utilisateur WHERE reservation_id = ? ORDER BY heure_comment DESC;");
    $comments->execute([$reservation_id]);
    return $comments->fetchAll(PDO::FETCH_ASSOC);
}

/**Ajouter les donnees */

public function NewReservation($startDate, $endDate, $startTime, $endTime, $attachments, $roomSelect, $utilisateur_id, $association_id, $recurrent = 0, $menageCheckbox = 0,  $menage = 0) {
    $newReservation = $this->dbh->prepare("INSERT INTO `reservations`(`date_debut`, `date_fin`, `heure_debut`, `heure_fin`, `pieces_jointe`, `salle_id`, `utilisateur_id`, `association_id`, `recurrent`, `menageCheckbox`, `menage`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $newReservation->execute([$startDate, $endDate, $startTime, $endTime, $attachments, $roomSelect, $utilisateur_id, $association_id, $recurrent, $menageCheckbox, $menage]);
    return $newReservation;
}


public function NewUtilisateur($name, $firstName, $tel, $mail, $pwd, $profil, $association_id) {
    $newUtilisateur = $this->dbh->prepare("INSERT INTO `utilisateurs`(`nom_utilisateur`, `prenom_utilisateur`, `telephone`, `email`, `password`, `profil`, `association_id`) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $newUtilisateur->execute([$name, $firstName, $tel, $mail, $pwd, $profil, $association_id]);
    return $newUtilisateur;
}

public function NewAssociation($name){
    $newAssociation=$this->dbh->prepare("INSERT INTO `associations`(`nom_association`) VALUES (?)");
    $newAssociation->execute([$name]);
    return $newAssociation;
}

public function NewSalle($name) {
    $newSalle = $this->dbh->prepare("INSERT INTO `salles`(`nom_salle`) VALUES (?)");
    $newSalle->execute([$name]);
    return $newSalle;
}

public function NewComment($reservation_id, $utilisateur_id, $comment) {
    $stmt = $this->dbh->prepare("INSERT INTO commentaires (reservation_id, utilisateur_id, comment, heure_comment) VALUES (?, ?, ?, NOW())");
    return $stmt->execute([$reservation_id, $utilisateur_id, $comment]);
}

/**Supprimer les données */
public function deleteUtilisateur($id_utilisateur) {
    $deletUser = $this->dbh->prepare("DELETE FROM utilisateurs WHERE id_utilisateur = ?");
    $deletUser->execute([$id_utilisateur]);
}

public function deleteSalle($id_salle) {
    $deletSalle = $this->dbh->prepare("DELETE FROM salles WHERE id_salle = ?");
    $deletSalle->execute([$id_salle]);
}

public function deleteAssociation($id_association) {
    $deletAssociation = $this->dbh->prepare("DELETE FROM associations WHERE id_association = ?");
    $deletAssociation->execute([$id_association]);
}

public function deleteReservation($id_reservation) {
    $deletReservation = $this->dbh->prepare("DELETE FROM reservations WHERE id_reservation = ?");
    $deletReservation->execute([$id_reservation]);
}

/**Modifier les données */
public function updateAssociation($id_association, $name) {
    $stmt = $this->dbh->prepare("
        UPDATE associations SET 
        nom_association = ?
        WHERE id_association = ?
    ");
    return $stmt->execute([$name, $id_association]);
}

public function updateSalle($id_salle, $name) {
    $stmt = $this->dbh->prepare("
        UPDATE salles SET 
        nom_salle = ?
        WHERE id_salle = ?
    ");
    return $stmt->execute([$name, $id_salle]);
}

public function updateUtilisateur($id_utilisateur, $name, $firstName, $mail, $tel, $profil, $association_id) {
    $stmt = $this->dbh->prepare("
        UPDATE utilisateurs SET 
        nom_utilisateur = ?, 
        prenom_utilisateur = ?, 
        email = ?, 
        telephone = ?, 
        profil = ?, 
        association_id = ?
        WHERE id_utilisateur = ?
    ");
    return $stmt->execute([$name, $firstName, $mail, $tel, $profil, $association_id, $id_utilisateur]);
}

public function updatePassword($id_utilisateur, $pwd) {
    $stmt = $this->dbh->prepare("UPDATE utilisateurs SET password = ? WHERE id_utilisateur = ?");
    return $stmt->execute([$pwd, $id_utilisateur]);
}

public function updateReservation($id_reservation, $startDate, $endDate, $startTime, $endTime, $attachments, $roomSelect, $utilisateur_id, $association_id, $recurrent, $menageCheckbox, $menage) {
    $stmt = $this->dbh->prepare("
        UPDATE reservations SET 
        date_debut = ?, 
        date_fin = ?, 
        heure_debut = ?, 
        heure_fin = ?,
        pieces_jointe = ?, 
        salle_id = ?,
        recurrent = ?,
        utilisateur_id = ?,
        association_id = ?,
        menageCheckbox = ?,
        Menage = ?
        WHERE id_reservation = ?
    ");
    return $stmt->execute([$startDate, $endDate, $startTime, $endTime, $attachments, $roomSelect, $utilisateur_id, $association_id, $recurrent, $menageCheckbox, $menage, $id_reservation]);
}
}
?>