<?php
    echo form_open('page/upsertGift', ['class' => 'giftDetail']);
    ?>
    <legend><?= $operation ?></legend>
    <label for="name">Nom du cadeau</label>
    <input id="name" type="text" name="name" autocomplete="off" value="<?= $title ?>">
    <label for="url">Lien d'exemple</label>
    <input id="url" type="text" name="url" autocomplete="off" value="<?= $url ?>">
    <label for="description">Description</label>
    <textarea id="description" name="description" rows="5"><?= $description ?></textarea>
    <input type="hidden" name="owner_id" value="<?= $owner_id ?>">
    <input type="hidden" name="gift_id" value="<?= $gift_id ?>">
    <input type="submit" value="Enregistrer" id="validGiftForm">
</form>