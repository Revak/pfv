<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Nouveau cadeau ajouté</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <style>
      a {
        color: #1ac6ff;
      }
    </style>
</head>
<body style="Margin-top: 0;color: #565656;font-family: Georgia,serif;
  font-size: 16px;line-height: 25px;">
  <div style="background: #80dfff; text-align: center;">
      <h1 style="color: #fff;">
        La porte du frigo virtuelle
      </h1>
  </div>

    <p style="Margin-bottom: 25px">
      Bonjour,
    </p>
    <p style="Margin-bottom: 25px">
      <?= $userName ?> vient d'ajouter le cadeau suivant à sa liste :
      <ul>
        <li><?php echo $gift['name']; ?>
          (<a href="<?php echo $gift['url']; ?>">Lien d'exemple</a>)
        </li>
      </ul>
      Connectez-vous sur
      <a href="<?php echo $_SERVER['HTTP_HOST'] . base_url(); ?>">
        Le site</a>
         pour consulter les listes.
    </p>
</body>
</html>
