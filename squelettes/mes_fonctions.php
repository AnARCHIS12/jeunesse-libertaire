<?php
if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}

/**
 * Retourne l'id_article de l'Agora permanente pour les débats libres.
 * Si l'article n'existe pas encore, il est automatiquement créé et publié.
 *
 * @param string $dummy
 * @return int
 */
function filtre_jeunesse_id_agora_dist($dummy = '') {
    include_spip('base/abstract_sql');
    $id = sql_getfetsel('id_article', 'spip_articles', "titre LIKE '%Agora%' AND statut='publie'");
    if (!$id) {
        $id_rubrique = sql_getfetsel('id_rubrique', 'spip_rubriques', "titre LIKE '%Débat%'");
        if (!$id_rubrique) {
            $id_rubrique = sql_insertq('spip_rubriques', [
                'titre' => 'Débats',
                'statut' => 'publie',
                'descriptif' => 'Agora et discussions libres des luttes lycéennes et étudiantes.'
            ]);
        }
        $id = sql_insertq('spip_articles', [
            'id_rubrique' => $id_rubrique,
            'id_secteur' => $id_rubrique,
            'titre' => 'Agora & Tribune libre : Débats et discussions ouvertes',
            'soustitre' => 'Fil d’échange libre et permanent pour toutes les compagnes et compagnons',
            'chapo' => 'Cet espace permanent permet d’échanger, de confronter nos analyses et de débattre des luttes lycéennes et étudiantes sans être rattaché à un article thématique.',
            'texte' => "Bienvenue dans l’Agora de Jeunesse Libertaire. Cet espace d’expression directe et collective est ouvert à toutes les compagnes et compagnons de lutte.\n\nVous pouvez y ouvrir des discussions, soumettre des réflexions, partager des initiatives ou débattre des questions d’auto-organisation et d’action directe.",
            'statut' => 'publie',
            'date' => date('Y-m-d H:i:s'),
            'date_redac' => date('Y-m-d H:i:s'),
            'lang' => 'fr',
            'accepter_forum' => 'pos'
        ]);
        sql_updateq('spip_rubriques', ['statut' => 'publie'], 'id_rubrique=' . intval($id_rubrique));
    }

    // Déploiement automatique du logo arton{id}.png dans IMG/ si absent
    $logo_cible = _DIR_IMG . 'arton' . intval($id) . '.png';
    if (!file_exists($logo_cible)) {
        $source = find_in_path('agora-miniature.png') ?: find_in_path('assets/agora-miniature.png');
        if ($source && file_exists($source)) {
            @copy($source, $logo_cible);
        }
    }

    return intval($id);
}
