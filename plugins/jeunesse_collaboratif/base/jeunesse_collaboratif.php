<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

function jeunesse_collaboratif_declarer_tables_objets_sql($tables) {
    $tables['spip_articles']['field']['licence_texte'] = "varchar(32) NOT NULL DEFAULT 'CC-BY-SA-4.0'";
    return $tables;
}
