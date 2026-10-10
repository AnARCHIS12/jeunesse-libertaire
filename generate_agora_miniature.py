import os
from PIL import Image, ImageDraw, ImageFont, ImageEnhance

W, H = 1200, 675

# 1. Base photo: rush_a.jpg (Student assembly, speeches and debate outside school)
base_path = "/tmp/frames_search/rush_a.jpg"
img_action = Image.open(base_path).convert("RGBA")

# Crop the action area: crowd, speaker, signs, assembly
crop_y = 860
crop_h = int(1080 * (H / W))
crop_box = (0, crop_y, 1080, crop_y + crop_h)
action_crop = img_action.crop(crop_box).resize((W, H), Image.Resampling.LANCZOS)

# Enhance contrast & warm rebellious tones
action_crop = ImageEnhance.Contrast(action_crop).enhance(1.25)
action_crop = ImageEnhance.Color(action_crop).enhance(1.2)

# 2. Cinematic gradient / vignette for flawless readability
gradient = Image.new("RGBA", (W, H), (0, 0, 0, 0))
d_grad = ImageDraw.Draw(gradient)

# Left gradient from black (x=0 to x=820)
for x in range(W):
    if x < 840:
        alpha = int(245 * ((1.0 - (x / 840.0)) ** 1.35))
        d_grad.line([(x, 0), (x, H)], fill=(12, 11, 10, alpha))

# Bottom subtle shadow (y=440 to H)
for y in range(440, H):
    alpha_b = int(230 * (((y - 440) / (H - 440)) ** 1.25))
    d_grad.line([(0, y), (W, y)], fill=(12, 11, 10, alpha_b))

# Red energy bar on left border
d_grad.rectangle([0, 0, 16, H], fill=(211, 41, 32, 255))

composite = Image.alpha_composite(action_crop, gradient)

# 3. Add Authentic Jeunesse Libertaire Ⓐ Logo with antialiased circular mask
logo_src = Image.open("/home/anar/jeunesse-libertaire/assets/avatar-reseaux-noir.png").convert("RGBA")
logo_resized = logo_src.resize((135, 135), Image.Resampling.LANCZOS)

# Antialiased circular mask (drawn at 4x then downscaled)
mask_hi = Image.new("L", (135 * 4, 135 * 4), 0)
d_mask = ImageDraw.Draw(mask_hi)
d_mask.ellipse((0, 0, 135 * 4, 135 * 4), fill=255)
mask = mask_hi.resize((135, 135), Image.Resampling.LANCZOS)

# Create a clean circular badge with border
badge_base = Image.new("RGBA", (139, 139), (0, 0, 0, 0))
d_badge = ImageDraw.Draw(badge_base)
# Red circular border ring
d_badge.ellipse((0, 0, 138, 138), fill=(211, 41, 32, 255))
badge_base.paste(logo_resized, (2, 2), mask)

# Paste logo at (48, 44)
composite.paste(badge_base, (48, 44), badge_base)

# 4. Typography setup
draw = ImageDraw.Draw(composite)

font_black = "/usr/share/fonts/google-noto/NotoSans-Black.ttf"
font_bold = "/usr/share/fonts/google-noto/NotoSans-Bold.ttf"

f_badge = ImageFont.truetype(font_bold, 17)
f_subhead = ImageFont.truetype(font_bold, 15)
f_title1 = ImageFont.truetype(font_black, 58)
f_title2 = ImageFont.truetype(font_black, 58)
f_lead = ImageFont.truetype(font_bold, 28)
f_sub = ImageFont.truetype(font_bold, 22)
f_tags = ImageFont.truetype(font_bold, 17)

# Top badge
draw.rectangle([205, 62, 590, 98], fill=(211, 41, 32, 255))
draw.text((220, 70), "JEUNESSE LIBERTAIRE · DÉBATS", fill=(255, 255, 255), font=f_badge)

draw.text((205, 114), "ESPACE AGORA & EXPRESSION DIRECTE", fill=(240, 235, 226), font=f_subhead)

# Main Title with drop-shadow
shadow_offset = 3
draw.text((48 + shadow_offset, 230 + shadow_offset), "TRIBUNE LIBRE & AGORA", fill=(0, 0, 0, 220), font=f_title1)
draw.text((48, 230), "TRIBUNE LIBRE & AGORA", fill=(255, 255, 255), font=f_title1)

draw.text((48 + shadow_offset, 310 + shadow_offset), "DÉBATS & LUTTES EN DIRECT", fill=(0, 0, 0, 220), font=f_title2)
draw.text((48, 310), "DÉBATS & LUTTES EN DIRECT", fill=(211, 41, 32), font=f_title2)

# Red accent underline bar
draw.rectangle([48, 400, 360, 406], fill=(211, 41, 32))

# Sub-bullets / Punchlines
draw.text((48, 435), "Prendre la parole · Confronter les idées · S'auto-organiser", fill=(255, 255, 255), font=f_lead)
draw.text((48, 485), "Ouvert à toutes les compagnes et compagnons de lutte", fill=(235, 226, 215), font=f_sub)

# Bottom decorative tags strip
draw.text((48, 565), "• Assemblées Générales • Démocratie Directe • Récits d'Action • Pensée Critique", fill=(200, 188, 172), font=f_tags)

# 5. Export
final_img = composite.convert("RGB")

# Target output files
out_assets = "/home/anar/jeunesse-libertaire/assets/agora-miniature.png"
out_artifact = "/home/anar/.gemini/antigravity/brain/3919ac62-a110-476a-b3b0-7d3f0aa9bb22/agora-miniature.png"

final_img.save(out_assets, quality=95)
final_img.save(out_artifact, quality=95)

print("AGORA_MINIATURE_SUCCESS")
