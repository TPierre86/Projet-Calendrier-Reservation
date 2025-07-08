<?php
session_start();
session_destroy();
header('Location: /Projet-Calendrier-Reservation/controllers.php');
exit;