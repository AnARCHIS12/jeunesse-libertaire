<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

function jeunesse_collaboratif_declarer_tables_objets_sql($tables) {
    $tables['spip_articles']['field']['licence_texte'] = "varchar(32) NOT NULL DEFAULT 'CC-BY-SA-4.0'";
    $tables['spip_articles']['field']['type_contribution'] = "varchar(32) NOT NULL DEFAULT 'actualite'";
    $tables['spip_articles']['field']['sources_contribution'] = "text NOT NULL DEFAULT ''";
    $tables['spip_articles']['field']['contributeur_pseudo'] = "varchar(128) NOT NULL DEFAULT ''";
    $tables['spip_articles']['field']['contributeur_email'] = "varchar(255) NOT NULL DEFAULT ''";
    $tables['spip_articles']['field']['cle_suivi_hash'] = "char(64) NOT NULL DEFAULT ''";
    $tables['spip_articles']['field']['consentement_date'] = "datetime DEFAULT NULL";
    return $tables;
}

function jeunesse_collaboratif_declarer_tables_auxiliaires($tables) {
    $tables['spip_jl_validations'] = [
        'field' => [
            'id_validation' => 'bigint(21) NOT NULL',
            'id_article' => 'bigint(21) NOT NULL DEFAULT 0',
            'id_auteur' => 'bigint(21) NOT NULL DEFAULT 0',
            'avis' => "varchar(24) NOT NULL DEFAULT 'a_revoir'",
            'commentaire' => "text NOT NULL DEFAULT ''",
            'date_avis' => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
        ],
        'key' => [
            'PRIMARY KEY' => 'id_validation',
            'UNIQUE KEY article_auteur' => 'id_article,id_auteur',
            'KEY id_article' => 'id_article',
        ],
    ];
    $tables['spip_jl_echanges'] = [
        'field' => [
            'id_echange' => 'bigint(21) NOT NULL',
            'id_article' => 'bigint(21) NOT NULL DEFAULT 0',
            'origine' => "varchar(16) NOT NULL DEFAULT 'auteur'",
            'id_auteur' => 'bigint(21) NOT NULL DEFAULT 0',
            'texte' => "text NOT NULL DEFAULT ''",
            'date_message' => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
        ],
        'key' => [
            'PRIMARY KEY' => 'id_echange',
            'KEY id_article' => 'id_article',
        ],
    ];
    return $tables;
}
