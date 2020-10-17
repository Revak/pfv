<?php
    echo form_open('admin/upsertUser');
    ?>
    <legend><?= $operation ?></legend>

    <label for="name">Nom</label>
    <input id="name" type="text" name="name" autocomplete="off" value="<?= $name ?>">

    <label for="email">Email</label>
    <input id="email" type="email" name="email" autocomplete="off" value="<?= $mail ?>">

    <label for="hasList">Possède une liste</label>
    <?php $checkedList = $hasList ? 'checked' : ''; ?>
    <input id="hasList" type="checkbox" name="hasList" value="1" <?= $checkedList ?>>

    <label for="allowedViews">Autorisé à voir</label>
    <input id="allowedViews" type="text" name="allowedViews" autocomplete="off" value="<?= $allowedViews ?>">

    <label for="isAdmin">Est admin</label>
    <?php $checkedAdmin = $isAdmin ? 'checked' : ''; ?>
    <input id="isAdmin" type="checkbox" name="isAdmin" value="1" <?= $checkedAdmin ?>>

    <input type="hidden" name="user_id" value="<?= $user_id ?>">

    <input type="submit" value="Enregistrer" id="validUsertForm">
</form>
