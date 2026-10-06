<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_proposer_article_charger_dist() {
    return [
        'pseudo' => '', 'email' => '', 'titre' => '', 'type_contribution' => 'actualite',
        'id_rubrique' => '', 'texte' => '', 'sources' => '', 'licence' => '',
        'website' => '', 'debut_formulaire' => time(),
        'rubriques' => sql_allfetsel('id_rubrique,titre', 'spip_rubriques', '', '', 'titre'),
    ];
}

function formulaires_proposer_article_verifier_dist() {
    include_spip('inc/filtres');
    $erreurs = [];
    $obligatoires = ['pseudo' => 'Votre pseudonyme', 'titre' => 'Le titre', 'texte' => 'Le texte', 'id_rubrique' => 'La rubrique'];
    foreach ($obligatoires as $champ => $nom) {
        if (!trim((string) _request($champ))) $erreurs[$champ] = "$nom est obligatoire.";
    }
    if (strlen(trim((string) _request('titre'))) < 5) $erreurs['titre'] = 'Le titre doit contenir au moins 5 caractères.';
    if (strlen(trim((string) _request('texte'))) < 100) $erreurs['texte'] = 'Le texte doit contenir au moins 100 caractères.';
    if (_request('email') && !email_valide(_request('email'))) $erreurs['email'] = 'Cette adresse électronique ne semble pas valide.';
    if (!_request('licence')) $erreurs['licence'] = 'Vous devez confirmer la licence et vos droits sur le contenu.';
    if (_request('website')) $erreurs['message_erreur'] = 'La proposition a été refusée.';
    if ((time() - intval(_request('debut_formulaire'))) < 4) $erreurs['message_erreur'] = 'Le formulaire a été envoyé trop rapidement.';
    $id_rubrique = intval(_request('id_rubrique'));
    if ($id_rubrique && !sql_getfetsel('id_rubrique', 'spip_rubriques', 'id_rubrique=' . $id_rubrique)) $erreurs['id_rubrique'] = 'Rubrique inconnue.';

    include_spip('inc/flock');
    $fichier = sous_repertoire(_DIR_TMP, 'jeunesse_libertaire') . 'depot-' . hash('sha256', $GLOBALS['ip'] ?? 'inconnue') . '.txt';
    $tentatives = file_exists($fichier) ? array_filter(array_map('intval', file($fichier)), fn($t) => $t > time() - 3600) : [];
    if (count($tentatives) >= 5) $erreurs['message_erreur'] = 'Trop de propositions ont été envoyées depuis cette connexion. Réessayez dans une heure.';
    return $erreurs;
}

function formulaires_proposer_article_traiter_dist() {
    include_spip('action/editer_objet');
    include_spip('inc/acces');
    $id_rubrique = intval(_request('id_rubrique'));
    $id_article = objet_inserer('article', $id_rubrique);
    if (!$id_article) return ['message_erreur' => 'La proposition n’a pas pu être enregistrée.'];

    $cle = bin2hex(random_bytes(24));
    $champs = [
        'titre' => trim(_request('titre')), 'texte' => trim(_request('texte')),
        'id_rubrique' => $id_rubrique,
        'type_contribution' => trim(_request('type_contribution')),
        'sources_contribution' => trim(_request('sources')),
        'contributeur_pseudo' => trim(_request('pseudo')),
        'contributeur_email' => trim(_request('email')),
        'licence_texte' => 'CC-BY-SA-4.0', 'cle_suivi_hash' => hash('sha256', $cle),
        'consentement_date' => date('Y-m-d H:i:s'),
    ];
    $erreur = objet_modifier('article', $id_article, $champs);
    if ($erreur) return ['message_erreur' => $erreur];
    include_spip('action/instituer_objet');
    objet_instituer('article', $id_article, ['statut' => 'prop']);

    include_spip('inc/flock');
    $fichier = sous_repertoire(_DIR_TMP, 'jeunesse_libertaire') . 'depot-' . hash('sha256', $GLOBALS['ip'] ?? 'inconnue') . '.txt';
    $lignes = file_exists($fichier) ? file($fichier, FILE_IGNORE_NEW_LINES) : [];
    $lignes[] = time();
    ecrire_fichier($fichier, implode("\n", array_slice($lignes, -5)) . "\n");

    $url = url_absolue(generer_url_public('suivi-proposition', 'cle=' . urlencode($cle)));
    return [
        'message_ok' => 'Votre texte a été transmis à la relecture collective.',
        'editable' => false,
        'url_suivi' => $url,
    ];
}
