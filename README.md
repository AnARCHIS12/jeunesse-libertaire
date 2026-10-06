# Jeunesse Libertaire

Base auto-hébergeable d’un média participatif d’éducation populaire, construite sur SPIP.

## Démarrage

1. Copiez `.env.example` vers `.env` et remplacez tous les mots de passe par des secrets uniques.
2. Lancez `docker compose up -d --build`.
3. Ouvrez `http://127.0.0.1:8088` sur le serveur ou configurez votre reverse proxy vers ce port.
4. L’image configure SPIP automatiquement avec les identifiants fournis dans `.env`. Connectez-vous à `/ecrire/` avec le compte administrateur défini et changez son mot de passe après le premier accès.
5. Dans SPIP, activez le plugin « Jeunesse Libertaire — collaboration éditoriale » et créez les rubriques « Actualités », « Témoignages », « Analyses » et « Ressources ».

Le port est lié à localhost par défaut. Gardez MariaDB sur le réseau Docker privé. Le volume `spip_data` conserve les fichiers gérés par SPIP ; les squelettes et le plugin sont montés depuis le dossier du projet.

## Ce qui est prêt

- Thème public responsive (accueil, article, rubrique) rouge, noir et crème.
- Affichage de licence CC BY-SA 4.0 sur les articles et forum SPIP intégré.
- Stack Docker avec MariaDB, volumes, réseau isolé, healthcheck et redémarrage automatique.

## Ce qui demande encore un vrai travail avant ouverture publique

- Le formulaire public « proposer un texte » et ses contrôles anti-spam.
- Un espace de relecture privé et des règles collectives de validation.
- La vérification et la configuration de l’image de base exacte dans votre environnement ; la construction dépend de l’accès au registre.
- La politique de données personnelles, consentement de licence au dépôt et charte de modération.

Le champ licence ajouté par le plugin est un socle technique ; activez-le d’abord sur une instance de test et sauvegardez la base avant une mise à jour. Le thème présume que les rubriques principales portent les identifiants d’URL `actualites`, `temoignages`, `analyses`, `ressources` ; adaptez les liens si votre configuration SPIP diffère.

## Licence

Le thème, le plugin et les fichiers d’infrastructure sont publiés sous GPL-3.0-or-later. Les articles publiés sur le site sont proposés sous CC BY-SA 4.0, sous réserve de l’accord de chaque auteur·ice. Les images peuvent avoir d’autres licences.
