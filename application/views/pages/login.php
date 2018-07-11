
<div id="loginContainer">
    <h1>Connexion</h1>

    <?php
    echo validation_errors();

    echo (isset($form_error)) ? $form_error : '';

    echo form_open('page/login');
    ?>
        <label>Identifiant </label>
        <input type="text" name="name">
        <label>Mot de passe </label>
        <input type="password" name="password">
        <input type="submit" value="Connexion">
    </form>

    <div id="forgotten">
        <a href="">J'ai oublié mon mot de passe</a>

        <?php
        echo form_open('page/forgottenPwd');
        ?>
            <label>Adresse email </label>
            <input type="email" name="mail">
            <input type="submit" value="Envoyer">
        </form>
    </div>
</div>
