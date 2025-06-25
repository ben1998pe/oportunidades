<?php
/**
 * Template name: Usuario - Cambio de Contraseña
 */
// Si el usuario NO está logueado, redirige sin importar el rol
if ( !is_user_logged_in() ) {
    $url = home_url("login");
    wp_safe_redirect($url);
    exit;
}
if ( current_user_can('administrator') ) {
    wp_safe_redirect( home_url() );
    exit;
}


if ( locate_template('header-full.php') ) {
   get_header('full'); 
} else {
   get_header(); 
}

?>
<div class="turimet-account">
    <aside class="turimet-account__aside no-mobile">
        <ul class="turimet-account__menu">
        <?php include(plugin_dir_path(__FILE__) . '../template-parts/menu-cuenta.php'); ?>
        </ul>
    </aside>
    <main class="turimet-account__main">
        <div class="turimet-account__alerts"></div>
        <div class="row">
            <h2 class="c-blue">Configuración</h2>

            <form class="form2">
                <div class="ff ff-input">
                    <div class="ff__info">
                        <h3>Nueva contraseña</h3>
                        <small>Ingresa una nueva contraseña.</small>
                    </div>
                    <div class="ff__field">
                        <div class="ff__field-wrap">
                            <input type="password" name="password" maxlength="16" required />
                        </div>
                    </div>
                </div>
                <div class="ff ff-input">
                    <div class="ff__info">
                        <h3>Confirma tu nueva contraseña</h3>
                        <small>Ingresa nuevamente tu nueva contraseña.</small>
                    </div>
                    <div class="ff__field">
                        <div class="ff__field-wrap">
                            <input type="password" name="password2" maxlength="16" required />
                        </div>
                    </div>
                </div>
                <div class="ff ff-input">
                    <div class="ff__info">
                        <h3>Contraseña actual</h3>
                        <small>Ingresa tu contraseña actual.</small>
                    </div>
                    <div class="ff__field">
                        <div class="ff__field-wrap">
                            <input type="password" name="current_password" maxlength="16" required />
                        </div>
                    </div>
                </div>
                <div class="ff">
                    <button class="btn btn-primary pink" type="submit">Guardar cambios</button>
                </div>
            </form>
        </div>
    </main>
</div>
<?php include('footer-simple.php'); ?>