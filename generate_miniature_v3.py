import os
from PIL import Image, ImageDraw, ImageFont, ImageEnhance

W, H = 1200, 675

# 1. Base Photo : rush_c.jpg
# On se concentre sur les jeunes au premier plan qui tiennent les pancartes et banderoles (Louis-le-Grand en solidarité)
img_raw = Image.open("/tmp/frames_search/rush_c.jpg").convert("RGBA")

# Le bloc d'action des jeunes est entre y=680 et y=1700
crop_y1 = 680
crop_h = int(1080 * (H / W))
action_crop = img_raw.crop((0, crop_y1, 1080, crop_y1 + crop_h)).resize((W, H), Image.Resampling.LANCZOS)

# Couleurs punchy et nettes
action_crop = ImageEnhance.Contrast(action_crop).enhance(1.2)
action_crop = ImageEnhance.Color(action_crop).enhance(1.1)

# 2. Bandeaux graphiques nets et contrastés (style affiche militante)
overlay = Image.new("RGBA", (W, H), (0, 0, 0, 0))
draw_ol = ImageDraw.Draw(overlay)

# Léger vignettage sombre en haut pour poser le header
for y in range(160):
    a = int(220 * (1.0 - (y / 160.0)))
    draw_ol.line([(0, y), (W, y)], fill=(15, 14, 13, a))

# Dégradé franc en bas pour que les slogans ressortent à 100% sans bavure
for y in range(320, H):
    factor = (y - 320) / float(H - 320)
    a = int(245 * (factor ** 0.8))
    draw_ol.line([(0, y), (W, y)], fill=(12, 11, 10, a))

composite = Image.alpha_composite(action_crop, overlay)

# 3. Logo officiel circulaire détouré (avatar-reseaux-noir.png)
logo_src = Image.open("/home/anar/jeunesse-libertaire/assets/avatar-reseaux-noir.png").convert("RGBA")
logo_resized = logo_src.resize((100, 100), Image.Resampling.LANCZOS)
composite.paste(logo_resized, (40, 25), logo_resized)

# 4. Textes avec police grasse bien posée
draw = ImageDraw.Draw(composite)

font_bold = "/usr/share/fonts/dejavu-sans-fonts/DejaVuSans-Bold.ttf"
if not os.path.exists(font_bold):
    font_bold = "/usr/share/fonts/google-noto/NotoSans-Bold.ttf"

f_top = ImageFont.truetype(font_bold, 20)
f_h1 = ImageFont.truetype(font_bold, 56)
f_sub = ImageFont.truetype(font_bold, 26)
f_pills = ImageFont.truetype(font_bold, 20)

# Header top
draw.rectangle([155, 35, 460, 72], fill=(211, 41, 32, 255))
draw.text((170, 42), "JEUNESSE LIBERTAIRE", fill=(255, 255, 255), font=f_top)
draw.text((155, 82), "MÉDIA D'ACTION DIRECTE & D'ÉDUCATION POPULAIRE", fill=(240, 235, 226), font=ImageFont.truetype(font_bold, 14))

# Bloc Titre en bas sur fond sombre parfaitement lisible
draw.text((40, 360), "PRENDRE LA PAROLE.", fill=(255, 255, 255), font=f_h1)
draw.text((40, 430), "CONSTRUIRE L'AVENIR !", fill=(211, 41, 32), font=f_h1)

draw.rectangle([40, 510, 320, 514], fill=(211, 41, 32))

draw.text((40, 535), "Face à l'État et au capital : l'auto-organisation des facs et des lycées", fill=(255, 255, 255), font=f_sub)

# Badges mots-clés percutants en bas
pills = ["GRÈVES", "BLOCAGES", "ASSEMBLÉES GÉNÉRALES", "ACTION DIRECTE"]
px = 40
py = 595
for p in pills:
    bbox = draw.textbbox((0, 0), p, font=f_pills)
    pw = bbox[2] - bbox[0] + 24
    draw.rectangle([px, py, px + pw, py + 38], fill=(211, 41, 32, 240))
    draw.text((px + 12, py + 8), p, fill=(255, 255, 255), font=f_pills)
    px += pw + 16

final_img = composite.convert("RGB")
final_img.save("/home/anar/jeunesse-libertaire/assets/bienvenue-miniature.png", quality=95)
print("MINIATURE_V3_SUCCESS")
