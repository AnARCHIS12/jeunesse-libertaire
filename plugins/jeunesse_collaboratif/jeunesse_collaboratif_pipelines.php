<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

function jeunesse_collaboratif_ajouter_menus($menus) {
    if (isset($menus['menu_edition'])) {
        $menus['menu_edition']->sousmenu['jeunesse_relecture'] = new Bouton(
            'article-24',
            'Relecture collective',
            generer_url_ecrire('jeunesse_relecture')
        );
    }
    return $menus;
}
