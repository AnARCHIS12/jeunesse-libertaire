import os
from PIL import Image, ImageDraw, ImageFont, ImageFilter, ImageEnhance

W, H = 1200, 675

# 1. Load background action shot
# action_shot1 is 1080x1920 (vertical). Crop and scale to 1200x675
bg = Image.open("/tmp/action_shot1.jpg").convert("RGBA")
# Crop center region
crop_w = bg.width
crop_h = int(crop_w * (H / W))
top = int((bg.height - crop_h) * 0.35)
bg_cropped = bg.crop((0, top, crop_w, top + crop_h)).resize((W, H), Image.Resampling.LANCZOS)

# Enhance contrast and saturation for high impact
bg_cropped = ImageEnhance.Contrast(bg_cropped).enhance(1.25)
bg_cropped = ImageEnhance.Color(bg_cropped).enhance(1.15)

# Darken / vignette overlay so text pops
overlay = Image.new("RGBA", (W, H), (0, 0, 0, 0))
draw_ol = ImageDraw.Draw(overlay)

# Dark gradient from left (where text and logo are)
for x in range(W):
    # gradient alpha from 0.88 on left to 0.4 on right
    factor = 1.0 - (x / W)
    alpha = int(240 * (factor ** 0.8) + 60)
    draw_ol.line([(x, 0), (x, H)], fill=(18, 17, 15, min(alpha, 245)))

# Add red tint diagonal slash across bottom-left
for y in range(H):
    alpha_red = max(0, int(160 * (1.0 - (y / H) ** 0.5)))
    # draw_ol.line([(0, y), (int(W * 0.4), y)], fill=(211, 41, 32, alpha_red))

composite = Image.alpha_composite(bg_cropped, overlay)

# 2. Render AnU Logo in top-left
# Vector AnU logo: red rounded rect, diagonal cut with black, white M/star
logo_anu = Image.new("RGBA", (140, 140), (0, 0, 0, 0))
d_anu = ImageDraw.Draw(logo_anu)
# red base with rounded corners
d_anu.rounded_rectangle([0, 0, 140, 140], radius=18, fill=(190, 30, 30, 255))
# black diagonal cut: polygon [140,0 140,140 0,140]
d_anu.polygon([(140, 0), (140, 140), (0, 140)], fill=(17, 17, 17, 255))
# fine separator line
d_anu.line([(0, 140), (140, 0)], fill=(255, 255, 255, 120), width=2)

# White M-like symbol coordinates scaled from original 400x400:
# [80,320 80,100 130,100 200,220 270,100 320,100 320,320 270,320 270,180 200,300 130,180 130,320]
scale = 140.0 / 400.0
pts = [
    (80*scale, 320*scale), (80*scale, 100*scale), (130*scale, 100*scale),
    (200*scale, 220*scale), (270*scale, 100*scale), (320*scale, 100*scale),
    (320*scale, 320*scale), (270*scale, 320*scale), (270*scale, 180*scale),
    (200*scale, 300*scale), (130*scale, 180*scale), (130*scale, 320*scale)
]
d_anu.polygon(pts, fill=(255, 255, 255, 255))

# Paste AnU logo at (60, 50)
composite.paste(logo_anu, (60, 50), logo_anu)

# 3. Typography drawing
draw = ImageDraw.Draw(composite)

# Try system fonts
font_paths = [
    "/usr/share/fonts/google-noto/NotoSans-Bold.ttf",
    "/usr/share/fonts/dejavu-sans-fonts/DejaVuSans-Bold.ttf",
    "/usr/share/fonts/liberation-sans/LiberationSans-Bold.ttf",
]
fpath = "/usr/share/fonts/dejavu-sans-fonts/DejaVuSans-Bold.ttf"
for p in font_paths:
    if os.path.exists(p):
        fpath = p
        break

f_super = ImageFont.truetype(fpath, 18)
f_h1 = ImageFont.truetype(fpath, 60)
f_sub = ImageFont.truetype(fpath, 28)
f_footer = ImageFont.truetype(fpath, 15)

# Badge next to logo
draw.rectangle([220, 62, 530, 96], fill=(211, 41, 32, 240))
draw.text((234, 70), "JEUNESSE LIBERTAIRE · AnU", fill=(255, 255, 255), font=f_super)

draw.text((220, 110), "MÉDIA D'ACTION DIRECTE & D'ÉDUCATION POPULAIRE", fill=(215, 209, 200), font=f_super)

# Headline
draw.text((60, 230), "PRENDRE LA PAROLE.", fill=(255, 255, 255), font=f_h1)
# Shadow / stroke for punch
draw.text((60, 305), "CONSTRUIRE L'AVENIR !", fill=(211, 41, 32), font=f_h1)

# Solid highlight bar
draw.rectangle([60, 395, 340, 399], fill=(211, 41, 32))

# Subtitles / bullets
draw.text((60, 425), "— Face à l'État et au capital : auto-organisation des facs et des lycées", fill=(246, 243, 237), font=f_sub)
draw.text((60, 475), "— Grèves, blocages, assemblées générales : la lutte sans compromis", fill=(246, 243, 237), font=f_sub)
draw.text((60, 525), "— Un espace commun, autogéré et sans frontières", fill=(215, 209, 200), font=f_sub)

# Bottom bar
draw.rectangle([0, 625, W, 675], fill=(18, 17, 15, 255))
draw.line([(0, 625), (W, 625)], fill=(211, 41, 32), width=3)
draw.text((60, 640), "Ⓐ  JEUNESSE LIBERTAIRE — UNION LIBERTAIRE ANARCHISTE (AnU)", fill=(246, 243, 237), font=f_footer)
draw.text((W - 320, 640), "CC BY-SA 4.0 · MÉDIA COMBATIF", fill=(170, 165, 156), font=f_footer)

final_img = composite.convert("RGB")
final_img.save("/home/anar/jeunesse-libertaire/assets/bienvenue-miniature.png", quality=95)
print("MINIATURE_GENERATED_SUCCESSFULLY")
