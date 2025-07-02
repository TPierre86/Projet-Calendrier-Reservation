<?php

class DAOReservation {
private $host="localhost";
private $dbname="reservation_salles";
private $username="gestionnaire";
private $password="admin";
private $dbh;

public function __construct() {

}

public function connection() {
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



public function deconnection() {
$this->dbh=null;
}


}

	