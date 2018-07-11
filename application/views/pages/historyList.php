<?php 
foreach ($gifts as $index => $giftGroup) {
    $hide = ($index != $_SESSION['userId'])?'hidden':'';
    echo '<div id="list_'.$index.'" class="'.$hide.'">'.
            '<ul>';
    foreach ($giftGroup as $gift) {
    ?>
        <li>
        <h4><?= ucfirst($gift['title']) ?></h4>
        <p><strong>Description : </strong><?= $gift['description'] ?></p>
        </li>
    <?php
    }
    echo '</ul>
    </div>';
} ?>