<?php
/**
 * Leadbusiness - Impressum
 */

$pageTitle = 'Impressum';
$metaDescription = 'Impressum von Leadbusiness - Angaben gemäß § 5 TMG.';
$currentPage = 'impressum';

require_once __DIR__ . '/../templates/marketing/header.php';
?>

<section class="py-20 bg-white">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl font-extrabold mb-8">Impressum</h1>
        
        <div class="prose prose-lg max-w-none">
            
            <h2>Angaben gemäß § 5 TMG</h2>
            
            <p>
                <strong>Leadbusiness GmbH</strong><br>
                Musterstraße 123<br>
                10115 Berlin<br>
                Deutschland
            </p>
            
            <h3>Vertreten durch</h3>
            <p>
                Geschäftsführer: Max Mustermann
            </p>
            
            <h3>Kontakt</h3>
            <p>
                Telefon: +49 30 123 456 789<br>
                E-Mail: info@leadbusiness.de
            </p>
            
            <h3>Registereintrag</h3>
            <p>
                Eintragung im Handelsregister.<br>
                Registergericht: Amtsgericht Berlin-Charlottenburg<br>
                Registernummer: HRB 123456
            </p>
            
            <h3>Umsatzsteuer-ID</h3>
            <p>
                Umsatzsteuer-Identifikationsnummer gemäß § 27 a Umsatzsteuergesetz:<br>
                DE 123 456 789
            </p>
            
            <h3>Verantwortlich für den Inhalt nach § 55 Abs. 2 RStV</h3>
            <p>
                Max Mustermann<br>
                Musterstraße 123<br>
                10115 Berlin
            </p>
            
            <h2>Streitschlichtung</h2>
            <p>
                Die Europäische Kommission stellt eine Plattform zur Online-Streitbeilegung (OS) bereit: 
                <a href="https://ec.europa.eu/consumers/odr/" target="_blank" rel="noopener">https://ec.europa.eu/consumers/odr/</a>
            </p>
            <p>
                Unsere E-Mail-Adresse finden Sie oben im Impressum.
            </p>
            <p>
                Wir sind nicht bereit oder verpflichtet, an Streitbeilegungsverfahren vor einer 
                Verbraucherschlichtungsstelle teilzunehmen.
            </p>
            
            <h2>Haftung für Inhalte</h2>
            <p>
                Als Diensteanbieter sind wir gemäß § 7 Abs.1 TMG für eigene Inhalte auf diesen Seiten 
                nach den allgemeinen Gesetzen verantwortlich. Nach §§ 8 bis 10 TMG sind wir als 
                Diensteanbieter jedoch nicht verpflichtet, übermittelte oder gespeicherte fremde 
                Informationen zu überwachen oder nach Umständen zu forschen, die auf eine rechtswidrige 
                Tätigkeit hinweisen.
            </p>
            <p>
                Verpflichtungen zur Entfernung oder Sperrung der Nutzung von Informationen nach den 
                allgemeinen Gesetzen bleiben hiervon unberührt. Eine diesbezügliche Haftung ist jedoch 
                erst ab dem Zeitpunkt der Kenntnis einer konkreten Rechtsverletzung möglich. Bei 
                Bekanntwerden von entsprechenden Rechtsverletzungen werden wir diese Inhalte umgehend entfernen.
            </p>
            
            <h2>Haftung für Links</h2>
            <p>
                Unser Angebot enthält Links zu externen Websites Dritter, auf deren Inhalte wir keinen 
                Einfluss haben. Deshalb können wir für diese fremden Inhalte auch keine Gewähr übernehmen. 
                Für die Inhalte der verlinkten Seiten ist stets der jeweilige Anbieter oder Betreiber der 
                Seiten verantwortlich.
            </p>
            <p>
                Die verlinkten Seiten wurden zum Zeitpunkt der Verlinkung auf mögliche Rechtsverstöße 
                überprüft. Rechtswidrige Inhalte waren zum Zeitpunkt der Verlinkung nicht erkennbar. 
                Eine permanente inhaltliche Kontrolle der verlinkten Seiten ist jedoch ohne konkrete 
                Anhaltspunkte einer Rechtsverletzung nicht zumutbar. Bei Bekanntwerden von 
                Rechtsverletzungen werden wir derartige Links umgehend entfernen.
            </p>
            
            <h2>Urheberrecht</h2>
            <p>
                Die durch die Seitenbetreiber erstellten Inhalte und Werke auf diesen Seiten unterliegen 
                dem deutschen Urheberrecht. Die Vervielfältigung, Bearbeitung, Verbreitung und jede Art 
                der Verwertung außerhalb der Grenzen des Urheberrechtes bedürfen der schriftlichen 
                Zustimmung des jeweiligen Autors bzw. Erstellers. Downloads und Kopien dieser Seite sind 
                nur für den privaten, nicht kommerziellen Gebrauch gestattet.
            </p>
            <p>
                Soweit die Inhalte auf dieser Seite nicht vom Betreiber erstellt wurden, werden die 
                Urheberrechte Dritter beachtet. Insbesondere werden Inhalte Dritter als solche 
                gekennzeichnet. Sollten Sie trotzdem auf eine Urheberrechtsverletzung aufmerksam werden, 
                bitten wir um einen entsprechenden Hinweis. Bei Bekanntwerden von Rechtsverletzungen 
                werden wir derartige Inhalte umgehend entfernen.
            </p>
            
        </div>
        
        <div class="mt-12 pt-8 border-t">
            <p class="text-gray-500 text-sm">Stand: <?= date('F Y') ?></p>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../templates/marketing/footer.php'; ?>
