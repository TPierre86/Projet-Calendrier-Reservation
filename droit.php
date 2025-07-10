<?php

function getUserRole() {
    return $_SESSION['profil'] ?? 'visitor';
}


function canEdit($reservationAssociationId = null) {
    $role = getUserRole();

    if ($role === 'Gestionnaire') {
        return true;
    }

    if ($role === "Président d'association") {
        $userAssocId = $_SESSION['association_id'] ?? null;
        return $userAssocId !== null && $reservationAssociationId !== null && $userAssocId == $reservationAssociationId;
    }

    return false;
}

function canDelete($reservationAssociationId = null) {
    $role = getUserRole();

    if ($role === 'Gestionnaire') {
        return true;
    }

    if ($role === "Président d'association") {
        $userAssocId = $_SESSION['association_id'] ?? null;
        return $userAssocId !== null && $reservationAssociationId !== null && $userAssocId == $reservationAssociationId;
    }

    return false;
}


function canCreate() {
    $role = getUserRole();
    return $role === 'Gestionnaire' || $role === "Président d'association";
}

function canComment() {
    $role = getUserRole();
    if ($role === 'Gestionnaire' || $role === 'Ménage') {
        return true;
    }
    if ($role === "Président d'association" || $role === 'Membres') {
        $userAssocId = $_SESSION['association_id'] ?? null;
        return $userAssocId !== null;
    }
    return false;
}

function canDownload()  {
    $role = getUserRole();
    if ($role === 'Gestionnaire' || $role === 'Ménage' || $role === "Président d'association") {
        return true;
    }
    if ( $role === 'Membres') {
        $userAssocId = $_SESSION['association_id'] ?? null;
        return $userAssocId !== null;
    }
    return false;
}


function canView() {
    return true;
}


// 'Membres'
// 'Gestionnaire'
// 'Menage'
// "Président d'association"
