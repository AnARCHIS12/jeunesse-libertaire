<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

function jeunesse_collaboratif_upgrade($nom_meta_base_version, $version_cible) {
    $maj = [];
    $maj['create'] = [
        ['maj_tables', ['spip_articles', 'spip_jl_validations', 'spip_jl_echanges']],
        ['jeunesse_collaboratif_creer_rubriques'],
    ];
    $maj['0.2.0'] = [
        ['maj_tables', ['spip_articles', 'spip_jl_validations', 'spip_jl_echanges']],
        ['jeunesse_collaboratif_creer_rubriques'],
    ];
    include_spip('base/upgrade');
    maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function jeunesse_collaboratif_creer_rubriques() {
    include_spip('action/editer_objet');
    foreach (['Actualités', 'Témoignages', 'Analyses', 'Ressources', 'Débats', 'Agenda'] as $titre) {
        if (!sql_getfetsel('id_rubrique', 'spip_rubriques', 'titre=' . sql_quote($titre))) {
            $id = objet_inserer('rubrique', 0);
            if ($id) objet_modifier('rubrique', $id, ['titre' => $titre]);
        }
    }
}

function jeunesse_collaboratif_vider_tables($nom_meta_base_version) {
    sql_drop_table('spip_jl_validations');
    sql_drop_table('spip_jl_echanges');
    effacer_meta($nom_meta_base_version);
}
