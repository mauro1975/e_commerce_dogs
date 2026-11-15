import os

import fitz

BASE = r"c:\Users\mauro\Downloads"
FILES = ["Foto.pdf", "Foto-1.pdf", "Foto-2.pdf", "Foto-3.pdf"]
OUT = r"c:\xampp\htdocs\e_commerce_dogs\public\images\size-guide"

os.makedirs(OUT, exist_ok=True)

for f in FILES:
    path = os.path.join(BASE, f)
    if not os.path.isfile(path):
        print("missing", path)
        continue
    doc = fitz.open(path)
    for i, page in enumerate(doc):
        pix = page.get_pixmap(matrix=fitz.Matrix(2, 2))
        stem = f.replace(".pdf", "")
        name = f"{stem}_p{i}.png" if doc.page_count > 1 else f"{stem}.png"
        pix.save(os.path.join(OUT, name))
    text = "".join(page.get_text() for page in doc)
    print(f, "pages", doc.page_count, "text", len(text))
    if text.strip():
        print(text[:800])