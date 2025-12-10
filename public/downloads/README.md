# Downloads Verzeichnis

Dieses Verzeichnis enthält herunterladbare Dateien für die Website.

## Dateien

| Datei | Beschreibung | Status |
|-------|--------------|--------|
| `avv-leadbusiness.pdf` | Auftragsverarbeitungsvertrag (AVV) gemäß Art. 28 DSGVO | ⏳ Manuell hochladen |

## Upload-Anleitung

1. Das PDF `avv-leadbusiness.pdf` wurde von Claude generiert
2. Lade es herunter und uploade es in dieses Verzeichnis (`public/downloads/`)
3. Die AVV-Seite (`/avv.php`) verlinkt automatisch auf `/downloads/avv-leadbusiness.pdf`

## Hinweis

PDFs können nicht direkt über die GitHub API hochgeladen werden, daher muss das PDF manuell über das GitHub Web-Interface oder per Git CLI hochgeladen werden:

```bash
# Lokales Klonen
git clone https://github.com/[your-repo]/leadbusiness.git
cd leadbusiness

# PDF kopieren
cp /pfad/zum/avv-leadbusiness.pdf public/downloads/

# Commit und Push
git add public/downloads/avv-leadbusiness.pdf
git commit -m "Add: AVV PDF document"
git push origin main
```
