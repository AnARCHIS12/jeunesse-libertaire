<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

function jeunesse_article_par_cle($cle) {
    if (!is_string($cle) || strlen($cle) !== 48 || !ctype_xdigit($cle)) return [];
    return sql_fetsel(
        'id_article,titre,statut,date,maj,contributeur_pseudo',
        'spip_articles',
        'cle_suivi_hash=' . sql_quote(hash('sha256', $cle))
    ) ?: [];
}

function jeunesse_etat_relecture($id_article) {
    $avis = sql_allfetsel('avis,commentaire,date_avis', 'spip_jl_validations', 'id_article=' . intval($id_article), '', 'date_avis');
    $validations = count(array_filter($avis, fn($a) => $a['avis'] === 'valider'));
    $blocages = count(array_filter($avis, fn($a) => in_array($a['avis'], ['a_revoir', 'opposition'], true)));
    return ['avis' => $avis, 'validations' => $validations, 'blocages' => $blocages, 'pret' => $validations >= 2 && $blocages === 0];
}
