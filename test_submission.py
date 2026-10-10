import urllib.request, urllib.parse, re, time, http.cookiejar

base_url = 'http://127.0.0.1:8088/spip.php?page=proposer'
headers = {
    'User-Agent': 'Mozilla/5.0 (X11; Linux x86_64; rv:128.0) Gecko/20100101 Firefox/128.0',
    'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    'Accept-Language': 'fr,fr-FR;q=0.8,en-US;q=0.5,en;q=0.3',
}

cj = http.cookiejar.CookieJar()
opener = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(cj))

req = urllib.request.Request(base_url, headers=headers)
with opener.open(req) as resp:
    html = resp.read().decode('utf-8')

# Capture avec saut de ligne
form_action_args = re.search(r"name=['\"]formulaire_action_args['\"][^>]*value=['\"]([^'\"]+)['\"]", html, re.DOTALL).group(1)
debut_form = re.search(r"name=['\"]debut_formulaire['\"][^>]*value=['\"]([^'\"]+)['\"]", html, re.DOTALL).group(1)
m_jeton = re.search(r"name=['\"]_jeton['\"][^>]*value=['\"]([^'\"]+)['\"]", html, re.DOTALL)
jeton = m_jeton.group(1) if m_jeton else ''

print("Valeurs extraites avec succès.")
print("Attente de 5 secondes pour passer la barrière anti-bot...")
time.sleep(5)

post_data = {
    'page': 'proposer',
    'formulaire_action': 'proposer_article',
    'formulaire_action_args': form_action_args,
    'formulaire_action_sign': '',
    '_jeton': jeton,
    'debut_formulaire': debut_form,
    'website': '', # honeypot anti-spam : vide
    'pseudo': 'Compagne_Test_Lycéenne',
    'email': 'compagne@riseup.net',
    'titre': 'Test de proposition en direct : grève générale dans les lycées',
    'type_contribution': 'actualite',
    'id_rubrique': '1', # Actualités
    'texte': 'Ceci est un texte de test pour vérifier la fonctionnalité de dépôt d article. Nous nous organisons dans les lycées et les facs sans hiérarchie. La grève est votée en assemblée générale souveraine et les comités de lutte sont en place partout.',
    'sources': 'Compte-rendu d AG du comité de lutte autonome',
    'licence': '1',
}

encoded_data = urllib.parse.urlencode(post_data).encode('utf-8')
post_headers = dict(headers)
post_headers['Content-Type'] = 'application/x-www-form-urlencoded'
post_req = urllib.request.Request(base_url, data=encoded_data, headers=post_headers)

with opener.open(post_req) as resp:
    res_html = resp.read().decode('utf-8')

print("RESULTAT :")
if "Votre texte a été transmis à la relecture collective" in res_html:
    print("✓ SUCCÈS CONFIRMÉ : Le formulaire fonctionne à 100% !")
    url_match = re.search(r'href=[\'\"]([^\'\"]*suivi-proposition[^\'\"]*)[\'\"]', res_html)
    if url_match:
        print("✓ Lien de suivi privé généré :", url_match.group(1))
elif "erreur" in res_html.lower():
    print("✕ ERREUR :", re.findall(r'class=[\'\"][^\'\"]*erreur[^\'\"]*[\'\"][^>]*>(.*?)<', res_html))
else:
    print("Contenu de retour non reconnu.")

