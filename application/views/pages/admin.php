<div id="admin_container">
    <h3>Administration</h3>

    <div>
        <h4>Gestion des utilisateurs</h4>
        <div class="buttonBar">
          <button type="button" class="addUser">Ajouter un utilisateur</button>
        </div>

        <table id="userList">
          <?php
          foreach ($users as $user) {
            $userName = $user['id'] === $_SESSION['userId'] ? '<strong>' . $user['name'] . '</strong>' : $user['name'];
            $delButton = $user['id'] === $_SESSION['userId'] ? '<td></td>' : '<td class="btnDel delUser" data-user-id="' . $user['id'] . '">Supprimer</td>';
            ?>
              <tr>
                <td><?= $userName;?></td>
                <td class="btnEdit editUser" data-user-id="<?= $user['id'] ?>">Modifier</td>
                <?= $delButton ?>
              </tr>
          <?php } ?>
        </table>
    </div>
</div>
