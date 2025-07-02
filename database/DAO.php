<?php
var_dump($_GET);
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
//print "Connexion réussie";
} catch (PDOException $e) {
print $e->getMessage();
// tenter de réessayer la connexion après un certain délai, par exemple
print "Oups ! connexion échouée.";
}
}

public function getAssociations() {
$associations = $this->dbh->prepare("SELECT * FROM associations ORDER BY nom_association");
$associations->execute();
return $associations;
}

public function getMembresByAssociation($id_association) {
    $membres = $this->dbh->prepare("SELECT * FROM utilisateurs WHERE association_id=".$id_association);
    $membres->execute();
    return $membres;
}

public function getReservation() {
    $reservations =$this->dbh->prepare("SELECT * FROM reservations ORDER BY date_debut, heure_debut");
    $reservations->execute();
    return $reservations;
}

public function getReservationByAssociation($id_association) {
    $reservation = $this->bdh->prepare("SELECT * FROM reservations INNER JOIN utilisateurs ON (utilisateurs.id_utilisateur=reservations.utilisateur_id) WHERE association_id=".$id_association." ORDER BY date_debut, heure_debut");
    $reservation->execute();
    return $reservation;
}

public function getSalles() {
    $salles= $this->dbh->prepare("SELECT * FROM salles");
    $salles->execute();
    return $salles;
}

public function getUtilisateurs() {
    $utilisateurs=$this->dbh->prepare("SELECT * FROM utilisateurs");
    $utilisateurs->execute();
    return $utilisateurs;
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
public function deconnection() {
$this->dbh=null;
}


public function getMail($email) {
    $getMail = $this->dbh->prepare("SELECT * FROM utilisateurs WHERE email=:email");
    $getMail->execute(['email' => $email]);
    return $getMail->fetch(PDO::FETCH_ASSOC);
}

}

?>