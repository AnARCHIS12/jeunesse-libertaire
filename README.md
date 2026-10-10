<p align="center">
  <img src="assets/avatar-reseaux-noir.png" alt="Jeunesse Libertaire" width="120" height="120" />
</p>

<h1 align="center">JEUNESSE LIBERTAIRE</h1>

<p align="center">
  <strong>Partoprena amaskomunikilo por kolektiva emancipiĝo, populara edukado kaj mezlernejaj kaj studentaj luktoj.</strong>
</p>

<p align="center">
  <a href="README.md"><b>Esperanto</b></a> •
  <a href="README.fr.md">Français</a> •
  <a href="README.en.md">English</a> •
  <a href="README.es.md">Español</a>
</p>

<p align="center">
  <a href="https://github.com/AnARCHIS12/jeunesse-libertaire"><img src="https://img.shields.io/badge/status-aktiva-10b981?style=flat-square" alt="Stato" /></a>
  <a href="https://www.spip.net"><img src="https://img.shields.io/badge/SPIP-4.4.28-c92a2a?style=flat-square" alt="SPIP Versio" /></a>
  <a href="https://www.php.net"><img src="https://img.shields.io/badge/PHP-8.4-4f5b93?style=flat-square" alt="PHP Versio" /></a>
  <a href="https://mariadb.org"><img src="https://img.shields.io/badge/MariaDB-11.8_LTS-003545?style=flat-square" alt="MariaDB Versio" /></a>
  <a href="https://contrib.spip.net/NoSPAM"><img src="https://img.shields.io/badge/sekureco-NoSpam_3.0.1-10b981?style=flat-square" alt="NoSpam Sekureco" /></a>
  <a href="LICENSE"><img src="https://img.shields.io/badge/kodo-GPL--3.0--or--later-blue?style=flat-square" alt="Koda Permesilo" /></a>
  <a href="https://creativecommons.org/licenses/by-sa/4.0/"><img src="https://img.shields.io/badge/enhavo-CC--BY--SA--4.0-lightgrey?style=flat-square" alt="Enhava Permesilo" /></a>
</p>

---

## Superrigardo

**Jeunesse Libertaire** estas memstara plurmedia platformo kreita de kaj por luktanta junularo. Konstruita sur la libera bazo SPIP 4.4 en plifortigita kontenera medio, ĝi kunigas publikan formularon por proponi artikolojn, malferman Agoron kaj rektan diskutspacon por la kunbatalantaj gejunuloj (kompaninoj kaj kompanoj), kolektivan relegadan cirkviton sen deviga konto-kreado, kaj altnivelan kontraŭspaman protekton sen eksteraj servoj aŭ komerca spurado.

- **Plena memstareco kaj Nul Eksteraj CDN-oj** : neniu voko al fermitaj proprietaj serviloj (Google, Cloudflare, ktp.). Ĉiuj skriptoj, stiloj kaj vektoraj bildoj estas loke gastigitaj kaj funkcias en fermita reto aŭ senrete.
- **Modereco kaj Kruda Estetiko** : fasonado inspirita de la avangarda anarkiisma gazetaro (profunda nigro `#0f0f10`, varma papero `#f4efe8`, vigla ruĝo `#d32920`).
- **Kolektiva Emancipiĝo** : horizontalaj iloj garantiantaj anonimecon, eldonliberecon kaj foreston de burokratia hierarkio.

---

## Ĉefaj Trajtoj

| Modulo | Priskribo | Sekureco kaj Etiko |
| :--- | :--- | :--- |
| **Rekta Propono** | Depono de tekstoj, atestoj, strikanalizoj kaj raportoj de ĝeneralaj asembleoj. | Kontrolo kontraŭ robotoj en 4 sekundoj, nevidebla kaptilo (*honeypot*), IP-limigilo ĉifrita per SHA-256. |
| **Sekreta Spurado** | Privata sekreta ligilo donita al la verkinto por dialogi kun la relegteamo. | Neniu konto postulata, sekreta ŝlosilo haketita en datumbazo, ne indeksita de serĉiloj. |
| **Agora kaj Libera Tribuno** | Konstanta kaj horizontala debata spaco ligita al la rubriko *Débats*. | Kolektiva antaŭa modereco, interaga faldebla elemento `<details>`, nul perantoj. |
| **NoSpam Protekto** | Oficiala kromaĵo NoSpam v3.0.1 kun tempaj ĵetonoj kaj falsaj kampoj. | 100% loka, neniu altrudita vida puzlo aŭ captcha, plena alirebleco. |
| **Kolektiva Relegado** | Privata interfaco por kolektiva taksado fare de la redakta kolektivo. | Kvorumo de du pozitivaj validigoj postulata antaŭ fakta publikigo. |
| **Lokaj Ilustraĵoj** | Dokumentaj miniaturoj kreitaj loke kaj aŭtomate sinkronigitaj (`IMG/arton*.png`). | Neniu ekstera nuba stokado, optimumigitaj formatoj WebP/PNG. |

---

## Rapida Ekfunkciigo

### 1. Aŭtomata instalado per unu komando

```bash
curl -fsSL https://raw.githubusercontent.com/AnARCHIS12/jeunesse-libertaire/main/install.sh | bash
```

La skripto instalas la dependecojn, hazarde generas tri kriptografiajn sekretojn, kreas la dosieron `.env`, lanĉas la Docker-reton kaj pravalorizas la datumbazon.

Por neinteraga instalado en produktado :

```bash
curl -fsSL https://raw.githubusercontent.com/AnARCHIS12/jeunesse-libertaire/main/install.sh | \
  JL_INSTALL_DIR=/opt/jeunesse-libertaire \
  JL_SITE_ADDRESS=https://jeunesse.example.org \
  JL_WEB_PORT=8088 bash
```

### 2. Mana deplojo per Docker Compose

```bash
# Kloni la deponejon
git clone https://github.com/AnARCHIS12/jeunesse-libertaire.git
cd jeunesse-libertaire

# Agordi la medion
cp .env.example .env
# Difini la pasvortojn kaj publikan URL en .env

# Lanĉi la servojn
docker compose up -d --build

# Aktivigi la kromaĵon NoSpam
echo yes | docker exec -i jeunesse-libertaire spip plugins:activer nospam

# Malplenigi la SPIP-kaŝmemoron
docker exec jeunesse-libertaire spip cache:vider
```

La publika retejo estas alirebla ĉe la adreso difinita en `SPIP_SITE_ADDRESS` (defaŭlte `http://localhost:8088`), kaj la administra interfaco ĉe `/ecrire/`.

---

## Teknika Strukturo

```
jeunesse-libertaire/
├── assets/                          • Grafikaj rimedoj, emblemoj Ⓐ kaj vidaĵoj
│   ├── agora-miniature.png          • Oficiala miniaturo por la Agora kaj Libera Tribuno
│   ├── bienvenue-miniature.png      • Bonvena ilustraĵo de la amaskomunikilo
│   ├── avatar-reseaux-noir.png      • Monokromata cirkla emblemo Ⓐ
│   └── avatar-reseaux-rouge.png     • Ruĝ-nigra cirkla emblemo Ⓐ
├── config/                          • Sistemaj agordaj dosieroj de SPIP
├── plugins/
│   ├── jeunesse_collaboratif/       • Kerna logiko : propono, sekreta spurado kaj relegado
│   │   ├── base/                    • Datumbazaj skemoj
│   │   ├── formulaires/             • Formularaj traktiloj
│   │   └── prive/                   • Kolektiva relegada panelo
│   └── nospam/                      • Oficiala kromaĵo NoSpam v3.0.1
├── squelettes/                      • Prezentaj ŝablonoj (HTML5 / SPIP)
│   ├── css/jeunesse.css             • Memstara stildosiero sen eksteraj ligiloj
│   ├── sommaire.html                • Ĉefpaĝo kun eldona fluo kaj rubrikoj
│   ├── article.html                 • Plena artikola vido kaj komentoj
│   ├── forum.html                   • Malfermita Agora, interaga faldfolio kaj liberaj debatoj
│   ├── rubrique.html                • Rubrikaj listoj (Aktualaĵoj, Debatoj, ktp.)
│   ├── proposer.html                • Publika formularo por proponi artikolon
│   ├── suivi-proposition.html       • Privata komunikspaco inter verkinto kaj kolektivo
│   └── mes_fonctions.php            • Propraj SPIP-filtriloj kaj aŭtomataj miniaturoj
├── docker-compose.yml               • Kontenera orkestrado (SPIP + MariaDB)
├── Dockerfile                       • Plifortigita SPIP-bildo
└── install.sh                       • Aŭtonoma instala skripto
```

---

## Cirkvito de Propono kaj Relegado

```
[Vizitanto] ──› Depono de teksto (/spip.php?page=proposer)
                     │
                     ├──› Generita privata sekreta ŝlosilo
                     └──› Artikolo registrita kun statuso « prop » (Relegado)
                                    │
                                    ▼
                     [Kolektiva Relegada Komitato]
                     (Édition → Relecture collective)
                                    │
                     ├── Horizontalaj interŝanĝoj kun la verkinto
                     ├── Kolektiva voĉdono (Interkonsento / Amendoj / Kontraŭstaro)
                     │
                     ▼
              [Kvorumo atingita : 2 klaraj validigoj]
                     │
                     ▼
               Publikigo en la retejo
```

1. **Depono** : la kontribuanto indikas sian pseŭdonimon, sian tekston, konfirmas la liberan permesilon CC BY-SA 4.0 kaj ricevas sekretan ligilon.
2. **Kolektiva relegado** : la membroj de la kolektivo ekzamenas la proponon en la dediĉita spaco.
3. **Horizontala interŝanĝo** : la sekreta ligilo ebligas dialogi kun la relegteamo sen bezono krei konton sur la servilo.
4. **Publikigo** : la teksto publikiĝas nur kiam la kolektiva kvorumo estas plenumita.

---

## Servilaj Ĝisdatigoj

Por apliki la plej novajn plibonigojn sur via produktada servilo :

```bash
cd /vojo/al/jeunesse-libertaire
git pull origin main
docker compose up -d
echo yes | docker exec -i jeunesse-libertaire spip plugins:activer nospam
docker exec jeunesse-libertaire spip cache:vider
```

---

## Permesiloj

- **Fontkodo, ŝablonoj kaj infrastrukturo** : [GNU General Public License v3.0 or later (GPL-3.0-or-later)](LICENSE).
- **Tekstaj enhavoj kaj artikoloj** : [Creative Commons Attribution-ShareAlike 4.0 International (CC BY-SA 4.0)](https://creativecommons.org/licenses/by-sa/4.0/).
- **Fotoj kaj dokumentoj** : laŭ la specifaj permesiloj indikitaj sur ĉiu havaĵo.
