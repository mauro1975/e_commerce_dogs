from PIL import Image
import numpy as np

OUT = r"c:\xampp\htdocs\e_commerce_dogs\public\images\size-guide\dog-profile-outline.png"
SRC = OUT

im = Image.open(SRC).convert("RGB")
arr = np.array(im)
h, w = arr.shape[:2]
dark = (arr[..., 0] < 60) & (arr[..., 1] < 60) & (arr[..., 2] < 60)


def is_frame(r: int, c: int) -> bool:
    return bool(dark[r, c] and (dark[r].sum() > w * 0.45 or dark[:, c].sum() > h * 0.45))


coords = [(r, c) for r in range(h) for c in range(w) if dark[r, c] and not is_frame(r, c)]
ys = [p[0] for p in coords]
xs = [p[1] for p in coords]
pad = 6
x0, y0, x1, y1 = max(0, min(xs) - pad), max(0, min(ys) - pad), min(w, max(xs) + pad + 1), min(h, max(ys) + pad + 1)
cropped = arr[y0:y1, x0:x1].copy()
ch, cw = cropped.shape[:2]
dg = (cropped[..., 0] < 80) & (cropped[..., 1] < 80) & (cropped[..., 2] < 80)
for r in range(ch):
    if dg[r].sum() > cw * 0.65:
        dg[r, :] = False
for c in range(cw):
    if dg[:, c].sum() > ch * 0.65:
        dg[:, c] = False
white = np.full_like(cropped, 255)
white[dg] = cropped[dg]
final = Image.fromarray(white.astype(np.uint8))
final.save(OUT, optimize=True)
print(final.size)