<?php $current_url = $_SERVER['REQUEST_URI']; ?>
<li id="menu-item-360" class="menu-item menu-item-type-post_type menu-item-object-page <?php if (strpos($current_url, '/mi-cuenta/mis-postulaciones/') !== false) echo ' current-menu-item current_page_item'; ?>">
    <a href="/mi-cuenta/mis-postulaciones/">
        <figure class="rnz-menu__icon">
            <img src="https://turimet.s3.amazonaws.com/uploads/2023/05/Concept-1683385446-632308121-1683385446-1931401980.svg" alt="Mis postulaciones">
        </figure>
        Mis postulaciones
    </a>
</li>

<li id="menu-item-354" class="menu-item menu-item-type-post_type menu-item-object-page <?php if (strpos($current_url, '/mi-cuenta/') !== false && $current_url === '/mi-cuenta/') echo ' current-menu-item current_page_item'; ?>">
    <a href="/mi-cuenta/">
        <figure class="rnz-menu__icon">
            <img src="https://turimet.s3.amazonaws.com/uploads/2023/05/User-1683385445-916862630-1683385445-1430137259.svg" alt="Mi perfil">
        </figure>
        Mi perfil
    </a>
</li>

<li id="menu-item-514" class="menu-item menu-item-type-custom menu-item-object-custom">
    <a target="_blank" href="https://accounts.google.com/v3/signin/identifier?dsh=S201380947%3A1683061892554648&amp;continue=https%3A%2F%2Fclassroom.google.com&amp;ifkv=Af_xneGCwZR-cn_i84L4lXYyJQHsJYqN27wmCCljgkGpjaQVEBsDFx8-1mvk7NRu460HZnsRk9hBAg&amp;passive=true&amp;flowName=GlifWebSignIn&amp;flowEntry=ServiceLogin">
        <figure class="rnz-menu__icon">
            <img src="https://turimet.s3.amazonaws.com/uploads/2023/05/Concept-1-1683385447-1057597920-1683385447-200092700.svg" alt="Mi aula">
        </figure>
        Mi aula
    </a>
</li>

<li id="menu-item-583" class="menu-item menu-item-type-post_type menu-item-object-page <?php if (strpos($current_url, '/mi-cuenta/configuracion/') !== false) echo ' current-menu-item current_page_item'; ?>">
    <a href="/mi-cuenta/configuracion/">
        <figure class="rnz-menu__icon">
            <img src="https://turimet.s3.amazonaws.com/uploads/2023/05/Sistem-1683385444-365393960-1683385444-2147155568.svg" alt="Configuración">
        </figure>
        Configuración
    </a>
</li>
