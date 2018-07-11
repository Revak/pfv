<?php
    echo form_open('page/addSuggestion', ['class' => 'suggestion_detail']);
    ?>
    <legend>Ajout</legend>
    <label for="suggestionText">Suggestion</label>
    <textarea id="suggestionText" name="suggestionText" rows="5"></textarea>
    <input type="hidden" id="suggestionTarget" name="target_id" value="<?= $target ?>">
    <input type="hidden" id="suggestionAuthor"name="author_id" value="<?= $author ?>">
    <input type="submit" value="Enregistrer" id="validSuggestionForm">
</form>