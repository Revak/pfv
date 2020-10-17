
<div id="container">
    <aside id="blockFamily">
        <h3>Famille</h3>
        <span id="currentUser"><?= $_SESSION['userId'] ?></span>
        <ul class="family">
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
        <?php foreach ($gifts as $index => $giftGroup) {
                $hide = ($index != $_SESSION['userId'])?'hidden':'';
                echo '<div id="list_'.$index.'" class="'.$hide.'">'.
                        '<ul>';
                foreach ($giftGroup as $gift) {
                ?>
                    <li>
                        <h4>
                            <?= ucfirst($gift['title']) ?>
                            <?php if ( $gift['url'] != '') { ?>
                                <a href="<?= $gift['url'] ?>" target="outside">&#8680; Exemple</a>
                            <?php } ?>
                        </h4>
                        <p><strong>Description : </strong><?= $gift['description'] ?></p>
                        <div class="actions">
                            <?php
                            if ($gift['owner'] == $_SESSION['userId']) { ?>
                                <span class="btnEdit editGift" data-gift-id="<?= $gift['id'] ?>">Modifier</span>
                                <span class="btnDel delGift" data-gift-id="<?= $gift['id'] ?>">Supprimer</span>
                                <?php $addGiftOK = true; ?>
                            <?php } else {
                                if ($gift['reserver'] == 0) { ?>
                                    <span class="resa" data-gift-id="<?= $gift['id'] ?>">Réserver</span>
                            <?php } else { ?>
                                    <span class="reserved">Réservé par <strong><?= ucfirst($users[$gift['reserver']]['name']) ?></strong></span>
                                    <?php if ($gift['reserver'] == $_SESSION['userId']) { ?>
                                        <button class="cancel_resa" data-gift-id="<?= $gift['id'] ?>">Annuler réservation</button>
                                    <?php }
                            }
                        } ?>
                        </div>
                    </li>
                <?php
                }
                echo '</ul>
                </div>';
            } ?>

            <div class="lastAction">
                <span class="addGift" data-gift-id="<?= $_SESSION['userId'] ?>">Ajouter un cadeau</span>
            </div>
    </main>

    <aside id="blockSuggestion">
        <div>
        <h3>Suggestions pour <span><?= ucfirst($users[$_SESSION['userId']]['name']) ?></span></h3>
            <div class="contentSuggestions">
                <p>Aucune suggestion</p>
            </div>
        </div>
        <p><span class="addSuggest">Ajouter une suggestion</span></p>
    </aside>

</div>
