<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_suivi_proposition_charger_dist($cle = '') {
    include_spip('inc/jeunesse_suivi');
    $article = jeunesse_article_par_cle($cle);
    if (!$article) return ['editable' => false, 'invalide' => true];
    $etat = jeunesse_etat_relecture($article['id_article']);
    return array_merge($article, $etat, [
        'cle' => $cle, 'message' => '',
        'echanges' => sql_allfetsel('origine,texte,date_message', 'spip_jl_echanges', 'id_article=' . intval($article['id_article']), '', 'date_message'),
    ]);
}

function formulaires_suivi_proposition_verifier_dist($cle = '') {
    include_spip('inc/jeunesse_suivi');
    if (!jeunesse_article_par_cle($cle)) return ['message_erreur' => 'Lien de suivi invalide.'];
    $message = trim((string) _request('message'));
    if (strlen($message) < 3) return ['message' => 'Votre réponse est trop courte.'];
    if (strlen($message) > 5000) return ['message' => 'Votre réponse dépasse 5 000 caractères.'];
    return [];
}

function formulaires_suivi_proposition_traiter_dist($cle = '') {
    include_spip('inc/jeunesse_suivi');
    $article = jeunesse_article_par_cle($cle);
    if (!$article) return ['message_erreur' => 'Lien de suivi invalide.'];
    sql_insertq('spip_jl_echanges', [
        'id_article' => $article['id_article'], 'origine' => 'auteur', 'id_auteur' => 0,
        'texte' => trim(_request('message')), 'date_message' => date('Y-m-d H:i:s'),
    ]);
    return ['message_ok' => 'Votre réponse a été transmise à l’équipe de relecture.'];
}
