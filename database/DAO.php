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
// print "Connexion réussie"; // À supprimer
} catch (PDOException $e) {
// print $e->getMessage(); // À supprimer
// print "Oups ! connexion échouée."; // À supprimer
throw $e; // Laisse l'exception remonter
}
}

public function getAssociations() {
$associations = $this->dbh->prepare("SELECT * FROM associations ORDER BY nom_association");
$associations->execute();
return $associations->fetchAll(PDO::FETCH_ASSOC);
}

public function getMembresByAssociation($id_association) {
    $membres = $this->dbh->prepare("SELECT * FROM utilisateurs WHERE association_id=".$id_association);
    $membres->execute();
    return $membres->fetchAll(PDO::FETCH_ASSOC);
}

public function getReservation() {
    $reservations =$this->dbh->prepare("SELECT * FROM reservations ORDER BY date_debut, heure_debut");
    $reservations->execute();
    return $reservations->fetchAll(PDO::FETCH_ASSOC);
}

public function getReservationByAssociation($id_association) {
    $reservation = $this->dbh->prepare("SELECT * FROM reservations INNER JOIN utilisateurs ON (utilisateurs.id_utilisateur=reservations.utilisateur_id) WHERE association_id=".$id_association." ORDER BY date_debut, heure_debut");
    $reservation->execute();
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
    return $usersLabelAssociation;
}

public function NewReservation($startDate,$endDate,$startTime,$endTime,$commentInput,$attachments,$roomSelect,$utilisateur_id) {
    $newReservation=$this->dbh->prepare("INSERT INTO `reservations`(`date_debut`, `date_fin`, `heure_debut`, `heure_fin`, `commentaire`, `pieces_jointe`, `salle_id`, `utilisateur_id`) VALUES ('".$startDate."','".$endDate."','".$startTime."','".$endTime."','".$commentInput."','".$attachments."','".$roomSelect."','".$utilisateur_id."')");
    $newReservation->execute();
    return $newReservation;
}

public function NewUtilisateur($name,$firstName,$tel,$mail,$pwd,$profil,$association_id){
    $newUtilisateur = $this->dbh->prepare("INSERT INTO `utilisateurs`(`nom_utilisateur`, `prenom_utilisateur`, `telephone`, `email`, `password`, `profil`, `association_id`) VALUES ('".$name."','".$firstName."','".$tel."','".$mail."','".$pwd."','".$profil."','".$association_id."')");
    $newUtilisateur->execute();
    return $newUtilisateur;
}

public function NewAssociation($nom_association){
    $newAssociation=$this->dbh->prepare("INSERT INTO `associations`(`nom_association`) VALUES ('".$nom_association."')");
    $newAssociation->execute();
    return $newAssociation;
}

public function NewSalle($name) {
    $newSalle = $this->dbh->prepare("INSERT INTO `salles`(`nom_salle`) VALUES (?)");
    $newSalle->execute([$name]);
    return $newSalle;
}

public function deconnection() {
$this->dbh=null;
}


public function getMail($email) {
    $getMail = $this->dbh->prepare("SELECT id_utilisateur, email, password, prenom_utilisateur, profil FROM utilisateurs WHERE email=:email");
    $getMail->execute([':email' => $email]);
    return $getMail->fetch(PDO::FETCH_ASSOC);
}

public function deleteUtilisateur($id_utilisateur) {
    $deletUser = $this->dbh->prepare("DELETE FROM utilisateurs WHERE id_utilisateur = ?");
    $deletUser->execute([$id_utilisateur]);
}

public function deleteSalle($id_salle) {
    $deletSalle = $this->dbh->prepare("DELETE FROM salles WHERE id_salle = ?");
    $deletSalle->execute([$id_salle]);
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

}?>