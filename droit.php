<?php

function getUserRole() {
    return $_SESSION['profil'] ?? null;
}

function canEdit() {
    $role = getUserRole();
    return in_array($role, ['Gestionnaire', "Président d'association"]);
}

function canEditAdmin() {
    $role = getUserRole();
    return in_array($role, ['Gestionnaire']);
}

function canDelete() {
    $role = getUserRole();
    return in_array($role, ['Gestionnaire', "Président d'association"]);
}

function canDeleteAdmin() {
    $role = getUserRole();
    return in_array($role, ['Gestionnaire']);
}

function canCreate() {
    $role = getUserRole();
    return in_array($role, ['Gestionnaire', "Président d'association"]);
}

function canCreateAdmin() {
    $role = getUserRole();
    return in_array($role, ['Gestionnaire']);
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
