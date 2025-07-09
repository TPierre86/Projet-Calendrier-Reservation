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


function canCreate($reservationAssociationId = null) {
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
function canComment() {
    $role = getUserRole();
    return in_array($role, ['Membres', 'Gestionnaire', "Président d'association", 'Menage']);
}

function canView() {
    return true;
}


// 'Membres'
// 'Gestionnaire'
// 'Menage'
// "Président d'association"
