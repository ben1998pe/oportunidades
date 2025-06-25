<?php
/**
 * Template Name: Login Page
 */
$url = home_url();

// Verifica si el usuario está logueado
if (is_user_logged_in()) {
    $current_user = wp_get_current_user();

    // Verifica si el usuario tiene el rol de "subscriber"
    if (in_array('subscriber', (array) $current_user->roles)) {

            $url = home_url("mi-cuenta");;
        // Redirige al usuario
        wp_safe_redirect($url);
        exit;
    }
}

include __DIR__ . '/header.php';

?>


<?php
$tab_active = isset($args['tab']) ? $args['tab'] : 'login';
?>
<div class="access-page">
    <div class="access-alerts"></div>
    <aside class="access-page__aside">
        <div class="access-page__row">
            <a href="<?=home_url()?>" class="icon-link--goback" style="color: #fff;"><span>regresar</span></a>
            <div class="access-page__aside--content">
                <a href="<?=home_url()?>" class="access-page__logo"><span>Turimet</span></a>
                <div class="rtabs__content">
                    <div data-id="login" class="global-tab__content<?=($tab_active=='login')?' active':''?>">
                        <p>Integra la más grande red especializada de profesionales vinculados al turismo.</p>
                    </div>
                    <div data-id="register" class="global-tab__content<?=($tab_active=='register')?' active':''?>">
                        <p>Súmate a la plataforma más importante de profesionales y organizaciones del sector turismo, y encuentra las herramientas que necesitas para alcanzar las mejores oportunidades.</p>
                    </div>
                </div>
            </div>
        </div>
    </aside>
    <div class="access-page__content">
        <div class="access-page__row">
            <a href="<?=home_url()?>" class="icon-link--goback"><span>regresar</span></a>
            <header class="rtabs__content">
                <div data-id="login" class="global-tab__content<?=($tab_active=='login')?' active':''?>">
                    <h2>¡Bienvenido/a!</h2>
                    <p>Ingresa a tu perfil profesional en Turimet</p>
                </div>
                <div data-id="register" class="global-tab__content<?=($tab_active=='register')?' active':''?>">
                    <h2>¿Eres nuevo/a?</h2>
                    <p>Ingresa tus datos para regístrarte en nuestra plataforma</p>
                </div>
            </header>
            <ul class="inline-list global-tab__list">
                <li <?=($tab_active=='login')?'class="active"':''?>><a href="#login">Iniciar sesión</a></li>
                <li <?=($tab_active=='register')?'class="active"':''?>><a href="#register">Regístrate</a></li>
            </ul>
            <div class="rtabs__content access-page__content--forms">
                <div data-id="login" class="global-tab__content<?=($tab_active=='login')?' active':''?>">
                    <form class="form" id="form-login" method="post">
                        <div class="form-row">
                            <input type="email" name="username" id="login_username" placeholder="Ingresa tu correo" />
                        </div>
                        <div class="form-row">
                            <input type="password" name="password" id="login_password" placeholder="Ingresa tu contraseña" />
                        </div>
                        <div class="form-row form-row-checkbox">
                            <label>
                                <input type="checkbox" name="remmeber" id="login_remember" />
                                <span>Recordarme</span>
                            </label>
                        </div>
                        <?php wp_nonce_field('turimet_login', '_turimet_login_nonce'); ?>
                        <input type="submit" value="Ingresar" disabled />
                        <p class="ta-center">
                            <a href="javascript:void(0);" data-action="lostpassword">¿Olvidaste tu contraseña?</a>
                        </p>
                    </form>
                </div>
                <div data-id="register" class="global-tab__content<?=($tab_active=='register')?' active':''?>">
                    <form class="form" id="form-register" method="post">
                        <div class="form-row">
                            <input type="text" name="firstname" id="register_firstname" placeholder="Nombre" />
                        </div>
                        <div class="form-row">
                            <input type="text" name="lastname" id="register_lastname" placeholder="Apellido" />
                        </div>
                        <div class="form-row">
                            <input type="email" name="email" id="register_email" placeholder="Correo electrónico" />
                        </div>
                        <div class="form-row">
                            <input type="password" name="password" id="register_password" placeholder="Crea tu contraseña" />
                        </div>
                        <div class="form-row">
                            <input type="password" name="password2" id="register_password2" placeholder="Ingresa nuevamente tu contraseña" />
                        </div>
                        <div class="form-row form-row-disclaimer">
                            <span class="text-small">Tus datos personales se utilizarán para mejorar tu experiencia en esta plataforma, gestionar accesos a tu cuenta y otros propósitos descritos en nuestras Políticas de Privacidad.</span>
                        </div>
                        <div class="form-row form-row-checkbox">
                            <label>
                                <input type="checkbox" name="acept" id="acept" required />
                                <span class="text-small">Acepto <a href="/politica-de-privacidad" target="_blank">Política de Privacidad</a>, los <a href="/terminos-y-condiciones/" target="_blank">Términos y Condiciones</a>, y las <a href="/terminos-y-condiciones-generales-de-contratacion/" target="_blank">Condiciones de Contratación.</a></span>
                            </label>
                        </div>
                        <?php wp_nonce_field('turimet_register', '_turimet_register_nonce'); ?>
                        <input type="submit" value="Registrarme" disabled />
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
include __DIR__ . '/footer-simple.php';
?>