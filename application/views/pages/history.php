
<div id="container">
    <aside class="history_menu">
        <h3>Choisir une année</h3>
        <select name="year_choice">
            <?php foreach ($years as $past) {
                echo '<option value="'.$past->year.'">'.$past->year.'</option>';
            } ?>
        </select>
        <h3>Choisir une personne</h3>
        <ul class="family histo">
            <?php foreach ($users as $user) {
              if ($user['hasList'])
              {
                // user view restrictions
                if (!$_SESSION['userViews'] || (in_array(strtolower($user['name']), $_SESSION['userViews'], false)) || $user['id'] === $_SESSION['userId'])
                {
                  $class = ($user['id'] == $_SESSION['userId']) ? 'selectedPerson' : '';
                  echo '<li id="fam'.$user['id'].'" class="'.$class.'">'.$user['name'].'</li>';
                }
              }
            } ?>
        </ul>
    </aside>

    <main>
        <h3>Liste de <span><?= ucfirst($users[$_SESSION['userId']]['name']) ?></span></h3>
        <div class="giftList">
            <?php $this->view('pages/historyList') ?>
        </div>
    </main>
</div>
