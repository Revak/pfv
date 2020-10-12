<div id="account_container">
    <h3>Mon Compte</h3>

    <div class="changeAlerts">
        <h4>Gestion des options</h4>

        <div class="flash"><?= ($this->session->flashdata('form_msg')) ? $this->session->flashdata('form_msg') : '' ?></div>
        <?php
          echo form_open('page/changeAlertSettings');
        ?>
            <label>Activer les alertes de nouveaux cadeaux</label>
            <?php $checked = ($_SESSION['userAlerts'] == 1) ? 'checked' : ''; ?>
            <label class="switch">
              <input type="checkbox" name="alerts" value="1" <?= $checked ?>>
              <span class="slider"></span>
            </label>
            <input type="hidden" name="user_id" id="user_id" value="<?= $_SESSION['userId'] ?>">
            <br>
            <input type="submit" value="Enregistrer">
        </form>
    </div>

    <div class="changePwd">
        <h4>Changer mon mot de passe</h4>
        <?php
          echo (isset($form_error)) ? '<div class="error_msg">' . $form_error . '</div>' : '';

          echo form_open('access/editPwd');
        ?>
            <label for="old_pass">Mot de passe actuel </label>
            <input type="password" name="old_pwd" id="old_pass" autocomplete="off">
            <label for="new_pass">Nouveau mot de passe </label>
            <input type="password" name="new_pwd" id="new_pass" autocomplete="off">
            <label for="confirm_pass">Confirmation du nouveau mot de passe </label>
            <input type="password" name="confirm_pwd" id="confirm_pass" autocomplete="off">
            <input type="submit" value="Valider">
        </form>
    </div>

    <a href="<?= site_url('access/logout'); ?>">Déconnexion</a>
</div>
