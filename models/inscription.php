<?php

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Inscription</title>
    <link rel="stylesheet" href="inscription.css" />
  </head>
  <body>
    <article>
      <h1>Inscription</h1>
      <form method="post" class="form">
        <label for="name" class="txt">Nom</label>
        <input type="text" id="name" name="name" class="form1" required />
        <label for="first-name" class="txt">Prénom</label>
        <input
          type="text"
          id="first-name"
          name="first-name"
          class="form1"
          required
        />
        <label for="tel" class="txt">n° Téléphone (format: 0615251168)</label>
        <input type="text" id="tel" name="tel" class="form1" required />
        <label for="mail" class="txt">Adresse e-mail</label>
        <input type="text" id="mail" name="mail" class="form1" required />
        <label for="pwd" class="txt">Mots de passe</label>
        <input type="password" id="pwd" name="pwd" class="form1" required />
        <label for="association" class="txt">Associations</label>
        <select>
          <option value="" disabled selected hidden>
            Choisissez une association
          </option>
          <option>Les Rives du Cérou</option>
          <option>Le Cercle du Vieux Bourg</option>
          <option>La Clé des Remparts</option>
          <option>L’Union du Pays Monestiéen</option>
          <option>Les Collines d’Occitanie</option>
          <option>Les Voix du Tarn</option>
          <option>La Maison des Quatre Saisons</option>
          <option>Le Foyer de la Place Haute</option>
          <option>Esprit de Village</option>
        </select>
        <button>
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
