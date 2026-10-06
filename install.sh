#!/usr/bin/env bash
set -Eeuo pipefail

REPOSITORY="AnARCHIS12/jeunesse-libertaire"
BRANCH="main"
INSTALL_DIR="${JL_INSTALL_DIR:-$PWD/jeunesse-libertaire}"
SITE_ADDRESS="${JL_SITE_ADDRESS:-http://localhost:8088}"
WEB_PORT="${JL_WEB_PORT:-8088}"
START_STACK=1
INTERACTIVE=0
[[ -r /dev/tty && -w /dev/tty ]] && INTERACTIVE=1

if [[ -t 1 ]]; then
  RED=$'\033[38;5;196m'; DARK=$'\033[38;5;235m'; WHITE=$'\033[97m'
  GREY=$'\033[38;5;245m'; GREEN=$'\033[38;5;82m'; BOLD=$'\033[1m'; RESET=$'\033[0m'
else
  RED=''; DARK=''; WHITE=''; GREY=''; GREEN=''; BOLD=''; RESET=''
fi

cleanup() { if [[ -n "${TMP_DIR:-}" && -d "$TMP_DIR" ]]; then rm -rf "$TMP_DIR"; fi; }
fail() { printf '\n%s✕ %s%s\n' "$RED" "$*" "$RESET" >&2; exit 1; }
on_error() { local line=$1; trap - ERR; fail "Installation interrompue à la ligne $line."; }
step() { printf '\n%s◆%s %s%s%s\n' "$RED" "$RESET" "$BOLD" "$*" "$RESET"; }
ok() { printf '%s✓%s %s\n' "$GREEN" "$RESET" "$*"; }
trap cleanup EXIT
trap 'on_error "$LINENO"' ERR

banner() {
  printf '%s%s' "$DARK" "$BOLD"
  cat <<'ART'
╭──────────────────────────────────────────────────────────────╮
│                                                              │
│     Ⓐ   J E U N E S S E   L I B E R T A I R E               │
│                                                              │
│         média participatif · éducation populaire             │
│                                                              │
╰──────────────────────────────────────────────────────────────╯
ART
  printf '%s' "$RESET"
  printf '%sInstallation automatisée SPIP + MariaDB + relecture collective%s\n' "$RED" "$RESET"
}

usage() {
  cat <<EOF
Usage : install.sh [options]

  --dir CHEMIN       Dossier d'installation
  --url URL          Adresse publique du site
  --port PORT        Port local publié (défaut : 8088)
  --no-start         Préparer les fichiers sans lancer Docker
  -h, --help         Afficher cette aide

Variables équivalentes : JL_INSTALL_DIR, JL_SITE_ADDRESS, JL_WEB_PORT
EOF
}

while (($#)); do
  case "$1" in
    --dir) [[ $# -ge 2 ]] || fail "Valeur manquante après --dir"; INSTALL_DIR=$2; shift 2 ;;
    --url) [[ $# -ge 2 ]] || fail "Valeur manquante après --url"; SITE_ADDRESS=$2; shift 2 ;;
    --port) [[ $# -ge 2 ]] || fail "Valeur manquante après --port"; WEB_PORT=$2; shift 2 ;;
    --no-start) START_STACK=0; shift ;;
    -h|--help) usage; exit 0 ;;
    *) fail "Option inconnue : $1" ;;
  esac
done

ask() {
  local prompt=$1 default=$2 answer=''
  if ((INTERACTIVE)); then
    printf '%s%s%s [%s] : ' "$WHITE" "$prompt" "$RESET" "$default" >/dev/tty
    IFS= read -r answer </dev/tty || true
  fi
  printf '%s' "${answer:-$default}"
}

secret() {
  if command -v openssl >/dev/null 2>&1; then
    openssl rand -hex 24
  else
    od -An -N24 -tx1 /dev/urandom | tr -d ' \n'
  fi
}

banner

command -v curl >/dev/null 2>&1 || fail "curl est nécessaire."
command -v tar >/dev/null 2>&1 || fail "tar est nécessaire."
[[ "$WEB_PORT" =~ ^[0-9]+$ ]] && ((WEB_PORT >= 1 && WEB_PORT <= 65535)) || fail "Port invalide : $WEB_PORT"

if ((INTERACTIVE)); then
  INSTALL_DIR=$(ask "Dossier d'installation" "$INSTALL_DIR")
  SITE_ADDRESS=$(ask "Adresse publique du site" "$SITE_ADDRESS")
  WEB_PORT=$(ask "Port local du conteneur" "$WEB_PORT")
fi

[[ "$WEB_PORT" =~ ^[0-9]+$ ]] && ((WEB_PORT >= 1 && WEB_PORT <= 65535)) || fail "Port invalide : $WEB_PORT"
[[ "$SITE_ADDRESS" =~ ^https?://[^[:space:]]+$ ]] || fail "Adresse de site invalide : $SITE_ADDRESS"

[[ "$INSTALL_DIR" = /* ]] || INSTALL_DIR="$PWD/${INSTALL_DIR#./}"

if [[ -e "$INSTALL_DIR" ]] && [[ -n "$(find "$INSTALL_DIR" -mindepth 1 -maxdepth 1 -print -quit 2>/dev/null)" ]]; then
  fail "Le dossier $INSTALL_DIR existe déjà et n'est pas vide. Choisissez un autre dossier avec --dir."
fi

step "Vérification de Docker"
DOCKER=(docker)
if ((START_STACK)); then
  command -v docker >/dev/null 2>&1 || fail "Docker n'est pas installé. Installez Docker Engine puis relancez la commande."
  docker compose version >/dev/null 2>&1 || fail "Le plugin Docker Compose v2 est nécessaire."
  if ! docker info >/dev/null 2>&1; then
    if command -v sudo >/dev/null 2>&1 && sudo docker info >/dev/null 2>&1; then
      DOCKER=(sudo docker)
    else
      fail "Docker est présent, mais votre compte ne peut pas accéder au service Docker."
    fi
  fi
  ok "Docker et Compose sont disponibles"
else
  ok "Démarrage Docker désactivé"
fi

step "Téléchargement de Jeunesse Libertaire"
TMP_DIR=$(mktemp -d)
ARCHIVE="$TMP_DIR/jeunesse-libertaire.tar.gz"
curl --fail --location --silent --show-error \
  --retry 3 --connect-timeout 15 \
  "https://github.com/${REPOSITORY}/archive/refs/heads/${BRANCH}.tar.gz" \
  --output "$ARCHIVE"
tar -xzf "$ARCHIVE" -C "$TMP_DIR"
SOURCE_DIR=$(find "$TMP_DIR" -mindepth 1 -maxdepth 1 -type d -name 'jeunesse-libertaire-*' -print -quit)
[[ -n "$SOURCE_DIR" && -f "$SOURCE_DIR/docker-compose.yml" ]] || fail "L'archive téléchargée n'est pas valide."
mkdir -p "$INSTALL_DIR"
cp -a "$SOURCE_DIR"/. "$INSTALL_DIR"/
ok "Projet installé dans $INSTALL_DIR"

step "Création de la configuration sécurisée"
umask 077
cat >"$INSTALL_DIR/.env" <<EOF
MYSQL_DATABASE=spip
MYSQL_USER=spip
MYSQL_PASSWORD=$(secret)
MYSQL_ROOT_PASSWORD=$(secret)
SPIP_SITE_NAME=Jeunesse Libertaire
SPIP_SITE_ADDRESS=$SITE_ADDRESS
WEB_BIND=127.0.0.1
WEB_PORT=$WEB_PORT
SPIP_ADMIN_LOGIN=admin
SPIP_ADMIN_NAME=Admin
SPIP_ADMIN_EMAIL=admin@localhost
SPIP_ADMIN_PASSWORD=$(secret)
EOF
chmod 600 "$INSTALL_DIR/.env"
ok "Trois secrets uniques ont été générés dans .env"

if ((START_STACK)); then
  step "Construction et démarrage des conteneurs"
  (cd "$INSTALL_DIR" && "${DOCKER[@]}" compose pull --quiet db && "${DOCKER[@]}" compose up -d --build)
  ok "SPIP et MariaDB sont démarrés"

  step "État des services"
  (cd "$INSTALL_DIR" && "${DOCKER[@]}" compose ps)
fi

printf '\n%s%s╭──────────────── Installation terminée ────────────────╮%s\n' "$RED" "$BOLD" "$RESET"
printf '%s  Site          %s%s%s\n' "$GREY" "$WHITE" "$SITE_ADDRESS" "$RESET"
printf '%s  Administration%s%s/ecrire/%s\n' "$GREY" "$WHITE" "${SITE_ADDRESS%/}" "$RESET"
printf '%s  Dossier       %s%s%s\n' "$GREY" "$WHITE" "$INSTALL_DIR" "$RESET"
printf '%s%s╰───────────────────────────────────────────────────────╯%s\n' "$RED" "$BOLD" "$RESET"
printf '%sLes identifiants administrateur sont dans %s/.env.%s\n' "$GREY" "$INSTALL_DIR" "$RESET"
printf '%sConservez ce fichier privé et sauvegardez les volumes Docker.%s\n\n' "$GREY" "$RESET"
