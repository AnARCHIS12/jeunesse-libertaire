<p align="center">
  <img src="assets/avatar-reseaux-noir.png" alt="Jeunesse Libertaire" width="120" height="120" />
</p>

<h1 align="center">JEUNESSE LIBERTAIRE</h1>

<p align="center">
  <strong>Média participatif d’émancipation collective, d’éducation populaire et de combat des luttes lycéennes et étudiantes.</strong>
</p>

<p align="center">
  <a href="README.md">Esperanto</a> •
  <a href="README.fr.md"><b>Français</b></a> •
  <a href="README.en.md">English</a> •
  <a href="README.es.md">Español</a>
</p>

<p align="center">
  <a href="https://github.com/AnARCHIS12/jeunesse-libertaire"><img src="https://img.shields.io/badge/statut-actif-10b981?style=flat-square" alt="Statut" /></a>
  <a href="https://www.spip.net"><img src="https://img.shields.io/badge/SPIP-4.4.28-c92a2a?style=flat-square" alt="SPIP Version" /></a>
  <a href="https://www.php.net"><img src="https://img.shields.io/badge/PHP-8.4-4f5b93?style=flat-square" alt="PHP Version" /></a>
  <a href="https://mariadb.org"><img src="https://img.shields.io/badge/MariaDB-11.8_LTS-003545?style=flat-square" alt="MariaDB Version" /></a>
  <a href="https://contrib.spip.net/NoSPAM"><img src="https://img.shields.io/badge/sécurité-NoSpam_3.0.1-10b981?style=flat-square" alt="Sécurité NoSpam" /></a>
  <a href="LICENSE"><img src="https://img.shields.io/badge/code-GPL--3.0--or--later-blue?style=flat-square" alt="Licence Code" /></a>
  <a href="https://creativecommons.org/licenses/by-sa/4.0/"><img src="https://img.shields.io/badge/contenu-CC--BY--SA--4.0-lightgrey?style=flat-square" alt="Licence Contenu" /></a>
</p>

---

## Vue d'ensemble

**Jeunesse Libertaire** est une plateforme multimédia autonome conçue pour et par la jeunesse en lutte. Bâtie sur le socle libre SPIP 4.4 dans un environnement conteneurisé ultra-sécurisé, elle réunit un formulaire public de proposition de publications, un espace d'Agora et de débat direct ouvert aux compagnes et compagnons, un circuit de relecture collective sans création obligatoire de compte, et une protection anti-spam avancée sans aucun service tiers ni traçage commercial.

- **Autonomie intégrale & Zéro CDN** : zéro appel vers des serveurs propriétaires (Google, Cloudflare, etc.). Tous les scripts, styles et images vectorielles sont auto-hébergés et fonctionnent en réseau fermé ou hors-ligne.
- **Sobriété & Esthétique brute** : charte graphique soignée inspirée de la presse d'avant-garde libertaire (noir profond `#0f0f10`, papier chaud `#f4efe8`, rouge d'impact `#d32920`).
- **Émancipation collective** : outils horizontaux garantissant l'anonymat, la liberté de publication et l'absence de toute hiérarchie bureaucratique.

---

## Fonctionnalités principales

| Module | Description | Sécurité & Éthique |
| :--- | :--- | :--- |
| **Proposition directe** | Dépose de textes, témoignages, analyses de grèves et comptes-rendus d'AG. | Vérification anti-bot en 4 secondes, honeypot furtif, limiteur d'IP chiffré en SHA-256. |
| **Suivi secret** | Lien de suivi privé remis à l'auteur·ice pour dialoguer avec les relecteurs. | Aucun compte exigé, empreinte de clé hachée en base, non indexé par les moteurs. |
| **Agora & Tribune libre** | Espace de débat permanent et horizontal rattaché à la rubrique *Débats*. | Modération a priori collective, dépliant interactif natif `<details>`, zéro intermédiaire. |
| **Protection NoSpam** | Extension officielle NoSpam v3.0.1 avec jetons temporels et honeypots. | 100% local, aucun captcha visuel ou puzzle contraignant, respect de l'accessibilité. |
| **Relecture collective** | Interface privée d'évaluation collégiale par les compagnes et compagnons. | Quorum de deux validations conformes requis avant publication effective. |
| **Illustrations autonomes** | Miniatures documentaires générées localement et auto-synchronisées (`IMG/arton*.png`). | Aucun stockage distant propriétaire, formats WebP/PNG optimisés. |

---

## Démarrage rapide

### 1. Installation automatique en une commande

```bash
curl -fsSL https://raw.githubusercontent.com/AnARCHIS12/jeunesse-libertaire/main/install.sh | bash
```

Le script installe les prérequis, génère aléatoirement trois secrets cryptographiques, crée le fichier d'environnement `.env`, déploie le réseau Docker et initialise la base de données.

Pour une installation non-interactive en production :

```bash
curl -fsSL https://raw.githubusercontent.com/AnARCHIS12/jeunesse-libertaire/main/install.sh | \
  JL_INSTALL_DIR=/opt/jeunesse-libertaire \
  JL_SITE_ADDRESS=https://jeunesse.example.org \
  JL_WEB_PORT=8088 bash
```

### 2. Déploiement manuel via Docker Compose

```bash
# Cloner le dépôt
git clone https://github.com/AnARCHIS12/jeunesse-libertaire.git
cd jeunesse-libertaire

# Configurer l'environnement
cp .env.example .env
# Renseigner les mots de passe et l'URL publique dans .env

# Démarrer les services
docker compose up -d --build

# Activer le plugin NoSpam
echo yes | docker exec -i jeunesse-libertaire spip plugins:activer nospam

# Vider le cache SPIP
docker exec jeunesse-libertaire spip cache:vider
```

L'espace public est accessible à l'adresse configurée dans `SPIP_SITE_ADDRESS` (par défaut `http://localhost:8088`), et l'espace de gestion restreint dans `/ecrire/`.

---

## Architecture technique

```
jeunesse-libertaire/
├── assets/                          • Ressources graphiques, logos Ⓐ et visuels
│   ├── agora-miniature.png          • Miniature officielle de l'Agora et Tribune libre
│   ├── bienvenue-miniature.png      • Illustration d'accueil du média
│   ├── avatar-reseaux-noir.png      • Logo rond Ⓐ monochrome
│   └── avatar-reseaux-rouge.png     • Logo rond Ⓐ rouge et noir
├── config/                          • Fichiers de configuration système SPIP
├── plugins/
│   ├── jeunesse_collaboratif/       • Cœur métier : proposition, suivi et relecture
│   │   ├── base/                    • Déclaration des schémas de base de données
│   │   ├── formulaires/             • Contrôleurs de proposition d'article
│   │   └── prive/                   • Écrans de relecture collective du back-office
│   └── nospam/                      • Extension officielle NoSpam v3.0.1
├── squelettes/                      • Gabarits de présentation du site (HTML5 / SPIP)
│   ├── css/jeunesse.css             • Feuille de style unique et autonome
│   ├── sommaire.html                • Page d'accueil avec flux éditorial et bannières
│   ├── article.html                 • Vue complète des publications et espace commentaires
│   ├── forum.html                   • Agora ouverte, accordéon interactif et débats libres
│   ├── rubrique.html                • Listes par thématiques (Actualités, Débats, etc.)
│   ├── proposer.html                • Formulaire public de dépôt de texte
│   ├── suivi-proposition.html       • Espace d'échange privé entre auteur·ice et collectif
│   └── mes_fonctions.php            • Filtres SPIP personnalisés et résilience des logos
├── docker-compose.yml               • Orchestration conteneurisée (Web SPIP + DB MariaDB)
├── Dockerfile                       • Définition de l'image SPIP durcie
└── install.sh                       • Script d'installation autonome
```

---

## Circuit de contribution & Relecture

```
[Visiteuse / Visiteur] ──› Dépôt d'article (/spip.php?page=proposer)
                                   │
                                   ├──› Clé secrète de suivi générée
                                   └──› Article enregistré en statut « prop » (Relecture)
                                                  │
                                                  ▼
                               [Comité de Relecture Collective]
                               (Édition → Relecture collective)
                                                  │
                                   ├── Échanges horizontaux avec l'auteur·ice
                                   ├── Vote collectif (Accord / Amendements / Opposition)
                                   │
                                   ▼
                            [Quorum atteint : 2 validations nettes]
                                                  │
                                                  ▼
                                    Publication sur le média
```

1. **Dépôt** : la personne contributrice renseigne son pseudonyme, son texte, confirme la licence libre CC BY-SA 4.0 et reçoit son lien secret de suivi.
2. **Relecture collective** : les compagnes et compagnons du collectif examinent la proposition dans l'espace dédié.
3. **Échange horizontal** : le lien secret permet à la personne contributrice de dialoguer avec les relecteurs sans avoir à créer de compte sur le serveur.
4. **Publication** : l'article n'est publié que si le quorum collectif est atteint, prévenant toute décision unilatérale.

---

## Sécurité & Défense en profondeur

- **NoSpam v3.0.1 actif** : détection heuristique des spambots, pièges `email_nobot` aléatoires et jetons signés temporellement.
- **Zéro fuite de données** : adresses email facultatives, jamais exposées publiquement.
- **Réseau interne isolé** : MariaDB est strictement confinée au réseau virtuel interne `back` et inaccessible depuis l'extérieur.
- **Isolation du port hôte** : liaison explicite à `127.0.0.1` par défaut pour forcer le passage par un reverse proxy sécurisé (Cosmos, Caddy, Nginx).
- **Protection des secrets** : exclusion stricte du fichier `.env` et des clés cryptographiques dans `.gitignore`.

---

## Mises à jour du serveur

Pour répercuter les dernières améliorations sur votre serveur en production :

```bash
cd /chemin/vers/jeunesse-libertaire
git pull origin main
docker compose up -d
echo yes | docker exec -i jeunesse-libertaire spip plugins:activer nospam
docker exec jeunesse-libertaire spip cache:vider
```

---

## Licences

- **Code source, gabarits et infrastructure** : [GNU General Public License v3.0 or later (GPL-3.0-or-later)](LICENSE).
- **Contenus textuels et articles** : [Creative Commons Attribution-ShareAlike 4.0 International (CC BY-SA 4.0)](https://creativecommons.org/licenses/by-sa/4.0/).
- **Photographies et documents** : soumises aux mentions et licences spécifiques indiquées sur chaque ressource.
