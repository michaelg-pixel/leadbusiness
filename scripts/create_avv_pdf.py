#!/usr/bin/env python3
"""
Leadbusiness - AVV PDF Generator
Erstellt ein professionelles PDF des Auftragsverarbeitungsvertrags

Verwendung:
    pip install reportlab
    python create_avv_pdf.py

Das generierte PDF in public/downloads/ kopieren.
"""

from reportlab.lib.pagesizes import A4
from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
from reportlab.lib.units import cm
from reportlab.lib.colors import HexColor, white
from reportlab.lib.enums import TA_CENTER, TA_JUSTIFY
from reportlab.platypus import SimpleDocTemplate, Paragraph, Spacer, PageBreak, Table, TableStyle
from datetime import datetime

# Farben
PRIMARY = HexColor('#667eea')
DARK = HexColor('#1e293b')
GRAY = HexColor('#64748b')
LIGHT = HexColor('#f1f5f9')
BORDER = HexColor('#e2e8f0')

# Layout
W, H = A4
M = 2.5 * cm

class AVVGenerator:
    def __init__(self, path):
        self.path = path
        self.styles = getSampleStyleSheet()
        self.story = []
        self._setup()
    
    def _setup(self):
        self.styles.add(ParagraphStyle('Title', fontName='Helvetica-Bold', fontSize=24, alignment=TA_CENTER, textColor=DARK, spaceAfter=20))
        self.styles.add(ParagraphStyle('Sub', fontName='Helvetica', fontSize=14, alignment=TA_CENTER, textColor=GRAY, spaceAfter=40))
        self.styles.add(ParagraphStyle('Sec', fontName='Helvetica-Bold', fontSize=14, textColor=PRIMARY, spaceBefore=25, spaceAfter=12))
        self.styles.add(ParagraphStyle('SSec', fontName='Helvetica-Bold', fontSize=11, textColor=DARK, spaceBefore=15, spaceAfter=8))
        self.styles.add(ParagraphStyle('Body', fontName='Helvetica', fontSize=10, leading=14, alignment=TA_JUSTIFY, textColor=DARK, spaceAfter=8))
        self.styles.add(ParagraphStyle('Bullet', fontName='Helvetica', fontSize=10, leading=14, textColor=DARK, leftIndent=15, spaceAfter=4))
    
    def sec(self, t): self.story.append(Paragraph(t, self.styles['Sec']))
    def ssec(self, t): self.story.append(Paragraph(t, self.styles['SSec']))
    def p(self, t): self.story.append(Paragraph(t, self.styles['Body']))
    def ul(self, items):
        for i in items: self.story.append(Paragraph(f"• {i}", self.styles['Bullet']))
        self.story.append(Spacer(1, 8))
    
    def box(self, lines):
        t = Table([[Paragraph("<br/>".join(lines), ParagraphStyle('B', fontSize=10, leading=14, textColor=DARK))]], colWidths=[14*cm])
        t.setStyle(TableStyle([('BACKGROUND', (0,0), (-1,-1), LIGHT), ('BOX', (0,0), (-1,-1), 1, BORDER), ('LEFTPADDING', (0,0), (-1,-1), 15), ('RIGHTPADDING', (0,0), (-1,-1), 15), ('TOPPADDING', (0,0), (-1,-1), 12), ('BOTTOMPADDING', (0,0), (-1,-1), 12)]))
        self.story.append(t)
        self.story.append(Spacer(1, 12))
    
    def tbl(self, h, r):
        d = [h] + r
        t = Table(d, colWidths=[14*cm/len(h)]*len(h))
        t.setStyle(TableStyle([('FONTNAME', (0,0), (-1,0), 'Helvetica-Bold'), ('FONTSIZE', (0,0), (-1,-1), 9), ('TEXTCOLOR', (0,0), (-1,0), white), ('BACKGROUND', (0,0), (-1,0), PRIMARY), ('GRID', (0,0), (-1,-1), 0.5, BORDER), ('ROWBACKGROUNDS', (0,1), (-1,-1), [white, LIGHT]), ('LEFTPADDING', (0,0), (-1,-1), 8), ('TOPPADDING', (0,0), (-1,-1), 8), ('BOTTOMPADDING', (0,0), (-1,-1), 8)]))
        self.story.append(t)
        self.story.append(Spacer(1, 15))
    
    def title_page(self):
        self.story.append(Spacer(1, 4*cm))
        self.story.append(Paragraph("LEADBUSINESS", ParagraphStyle('L', fontName='Helvetica-Bold', fontSize=16, textColor=PRIMARY, alignment=TA_CENTER, spaceAfter=10)))
        self.story.append(Paragraph("empfehlungen.cloud", ParagraphStyle('LS', fontName='Helvetica', fontSize=12, textColor=GRAY, alignment=TA_CENTER, spaceAfter=60)))
        self.story.append(Paragraph("Auftragsverarbeitungsvertrag", self.styles['Title']))
        self.story.append(Paragraph("gemäß Art. 28 DSGVO", self.styles['Sub']))
        self.story.append(Spacer(1, 20))
        t = Table([['']],colWidths=[12*cm])
        t.setStyle(TableStyle([('LINEABOVE', (0,0), (-1,-1), 2, PRIMARY)]))
        self.story.append(t)
        self.story.append(Spacer(1, 40))
        m = Table([['Version:', '1.0'], ['Stand:', datetime.now().strftime('%B %Y')], ['Gültig ab:', datetime.now().strftime('%d.%m.%Y')]], colWidths=[4*cm, 6*cm])
        m.setStyle(TableStyle([('FONTNAME', (0,0), (0,-1), 'Helvetica-Bold'), ('FONTSIZE', (0,0), (-1,-1), 10), ('TEXTCOLOR', (0,0), (0,-1), GRAY), ('ALIGN', (0,0), (0,-1), 'RIGHT'), ('BOTTOMPADDING', (0,0), (-1,-1), 8)]))
        self.story.append(m)
        self.story.append(Spacer(1, 80))
        h = Table([[Paragraph("<b>Hinweis:</b> Dieser AVV wird automatisch Bestandteil des Vertrags bei Abschluss eines Leadbusiness-Abonnements.", ParagraphStyle('H', fontSize=9, leading=13, textColor=HexColor('#1e40af')))]], colWidths=[14*cm])
        h.setStyle(TableStyle([('BACKGROUND', (0,0), (-1,-1), HexColor('#eff6ff')), ('BOX', (0,0), (-1,-1), 1, HexColor('#3b82f6')), ('LEFTPADDING', (0,0), (-1,-1), 15), ('RIGHTPADDING', (0,0), (-1,-1), 15), ('TOPPADDING', (0,0), (-1,-1), 12), ('BOTTOMPADDING', (0,0), (-1,-1), 12)]))
        self.story.append(h)
        self.story.append(PageBreak())
    
    def content(self):
        self.sec("Präambel")
        self.p("Der vorliegende Auftragsverarbeitungsvertrag regelt die Rechte und Pflichten der Parteien im Hinblick auf die Verarbeitung personenbezogener Daten im Rahmen der Nutzung der SaaS-Plattform Leadbusiness (empfehlungen.cloud).")
        
        self.sec("§ 1 Vertragsparteien")
        self.ssec("1.1 Auftraggeber")
        self.p("Der Kunde, der die Dienste von Leadbusiness zur Durchführung eines Empfehlungsprogramms nutzt.")
        self.ssec("1.2 Auftragnehmer")
        self.box(["<b>Leadbusiness / empfehlungen.cloud</b>", "Betrieben von: [Ihr Unternehmen]", "[Ihre Adresse]", "E-Mail: datenschutz@empfehlungen.cloud"])
        
        self.sec("§ 2 Gegenstand und Dauer")
        self.ssec("2.1 Gegenstand")
        self.p("Der Auftragnehmer verarbeitet im Auftrag des Auftraggebers personenbezogene Daten im Rahmen der Bereitstellung der Leadbusiness-Plattform.")
        self.ssec("2.2 Dauer")
        self.p("Die Verarbeitung beginnt mit Abschluss des Hauptvertrags und endet mit dessen Beendigung, zuzüglich gesetzlicher Aufbewahrungspflichten.")
        
        self.sec("§ 3 Art und Zweck der Verarbeitung")
        self.p("Die Verarbeitung umfasst:")
        self.ul(["Speicherung von Teilnehmerdaten (Leads)", "Versand von E-Mail-Benachrichtigungen", "Tracking von Empfehlungen und Conversions", "Verwaltung von Belohnungen", "Bereitstellung von Statistiken", "Betrugsprävention"])
        
        self.sec("§ 4 Art der personenbezogenen Daten")
        self.ul(["<b>Kontaktdaten:</b> E-Mail-Adresse, Name, Telefonnummer", "<b>Empfehlungsdaten:</b> Referral-Code, Empfehlungen, Belohnungen", "<b>Technische Daten:</b> IP-Adresse (anonymisiert), Zeitstempel", "<b>Kommunikationsdaten:</b> E-Mail-Versandhistorie"])
        
        self.sec("§ 5 Kategorien betroffener Personen")
        self.ul(["Teilnehmer des Empfehlungsprogramms (Leads/Empfehler)", "Personen über Empfehlungslinks"])
        
        self.sec("§ 6 Pflichten des Auftragnehmers")
        self.ssec("6.1 Weisungsgebundenheit")
        self.p("Der Auftragnehmer verarbeitet Daten ausschließlich auf dokumentierte Weisung des Auftraggebers.")
        self.ssec("6.2 Vertraulichkeit")
        self.p("Alle befugten Personen sind zur Vertraulichkeit verpflichtet.")
        self.ssec("6.3 Technische und organisatorische Maßnahmen")
        self.ul(["Verschlüsselung (TLS/HTTPS)", "Regelmäßige Backups", "Zugriffskontrolle", "Multi-Tenant-Isolation"])
        self.ssec("6.4 Unterauftragsverarbeiter")
        self.tbl(['Unterauftragsverarbeiter', 'Tätigkeit', 'Standort'], [['Hostinger International Ltd.', 'Webhosting', 'EU'], ['Mailgun Technologies', 'E-Mail-Versand', 'EU (Frankfurt)']])
        self.ssec("6.5 Unterstützung")
        self.p("Der Auftragnehmer unterstützt bei Anfragen betroffener Personen und Datenschutz-Folgenabschätzungen.")
        self.ssec("6.6 Datenschutzverletzungen")
        self.p("Meldung innerhalb von 24 Stunden nach Kenntniserlangung.")
        self.ssec("6.7 Löschung")
        self.p("Nach Vertragsende werden alle Daten gelöscht, sofern keine gesetzliche Aufbewahrungspflicht besteht.")
        
        self.sec("§ 7 Pflichten des Auftraggebers")
        self.ul(["Datenschutzrechtliche Zulässigkeit sicherstellen", "Betroffene informieren", "Einwilligungen einholen (Double-Opt-In)"])
        
        self.sec("§ 8 Kontrollrechte")
        self.p("Der Auftraggeber ist berechtigt, die Einhaltung der Maßnahmen zu überprüfen.")
        
        self.sec("§ 9 Haftung")
        self.p("Die Haftung richtet sich nach den gesetzlichen Bestimmungen der DSGVO.")
        
        self.sec("§ 10 Schlussbestimmungen")
        self.p("Änderungen bedürfen der Schriftform. Es gilt deutsches Recht.")
        
        self.story.append(PageBreak())
        
        self.sec("Anlage 1: Technische und organisatorische Maßnahmen (TOM)")
        for t, l in [("1. Zutrittskontrolle", ["24/7-Überwachung in EU-Rechenzentren"]), ("2. Zugangskontrolle", ["Passwortschutz", "Zwei-Faktor-Authentifizierung", "Session-Timeouts"]), ("3. Zugriffskontrolle", ["Rollenbasiertes Berechtigungskonzept", "Mandantentrennung"]), ("4. Weitergabekontrolle", ["TLS 1.2/1.3 Verschlüsselung"]), ("5. Eingabekontrolle", ["Protokollierung", "Audit-Logs"]), ("6. Verfügbarkeitskontrolle", ["Tägliche Backups", "Redundante Speicherung"]), ("7. Trennungskontrolle", ["Logische Datentrennung", "Separate Subdomains"])]:
            self.ssec(t)
            self.ul(l)
        
        self.story.append(Spacer(1, 30))
        self.story.append(Paragraph(f"Stand: {datetime.now().strftime('%B %Y')} | Version 1.0", ParagraphStyle('V', fontSize=9, textColor=GRAY, alignment=TA_CENTER)))
    
    def page_num(self, c, d):
        c.saveState()
        n = c.getPageNumber()
        if n > 1:
            c.setFont('Helvetica', 8)
            c.setFillColor(GRAY)
            c.drawCentredString(W/2, 1.5*cm, f"Seite {n}")
            c.drawString(M, H-1.5*cm, "Auftragsverarbeitungsvertrag | Leadbusiness")
            c.drawRightString(W-M, H-1.5*cm, "empfehlungen.cloud")
            c.setStrokeColor(BORDER)
            c.line(M, H-1.8*cm, W-M, H-1.8*cm)
        c.restoreState()
    
    def generate(self):
        doc = SimpleDocTemplate(self.path, pagesize=A4, leftMargin=M, rightMargin=M, topMargin=M, bottomMargin=M, title="AVV - Leadbusiness", author="Leadbusiness", subject="AVV gemäß Art. 28 DSGVO")
        self.title_page()
        self.content()
        doc.build(self.story, onFirstPage=self.page_num, onLaterPages=self.page_num)
        print(f"✅ PDF erstellt: {self.path}")

if __name__ == "__main__":
    AVVGenerator("avv-leadbusiness.pdf").generate()
