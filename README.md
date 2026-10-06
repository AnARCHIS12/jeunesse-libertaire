# Jeunesse Libertaire

Média participatif d’éducation populaire basé sur SPIP, livré avec son thème, son formulaire de contribution et son circuit de relecture collective.

## Démarrage

```bash
cp .env.example .env
# Remplacer les trois mots de passe et renseigner l’adresse publique du site.
docker compose up -d --build
```

Ouvrez ensuite l’adresse définie dans `SPIP_SITE_ADDRESS`. L’administration se trouve dans `/ecrire/`.

Au premier démarrage, l’image :

- installe automatiquement SPIP et le compte administrateur ;
- active le plugin `jeunesse_collaboratif` ;
- crée les tables du circuit de relecture ;
- crée les rubriques Actualités, Témoignages, Analyses, Ressources, Débats et Agenda ;
- charge le thème rouge, noir et crème.

## Parcours de contribution

1. La personne ouvre `/spip.php?page=proposer`.
2. Elle dépose son texte, accepte la CC BY-SA 4.0 et reçoit un lien secret de suivi.
3. L’article arrive avec le statut SPIP « proposé à l’évaluation ».
4. Les rédacteur·ices et administrateur·ices ouvrent **Édition → Relecture collective**.
5. Chaque personne donne un avis : validation, modifications demandées ou opposition motivée.
6. Deux validations et aucun blocage rendent le texte prêt à publier.
7. Une personne administratrice publie l’article depuis sa page SPIP.

Le lien secret permet à la personne contributrice de lire les demandes et de répondre sans créer de compte. Il n’est pas indexé et seule son empreinte cryptographique est conservée en base.

## Protections intégrées

- jeton CSRF fourni par les formulaires CVT de SPIP ;
- champ invisible contre les robots ;
- délai minimal de remplissage ;
- cinq dépôts au maximum par heure et par empreinte de connexion ;
- validation des champs côté serveur ;
- courriel facultatif et jamais affiché publiquement ;
- MariaDB inaccessible depuis l’extérieur du réseau Docker ;
- port web lié à `127.0.0.1` par défaut ;
- secrets réels exclus de Git par `.gitignore`.

## Image de base vérifiée

L’image est fixée sur `ipeos/spip:4.4.25`. La source amont publiée le 25 septembre 2026 embarque SPIP 4.4.25 sur PHP 8.4, vérifie l’archive SPIP avec le SHA-256 `99ba244ddf6a48d7d954dfc30db2cf4b84a2e481e47f5edd4b0c886673e0e281` et applique ses règles de durcissement Apache/PHP. MariaDB est fixée sur la branche LTS 11.8.

## Pages fournies

- `/spip.php?page=proposer` : dépôt d’un texte ;
- `/spip.php?page=suivi-proposition&cle=…` : suivi privé ;
- `/spip.php?page=charte` : règles de publication et de modération ;
- `/spip.php?page=confidentialite` : politique de données personnelles ;
- `/spip.php?page=mentions` : licences et mentions à compléter.

Avant l’ouverture publique, complétez dans `squelettes/mentions.html` l’identité du collectif, son adresse de contact et l’hébergeur. Ces informations dépendent de votre structure et ne peuvent pas être inventées dans l’image.

## Sauvegardes

Sauvegardez les volumes `db_data` et `spip_data`. Testez une restauration avant toute mise à jour majeure.

## Licences

Le thème, le plugin et l’infrastructure sont sous GPL-3.0-or-later. Les textes proposés sont sous CC BY-SA 4.0 avec consentement explicite. Les images et documents peuvent porter une licence distincte.
