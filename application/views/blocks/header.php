<html>
<head>
    <meta charset="UTF-8">
    <title>La porte du frigo virtuelle</title>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>resources/css/normalize.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>resources/css/main.css">
    <link rel="icon" type="image/png" href="<?php echo base_url(); ?>resources/favicon.ico">
</head>
<body>
      <script src="https://code.jquery.com/jquery-3.3.1.min.js"
  integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
  crossorigin="anonymous">
  </script>
  <script type="text/javascript" src="<?php echo base_url(); ?>resources/js/main.js"></script>

    <div id="banner">
        <h1>La porte du frigo virtuelle</h1>
        <?php
            $uriLast = $last = $this->uri->total_segments();
            $activePage = $this->uri->segment($uriLast);
            if ($activePage == 'login') $activePage = 'giftList';
        ?>
        <nav>
            <a class="<?php echo ($activePage == 'giftList')    ? 'active' : ''; ?>" href="<?= site_url('page/giftList'); ?>">Listes</a>
            <a class="<?php echo ($activePage == 'history')     ? 'active' : ''; ?>" href="<?= site_url('page/history'); ?>">Historique</a>
            <a class="<?php echo ($activePage == 'news')        ? 'active' : ''; ?>" href="<?= site_url('page/news'); ?>">Nouveautés</a>
            <a class="<?php echo ($activePage == 'editAccount') ? 'active' : ''; ?>" href="<?= site_url('page/editAccount'); ?>">Mon compte</a>
            <?php if (@$_SESSION['userAdmin'] == 1) : ?>
                <a class="<?php echo ($activePage == 'admin')   ? 'active' : ''; ?>" href="<?= site_url('admin'); ?>">Admin</a>
            <?php endif ?>
        </nav>
    </div>
