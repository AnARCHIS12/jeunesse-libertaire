<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

use Spip\Admin\Bouton;

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

function jeunesse_collaboratif_nospam_lister_formulaires($formulaires) {
    if (!is_array($formulaires)) {
        $formulaires = [];
    }
    $formulaires[] = 'proposer_article';
    return $formulaires;
}
