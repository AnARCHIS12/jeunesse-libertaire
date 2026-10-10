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

/**
 * Filtre résilient pour l'affichage du logo ou miniature d'une carte d'article
 * Garantit qu'un article d'Agora ou de Bienvenue affiche toujours son illustration,
 * même si le logo SPIP n'a pas encore été synchronisé dans IMG/arton{id}.png.
 *
 * @param string $logo Tag img calculé par SPIP (peut être vide)
 * @param string $titre Titre de l'article
 * @param int $id_article Identifiant de l'article
 * @return string
 */
function filtre_jeunesse_card_logo_dist($logo = '', $titre = '', $id_article = 0) {
    if (!empty(trim($logo))) {
        return $logo;
    }

    include_spip('inc/filtres');

    // Article Agora / Tribune libre
    if (stripos($titre, 'Agora') !== false || stripos($titre, 'Tribune') !== false || stripos($titre, 'Débat') !== false) {
        $src = find_in_path('agora-miniature.png') ?: 'squelettes/agora-miniature.png';
        if ($id_article && !file_exists(_DIR_IMG . 'arton' . intval($id_article) . '.png') && file_exists($src)) {
            @copy($src, _DIR_IMG . 'arton' . intval($id_article) . '.png');
        }
        return '<img src="' . $src . '" alt="' . attribut_html($titre) . '" width="640" height="360" loading="lazy" />';
    }

    // Article Bienvenue / Lancement
    if (stripos($titre, 'Bienvenue') !== false) {
        $src = find_in_path('bienvenue-miniature.png') ?: 'squelettes/bienvenue-miniature.png';
        if ($id_article && !file_exists(_DIR_IMG . 'arton' . intval($id_article) . '.png') && file_exists($src)) {
            @copy($src, _DIR_IMG . 'arton' . intval($id_article) . '.png');
        }
        return '<img src="' . $src . '" alt="' . attribut_html($titre) . '" width="640" height="360" loading="lazy" />';
    }

    return '<span class="card-symbol">✳</span>';
}

/**
 * Filtre pour l'image d'en-tête de l'article complet (sans fallback d'astérisque)
 */
function filtre_jeunesse_hero_logo_dist($logo = '', $titre = '', $id_article = 0) {
    if (!empty(trim($logo))) {
        return '<div class="article-hero-image">' . $logo . '</div>';
    }

    include_spip('inc/filtres');

    if (stripos($titre, 'Agora') !== false || stripos($titre, 'Tribune') !== false || stripos($titre, 'Débat') !== false) {
        $src = find_in_path('agora-miniature.png') ?: 'squelettes/agora-miniature.png';
        if ($id_article && !file_exists(_DIR_IMG . 'arton' . intval($id_article) . '.png') && file_exists($src)) {
            @copy($src, _DIR_IMG . 'arton' . intval($id_article) . '.png');
        }
        return '<div class="article-hero-image"><img src="' . $src . '" alt="' . attribut_html($titre) . '" width="1000" loading="lazy" /></div>';
    }

    if (stripos($titre, 'Bienvenue') !== false) {
        $src = find_in_path('bienvenue-miniature.png') ?: 'squelettes/bienvenue-miniature.png';
        if ($id_article && !file_exists(_DIR_IMG . 'arton' . intval($id_article) . '.png') && file_exists($src)) {
            @copy($src, _DIR_IMG . 'arton' . intval($id_article) . '.png');
        }
        return '<div class="article-hero-image"><img src="' . $src . '" alt="' . attribut_html($titre) . '" width="1000" loading="lazy" /></div>';
    }

    return '';
}

/**
 * Retourne l'URL absolue de l'image de partage (Open Graph / Twitter Card) pour un article.
 * Recherche en priorité le logo propre de l'article, puis l'illustration de fallback (Agora, Bienvenue),
 * et enfin le logo général du site.
 *
 * @param int $id_article Identifiant de l'article
 * @param string $titre Titre de l'article
 * @return string URL absolue complète
 */
function filtre_jeunesse_og_image_article_dist($id_article = 0, $titre = '') {
    include_spip('inc/filtres');
    include_spip('public/quete');

    $id_article = intval($id_article);

    // 1. Logo propre de l'article
    if ($id_article > 0) {
        $logo = quete_logo('id_article', 'ON', $id_article, 0, false);
        if (is_array($logo) && !empty($logo['src'])) {
            return url_absolue($logo['src']);
        }
        if (file_exists(_DIR_IMG . 'arton' . $id_article . '.png')) {
            return url_absolue(_DIR_IMG . 'arton' . $id_article . '.png');
        }
    }

    // 2. Détection par thématique / titre (Agora, Débat, Tribune, Bienvenue)
    if (stripos($titre, 'Agora') !== false || stripos($titre, 'Tribune') !== false || stripos($titre, 'Débat') !== false) {
        $src = find_in_path('agora-miniature.png') ?: 'squelettes/agora-miniature.png';
        if ($id_article && !file_exists(_DIR_IMG . 'arton' . $id_article . '.png') && file_exists($src)) {
            @copy($src, _DIR_IMG . 'arton' . $id_article . '.png');
        }
        return url_absolue($src);
    }

    if (stripos($titre, 'Bienvenue') !== false) {
        $src = find_in_path('bienvenue-miniature.png') ?: 'squelettes/bienvenue-miniature.png';
        if ($id_article && !file_exists(_DIR_IMG . 'arton' . $id_article . '.png') && file_exists($src)) {
            @copy($src, _DIR_IMG . 'arton' . $id_article . '.png');
        }
        return url_absolue($src);
    }

    // 3. Repli sur le logo du site
    return filtre_jeunesse_og_image_site_dist();
}

/**
 * Retourne l'URL absolue du logo de site officiel pour les métadonnées de partage.
 *
 * @return string URL absolue complète
 */
function filtre_jeunesse_og_image_site_dist() {
    include_spip('inc/filtres');
    include_spip('public/quete');

    // Logo du site dans IMG (siteon0.png)
    if (file_exists(_DIR_IMG . 'siteon0.png')) {
        return url_absolue(_DIR_IMG . 'siteon0.png');
    }

    // Logo dans squelettes/assets ou assets
    $path = find_in_path('assets/avatar-reseaux-noir.png') ?: (find_in_path('avatar-reseaux-noir.png') ?: 'squelettes/assets/avatar-reseaux-noir.png');
    if (file_exists($path) && !file_exists(_DIR_IMG . 'siteon0.png')) {
        @copy($path, _DIR_IMG . 'siteon0.png');
    }

    return url_absolue($path);
}


