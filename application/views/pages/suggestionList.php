<ul>
    <?php 
        foreach ($suggestions as $suggestion) {
            echo '<li><strong>'.ucfirst($users[$suggestion['author']]['name']).' ></strong> '.$suggestion['text'];
            if ($suggestion['author'] == $_SESSION['userId']) {
                echo '<span class="del_suggest" data-id="'.$suggestion['id'].'">X</span>';
            }
            echo '</li>';
        }
     ?>
</ul>