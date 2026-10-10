import urllib.request, urllib.parse, re, time

base_url = 'http://127.0.0.1:8088/spip.php?page=proposer'
req = urllib.request.Request(base_url)
with urllib.request.urlopen(req) as resp:
    html = resp.read().decode('utf-8')

# Capture avec saut de ligne
form_action_args = re.search(r"name=['\"]formulaire_action_args['\"][^>]*value=['\"]([^'\"]+)['\"]", html, re.DOTALL).group(1)
debut_form = re.search(r"name=['\"]debut_formulaire['\"][^>]*value=['\"]([^'\"]+)['\"]", html, re.DOTALL).group(1)

print("Valeurs extraites avec succès.")
print("Attente de 5 secondes pour passer la barrière anti-bot...")
time.sleep(5)

post_data = {
    'page': 'proposer',
    'formulaire_action': 'proposer_article',
    'formulaire_action_args': form_action_args,
    'formulaire_action_sign': '',
    'debut_formulaire': debut_form,
    'website': '', # honeypot anti-spam : vide
    'pseudo': 'Camarade_Test_Lycéen',
    'email': 'compagne@riseup.net',
    'titre': 'Test de proposition en direct : grève générale dans les lycées',
    'type_contribution': 'actualite',
    'id_rubrique': '1', # Actualités
    'texte': 'Ceci est un texte de test pour vérifier la fonctionnalité de dépôt d article. Nous nous organisons dans les lycées et les facs sans hiérarchie. La grève est votée en assemblée générale souveraine et les comités de lutte sont en place partout.',
    'sources': 'Compte-rendu d AG du comité de lutte autonome',
    'licence': '1',
}

encoded_data = urllib.parse.urlencode(post_data).encode('utf-8')
post_req = urllib.request.Request(base_url, data=encoded_data, headers={'Content-Type': 'application/x-www-form-urlencoded'})

with urllib.request.urlopen(post_req) as resp:
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

