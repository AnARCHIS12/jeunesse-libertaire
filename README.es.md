<p align="center">
  <img src="assets/avatar-reseaux-noir.png" alt="Jeunesse Libertaire" width="120" height="120" />
</p>

<h1 align="center">JEUNESSE LIBERTAIRE</h1>

<p align="center">
  <strong>Medio participativo de emancipación colectiva, educación popular y combate de las luchas estudiantiles y juveniles.</strong>
</p>

<p align="center">
  <a href="README.md">Esperanto</a> •
  <a href="README.fr.md">Français</a> •
  <a href="README.en.md">English</a> •
  <a href="README.es.md"><b>Español</b></a>
</p>

<p align="center">
  <a href="https://github.com/AnARCHIS12/jeunesse-libertaire"><img src="https://img.shields.io/badge/estado-activo-10b981?style=flat-square" alt="Estado" /></a>
  <a href="https://www.spip.net"><img src="https://img.shields.io/badge/SPIP-4.4.28-c92a2a?style=flat-square" alt="Versión SPIP" /></a>
  <a href="https://www.php.net"><img src="https://img.shields.io/badge/PHP-8.4-4f5b93?style=flat-square" alt="Versión PHP" /></a>
  <a href="https://mariadb.org"><img src="https://img.shields.io/badge/MariaDB-11.8_LTS-003545?style=flat-square" alt="Versión MariaDB" /></a>
  <a href="https://contrib.spip.net/NoSPAM"><img src="https://img.shields.io/badge/seguridad-NoSpam_3.0.1-10b981?style=flat-square" alt="Seguridad NoSpam" /></a>
  <a href="LICENSE"><img src="https://img.shields.io/badge/código-GPL--3.0--or--later-blue?style=flat-square" alt="Licencia Código" /></a>
  <a href="https://creativecommons.org/licenses/by-sa/4.0/"><img src="https://img.shields.io/badge/contenido-CC--BY--SA--4.0-lightgrey?style=flat-square" alt="Licencia Contenido" /></a>
</p>

---

## Visión general

**Jeunesse Libertaire** es una plataforma multimedia autogestionada concebida por y para la juventud en lucha. Construida sobre la base libre de SPIP 4.4 en un entorno contenerizado blindado, integra un formulario público para proponer artículos, un espacio de Ágora y debate directo para compañeras y compañeros, un circuito de revisión colectiva sin necesidad de crear una cuenta, y una protección anti-spam avanzada sin servicios de terceros ni rastreo comercial.

- **Autonomía total y Cero CDNs**: cero peticiones a servidores propietarios externos (Google, Cloudflare, etc.). Todos los scripts, hojas de estilo e imágenes vectoriales están alojados localmente y funcionan en red cerrada o sin conexión.
- **Sobriedad y Estética cruda**: diseño cuidado inspirado en la prensa libertaria de vanguardia (negro profundo `#0f0f10`, papel cálido `#f4efe8`, rojo de combate `#d32920`).
- **Emancipación colectiva**: herramientas horizontales que aseguran el anonimato, la libertad de publicación y la ausencia de jerarquías burocráticas.

---

## Funcionalidades principales

| Módulo | Descripción | Seguridad y Ética |
| :--- | :--- | :--- |
| **Propuesta directa** | Envío de textos, testimonios, análisis de huelgas y actas de asambleas. | Validación anti-bot en 4 segundos, trampa oculta (*honeypot*), limitador de IP cifrado en SHA-256. |
| **Seguimiento secreto** | Enlace privado entregado a quien redacta para dialogar con el equipo de lectura. | Sin registro obligatorio, huella criptográfica en base de datos, no indexado por buscadores. |
| **Ágora y Tribuna libre** | Espacio de debate continuo y horizontal vinculado a la sección *Débats*. | Moderación colectiva previa, acordeón interactivo nativo `<details>`, sin intermediarios. |
| **Protección NoSpam** | Extensión oficial NoSpam v3.0.1 con fichas temporales y campos trampa. | 100% local, sin captchas visuales forzados ni rompecabezas, respetuoso con la accesibilidad. |
| **Revisión colectiva** | Panel privado de evaluación horizontal por la asamblea de redacción. | Se exige un quórum de dos aprobaciones válidas antes de su publicación efectiva. |
| **Ilustraciones locales** | Miniaturas documentales generadas localmente y sincronizadas automáticamente (`IMG/arton*.png`). | Sin almacenamiento en nubes privativas, formatos optimizados WebP/PNG. |

---

## Puesta en marcha rápida

### 1. Instalación automática en un comando

```bash
curl -fsSL https://raw.githubusercontent.com/AnARCHIS12/jeunesse-libertaire/main/install.sh | bash
```

El script instala las dependencias necesarias, genera tres secretos criptográficos de forma aleatoria, crea el archivo de entorno `.env`, despliega la red Docker e inicializa la base de datos.

Para una instalación desatendida en producción:

```bash
curl -fsSL https://raw.githubusercontent.com/AnARCHIS12/jeunesse-libertaire/main/install.sh | \
  JL_INSTALL_DIR=/opt/jeunesse-libertaire \
  JL_SITE_ADDRESS=https://jeunesse.example.org \
  JL_WEB_PORT=8088 bash
```

### 2. Despliegue manual mediante Docker Compose

```bash
# Clonar el repositorio
git clone https://github.com/AnARCHIS12/jeunesse-libertaire.git
cd jeunesse-libertaire

# Configurar el entorno
cp .env.example .env
# Indicar contraseñas y la URL pública en .env

# Iniciar los servicios
docker compose up -d --build

# Activar el plugin NoSpam
echo yes | docker exec -i jeunesse-libertaire spip plugins:activer nospam

# Limpiar la caché de SPIP
docker exec jeunesse-libertaire spip cache:vider
```

El espacio público está accesible en la dirección configurada en `SPIP_SITE_ADDRESS` (por defecto `http://localhost:8088`), y el área de administración en `/ecrire/`.

---

## Arquitectura técnica

```
jeunesse-libertaire/
├── assets/                          • Recursos gráficos, logotipos Ⓐ y multimedia
│   ├── agora-miniature.png          • Miniatura oficial del Ágora y Tribuna libre
│   ├── bienvenue-miniature.png      • Ilustración de bienvenida del medio
│   ├── avatar-reseaux-noir.png      • Logotipo circular Ⓐ monocromático
│   └── avatar-reseaux-rouge.png     • Logotipo circular Ⓐ rojo y negro
├── config/                          • Archivos de configuración interna de SPIP
├── plugins/
│   ├── jeunesse_collaboratif/       • Núcleo: propuesta, seguimiento secreto y revisión
│   │   ├── base/                    • Esquemas de base de datos
│   │   ├── formulaires/             • Controladores del formulario de propuesta
│   │   └── prive/                   • Paneles de revisión colectiva
│   └── nospam/                      • Extensión oficial NoSpam v3.0.1
├── squelettes/                      • Plantillas de presentación (HTML5 / SPIP)
│   ├── css/jeunesse.css             • Hoja de estilos única y autónoma
│   ├── sommaire.html                • Portada con flujo editorial y secciones
│   ├── article.html                 • Vista completa de artículos y comentarios
│   ├── forum.html                   • Ágora abierta, acordeón interactivo y debates libres
│   ├── rubrique.html                • Listados temáticos (Actualidad, Debates, etc.)
│   ├── proposer.html                • Formulario público para proponer textos
│   ├── suivi-proposition.html       • Área de diálogo privada entre autoría y colectivo
│   └── mes_fonctions.php            • Filtros personalizados de SPIP y motor de miniaturas
├── docker-compose.yml               • Orquestación contenerizada (SPIP Web + MariaDB)
├── Dockerfile                       • Imagen SPIP asegurada
└── install.sh                       • Script de instalación autónomo
```

---

## Circuito de contribución y Revisión

```
[Visitante] ──› Envío de texto (/spip.php?page=proposer)
                     │
                     ├──› Generación de clave secreta de seguimiento
                     └──› Artículo registrado con estado « prop » (Revisión)
                                    │
                                    ▼
                     [Comité de Revisión Colectiva]
                     (Édition → Relecture collective)
                                    │
                     ├── Intercambios horizontales con la persona autora
                     ├── Votación colectiva (Acuerdo / Enmiendas / Oposición)
                     │
                     ▼
              [Quórum alcanzado: 2 aprobaciones netas]
                     │
                     ▼
               Publicación en el medio
```

1. **Envío**: la persona colaboradora introduce su seudónimo, su texto, acepta la licencia libre CC BY-SA 4.0 y recibe su enlace secreto.
2. **Revisión colectiva**: las compañeras y compañeros examinan la propuesta en el panel privado de redacción.
3. **Diálogo horizontal**: el enlace secreto permite comunicarse con el colectivo sin necesidad de tener una cuenta de usuario en el servidor.
4. **Publicación**: el artículo solo se publica tras alcanzar el quórum colectivo, impidiendo decisiones individuales o arbitrarias.

---

## Seguridad y Defensa en profundidad

- **NoSpam v3.0.1 activo**: análisis heurístico anti-robots, trampas dinámicas `email_nobot` y tokens firmados temporalmente.
- **Cero fugas de datos**: correos electrónicos estrictamente opcionales y nunca expuestos públicamente.
- **Red interna aislada**: MariaDB está conectada exclusivamente a la red interna `back`, inaccesible desde el exterior.
- **Vinculación de puertos**: asignado explícitamente a `127.0.0.1` por defecto para forzar el paso por un proxy inverso seguro (Cosmos, Caddy, Nginx).
- **Protección de secretos**: exclusión tajante del archivo `.env` y claves en `.gitignore`.

---

## Actualizaciones del servidor

Para aplicar las últimas mejoras en su servidor de producción:

```bash
cd /ruta/hacia/jeunesse-libertaire
git pull origin main
docker compose up -d
echo yes | docker exec -i jeunesse-libertaire spip plugins:activer nospam
docker exec jeunesse-libertaire spip cache:vider
```

---

## Licencias

- **Código fuente, plantillas e infraestructura**: [GNU General Public License v3.0 or later (GPL-3.0-or-later)](LICENSE).
- **Textos y artículos**: [Creative Commons Attribution-ShareAlike 4.0 International (CC BY-SA 4.0)](https://creativecommons.org/licenses/by-sa/4.0/).
- **Fotografías y documentos**: sujetos a las licencias específicas indicadas en cada recurso.
