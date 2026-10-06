<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_avis_relecture_charger_dist($id_article) {
    $id_auteur = intval($GLOBALS['visiteur_session']['id_auteur'] ?? 0);
    $existant = sql_fetsel('avis,commentaire', 'spip_jl_validations', 'id_article=' . intval($id_article) . ' AND id_auteur=' . $id_auteur);
    return ['id_article' => intval($id_article), 'avis' => $existant['avis'] ?? '', 'commentaire' => $existant['commentaire'] ?? ''];
}

function formulaires_avis_relecture_verifier_dist($id_article) {
    $id_auteur = intval($GLOBALS['visiteur_session']['id_auteur'] ?? 0);
    if (!$id_auteur || !in_array($GLOBALS['visiteur_session']['statut'] ?? '', ['0minirezo', '1comite'], true)) return ['message_erreur' => 'Accès réservé à l’équipe éditoriale.'];
    if (!in_array(_request('avis'), ['valider', 'a_revoir', 'opposition'], true)) return ['avis' => 'Choisissez un avis.'];
    if (_request('avis') !== 'valider' && strlen(trim((string) _request('commentaire'))) < 5) return ['commentaire' => 'Expliquez la modification demandée ou l’opposition.'];
    return [];
}

function formulaires_avis_relecture_traiter_dist($id_article) {
    $id_auteur = intval($GLOBALS['visiteur_session']['id_auteur']);
    $where = 'id_article=' . intval($id_article) . ' AND id_auteur=' . $id_auteur;
    $donnees = ['avis' => _request('avis'), 'commentaire' => trim(_request('commentaire')), 'date_avis' => date('Y-m-d H:i:s')];
    if (sql_countsel('spip_jl_validations', $where)) sql_updateq('spip_jl_validations', $donnees, $where);
    else sql_insertq('spip_jl_validations', array_merge($donnees, ['id_article' => intval($id_article), 'id_auteur' => $id_auteur]));
    if ($donnees['commentaire']) sql_insertq('spip_jl_echanges', ['id_article' => intval($id_article), 'origine' => 'equipe', 'id_auteur' => $id_auteur, 'texte' => $donnees['commentaire'], 'date_message' => date('Y-m-d H:i:s')]);
    return ['message_ok' => 'Votre avis a été enregistré.'];
}
