<?php
/**
 * Template name: Password Recovery
 */
get_header('hidden');
?>
<div class="access-page">
    <div class="access-alerts"></div>
    <aside class="access-page__aside">
        <div class="access-page__row">
            <a href="<?=home_url()?>" class="icon-link--goback"><span>regresar</span></a>
            <div class="access-page__aside--content">
                <a href="<?=home_url()?>" class="access-page__logo"><span>Turimet</span></a>
                <div class="rtabs__content">
                    <div data-id="login" class="global-tab__content active">
                        <p>Integra la más grande red especializada de profesionales vinculados al turismo.</p>
                    </div>
                </div>
            </div>
        </div>
    </aside>
    <div class="access-page__content">
        <div class="access-page__row">
            <a href="<?=home_url()?>" class="icon-link--goback"><span>regresar</span></a>
            <header class="rtabs__content">
                <div data-id="login" class="global-tab__content active">
                    <h2>Recupera tu cuenta</h2>
                    <p>Ingresa tu nueva contraseña para recuperar tu cuenta.</p>
                </div>
            </header>
            <div class="rtabs__content access-page__content--forms">
                <div data-id="login" class="global-tab__content active">
                    <form class="form" id="form-recovery" method="post">
                        <div class="form-row">
                            <input type="email" name="email" id="recovery_email" placeholder="Correo electrónico" />
                        </div>
                        <div class="form-row">
                            <input type="password" name="password" id="recovery_password" placeholder="Crea tu contraseña" />
                        </div>
                        <div class="form-row">
                            <input type="password" name="password2" id="recovery_password2" placeholder="Ingresa nuevamente tu contraseña" />
                        </div>
                        <div class="form-row">
                            <input type="text" name="code" id="recovery_code" placeholder="Ingresa el código de verificación" />
                        </div>
                        <input type="submit" value="Recuperar contraseña" disabled />
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
get_footer('hidden');