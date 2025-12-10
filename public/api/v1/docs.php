<?php
/**
 * API v1 - Dokumentation
 */

$pageTitle = 'API Dokumentation';
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - Leadbusiness API</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        code, pre { font-family: 'JetBrains Mono', monospace; }
        .method-get { background: #3b82f6; }
        .method-post { background: #22c55e; }
        .method-put { background: #f59e0b; }
        .method-delete { background: #ef4444; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen">
    
    <!-- Header -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-code text-white"></i>
                    </div>
                    <div>
                        <h1 class="font-bold text-slate-800">Leadbusiness API</h1>
                        <p class="text-xs text-slate-500">Version 1.0</p>
                    </div>
                </div>
                <a href="/dashboard/api.php" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 rounded-lg text-sm text-slate-700">
                    <i class="fas fa-key mr-2"></i>API Keys verwalten
                </a>
            </div>
        </div>
    </header>
    
    <div class="max-w-6xl mx-auto px-6 py-8">
        <div class="grid lg:grid-cols-4 gap-8">
            
            <!-- Sidebar Navigation -->
            <nav class="lg:col-span-1">
                <div class="sticky top-24 space-y-6">
                    <div>
                        <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Erste Schritte</h3>
                        <ul class="space-y-2">
                            <li><a href="#introduction" class="text-slate-600 hover:text-blue-600">Einführung</a></li>
                            <li><a href="#authentication" class="text-slate-600 hover:text-blue-600">Authentifizierung</a></li>
                            <li><a href="#rate-limiting" class="text-slate-600 hover:text-blue-600">Rate Limiting</a></li>
                            <li><a href="#errors" class="text-slate-600 hover:text-blue-600">Fehlerbehandlung</a></li>
                        </ul>
                    </div>
                    
                    <div>
                        <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Endpoints</h3>
                        <ul class="space-y-2">
                            <li><a href="#leads" class="text-slate-600 hover:text-blue-600">Leads</a></li>
                            <li><a href="#referrers" class="text-slate-600 hover:text-blue-600">Empfehler</a></li>
                            <li><a href="#stats" class="text-slate-600 hover:text-blue-600">Statistiken</a></li>
                            <li><a href="#rewards" class="text-slate-600 hover:text-blue-600">Belohnungen</a></li>
                        </ul>
                    </div>
                    
                    <div>
                        <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Webhooks</h3>
                        <ul class="space-y-2">
                            <li><a href="#webhooks" class="text-slate-600 hover:text-blue-600">Übersicht</a></li>
                            <li><a href="#webhook-events" class="text-slate-600 hover:text-blue-600">Events</a></li>
                            <li><a href="#webhook-security" class="text-slate-600 hover:text-blue-600">Signatur-Verifizierung</a></li>
                        </ul>
                    </div>
                </div>
            </nav>
            
            <!-- Main Content -->
            <main class="lg:col-span-3 space-y-12">
                
                <!-- Introduction -->
                <section id="introduction">
                    <h2 class="text-2xl font-bold text-slate-800 mb-4">Einführung</h2>
                    <div class="prose prose-slate max-w-none">
                        <p class="text-slate-600">
                            Die Leadbusiness API ermöglicht Ihnen den programmatischen Zugriff auf Ihre Empfehlungsprogramm-Daten. 
                            Sie können Leads abrufen und erstellen, Empfehler verwalten und Statistiken abfragen.
                        </p>
                        
                        <div class="bg-slate-800 rounded-xl p-4 mt-4">
                            <p class="text-slate-400 text-sm mb-2">Base URL</p>
                            <code class="text-green-400">https://www.empfehlungen.cloud/api/v1</code>
                        </div>
                    </div>
                </section>
                
                <!-- Authentication -->
                <section id="authentication">
                    <h2 class="text-2xl font-bold text-slate-800 mb-4">Authentifizierung</h2>
                    <div class="prose prose-slate max-w-none">
                        <p class="text-slate-600 mb-4">
                            Alle API-Requests müssen mit Ihrem API-Key und Secret-Key authentifiziert werden. 
                            Diese werden als HTTP-Header übermittelt.
                        </p>
                        
                        <div class="bg-slate-800 rounded-xl p-4">
                            <p class="text-slate-400 text-sm mb-2">Header</p>
                            <pre class="text-green-400 text-sm">X-API-Key: lb_xxxxxxxxxxxx
X-API-Secret: sk_xxxxxxxxxxxx</pre>
                        </div>
                        
                        <h3 class="text-lg font-semibold text-slate-800 mt-6 mb-3">Beispiel mit cURL</h3>
                        <div class="bg-slate-800 rounded-xl p-4">
                            <pre class="text-green-400 text-sm overflow-x-auto">curl -X GET "https://www.empfehlungen.cloud/api/v1/leads" \
  -H "X-API-Key: lb_xxxxxxxxxxxx" \
  -H "X-API-Secret: sk_xxxxxxxxxxxx"</pre>
                        </div>
                    </div>
                </section>
                
                <!-- Rate Limiting -->
                <section id="rate-limiting">
                    <h2 class="text-2xl font-bold text-slate-800 mb-4">Rate Limiting</h2>
                    <div class="prose prose-slate max-w-none">
                        <p class="text-slate-600 mb-4">
                            API-Requests sind je nach Plan limitiert:
                        </p>
                        
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="bg-slate-100">
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-slate-700">Plan</th>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-slate-700">Requests/Stunde</th>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-slate-700">API-Keys</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b border-slate-200">
                                    <td class="px-4 py-3 text-slate-600">Professional</td>
                                    <td class="px-4 py-3 text-slate-600">1.000</td>
                                    <td class="px-4 py-3 text-slate-600">3</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 text-slate-600">Enterprise</td>
                                    <td class="px-4 py-3 text-slate-600">10.000</td>
                                    <td class="px-4 py-3 text-slate-600">10</td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <p class="text-slate-600 mt-4">
                            Rate-Limit-Informationen werden in den Response-Headers zurückgegeben:
                        </p>
                        
                        <div class="bg-slate-800 rounded-xl p-4 mt-2">
                            <pre class="text-green-400 text-sm">X-RateLimit-Limit: 1000
X-RateLimit-Remaining: 999</pre>
                        </div>
                    </div>
                </section>
                
                <!-- Errors -->
                <section id="errors">
                    <h2 class="text-2xl font-bold text-slate-800 mb-4">Fehlerbehandlung</h2>
                    <div class="prose prose-slate max-w-none">
                        <p class="text-slate-600 mb-4">
                            Fehler werden als JSON mit entsprechendem HTTP-Statuscode zurückgegeben:
                        </p>
                        
                        <div class="bg-slate-800 rounded-xl p-4">
                            <pre class="text-green-400 text-sm">{
  "success": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Validation failed",
    "status": 400,
    "errors": ["email is required"]
  }
}</pre>
                        </div>
                        
                        <h3 class="text-lg font-semibold text-slate-800 mt-6 mb-3">Häufige Fehlercodes</h3>
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="bg-slate-100">
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-slate-700">Code</th>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-slate-700">HTTP</th>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-slate-700">Beschreibung</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                <tr class="border-b border-slate-200">
                                    <td class="px-4 py-2"><code class="text-red-600">API_KEY_MISSING</code></td>
                                    <td class="px-4 py-2">401</td>
                                    <td class="px-4 py-2 text-slate-600">API-Key oder Secret fehlt</td>
                                </tr>
                                <tr class="border-b border-slate-200">
                                    <td class="px-4 py-2"><code class="text-red-600">API_KEY_INVALID</code></td>
                                    <td class="px-4 py-2">401</td>
                                    <td class="px-4 py-2 text-slate-600">Ungültiger API-Key</td>
                                </tr>
                                <tr class="border-b border-slate-200">
                                    <td class="px-4 py-2"><code class="text-red-600">RATE_LIMIT_EXCEEDED</code></td>
                                    <td class="px-4 py-2">429</td>
                                    <td class="px-4 py-2 text-slate-600">Rate-Limit überschritten</td>
                                </tr>
                                <tr class="border-b border-slate-200">
                                    <td class="px-4 py-2"><code class="text-red-600">VALIDATION_ERROR</code></td>
                                    <td class="px-4 py-2">400</td>
                                    <td class="px-4 py-2 text-slate-600">Validierungsfehler</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2"><code class="text-red-600">NOT_FOUND</code></td>
                                    <td class="px-4 py-2">404</td>
                                    <td class="px-4 py-2 text-slate-600">Ressource nicht gefunden</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>
                
                <!-- Leads -->
                <section id="leads">
                    <h2 class="text-2xl font-bold text-slate-800 mb-4">Leads</h2>
                    
                    <!-- GET /leads -->
                    <div class="bg-white rounded-xl border border-slate-200 p-6 mb-4">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="method-get px-2 py-1 rounded text-xs font-bold text-white">GET</span>
                            <code class="text-slate-700">/leads</code>
                        </div>
                        <p class="text-slate-600 mb-4">Alle Leads abrufen (paginiert)</p>
                        
                        <h4 class="font-semibold text-slate-700 mb-2">Query Parameter</h4>
                        <table class="w-full text-sm mb-4">
                            <tr class="border-b border-slate-100">
                                <td class="py-2"><code>page</code></td>
                                <td class="py-2 text-slate-600">Seitennummer (Standard: 1)</td>
                            </tr>
                            <tr class="border-b border-slate-100">
                                <td class="py-2"><code>per_page</code></td>
                                <td class="py-2 text-slate-600">Einträge pro Seite (1-100, Standard: 50)</td>
                            </tr>
                            <tr class="border-b border-slate-100">
                                <td class="py-2"><code>status</code></td>
                                <td class="py-2 text-slate-600">Filter nach Status (new, contacted, converted)</td>
                            </tr>
                            <tr class="border-b border-slate-100">
                                <td class="py-2"><code>referrer_code</code></td>
                                <td class="py-2 text-slate-600">Filter nach Empfehler-Code</td>
                            </tr>
                            <tr>
                                <td class="py-2"><code>since</code></td>
                                <td class="py-2 text-slate-600">Leads ab Datum (ISO 8601)</td>
                            </tr>
                        </table>
                        
                        <h4 class="font-semibold text-slate-700 mb-2">Response</h4>
                        <div class="bg-slate-800 rounded-lg p-4">
                            <pre class="text-green-400 text-xs overflow-x-auto">{
  "success": true,
  "data": {
    "items": [
      {
        "id": 123,
        "name": "Max Mustermann",
        "email": "max@example.com",
        "phone": "+49123456789",
        "status": "new",
        "source": "website",
        "referrer": {
          "code": "ABC123",
          "name": "Anna Referrer"
        },
        "created_at": "2024-01-15T10:30:00Z"
      }
    ],
    "pagination": {
      "total": 150,
      "per_page": 50,
      "current_page": 1,
      "total_pages": 3,
      "has_more": true
    }
  }
}</pre>
                        </div>
                    </div>
                    
                    <!-- POST /leads -->
                    <div class="bg-white rounded-xl border border-slate-200 p-6 mb-4">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="method-post px-2 py-1 rounded text-xs font-bold text-white">POST</span>
                            <code class="text-slate-700">/leads</code>
                        </div>
                        <p class="text-slate-600 mb-4">Neuen Lead erstellen</p>
                        
                        <h4 class="font-semibold text-slate-700 mb-2">Request Body</h4>
                        <div class="bg-slate-800 rounded-lg p-4 mb-4">
                            <pre class="text-green-400 text-xs">{
  "name": "Max Mustermann",
  "email": "max@example.com",
  "phone": "+49123456789",
  "referrer_code": "ABC123",
  "source": "api"
}</pre>
                        </div>
                        
                        <table class="w-full text-sm">
                            <tr class="border-b border-slate-100">
                                <td class="py-2"><code>name</code> <span class="text-red-500">*</span></td>
                                <td class="py-2 text-slate-600">Name des Leads</td>
                            </tr>
                            <tr class="border-b border-slate-100">
                                <td class="py-2"><code>email</code> <span class="text-red-500">*</span></td>
                                <td class="py-2 text-slate-600">E-Mail-Adresse</td>
                            </tr>
                            <tr class="border-b border-slate-100">
                                <td class="py-2"><code>phone</code></td>
                                <td class="py-2 text-slate-600">Telefonnummer (optional)</td>
                            </tr>
                            <tr class="border-b border-slate-100">
                                <td class="py-2"><code>referrer_code</code></td>
                                <td class="py-2 text-slate-600">Code des Empfehlers (optional)</td>
                            </tr>
                            <tr>
                                <td class="py-2"><code>source</code></td>
                                <td class="py-2 text-slate-600">Quelle (optional, Standard: api)</td>
                            </tr>
                        </table>
                    </div>
                    
                    <!-- GET /leads/{id} -->
                    <div class="bg-white rounded-xl border border-slate-200 p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="method-get px-2 py-1 rounded text-xs font-bold text-white">GET</span>
                            <code class="text-slate-700">/leads/{id}</code>
                        </div>
                        <p class="text-slate-600">Einzelnen Lead abrufen</p>
                    </div>
                </section>
                
                <!-- Referrers -->
                <section id="referrers">
                    <h2 class="text-2xl font-bold text-slate-800 mb-4">Empfehler</h2>
                    
                    <div class="bg-white rounded-xl border border-slate-200 p-6 mb-4">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="method-get px-2 py-1 rounded text-xs font-bold text-white">GET</span>
                            <code class="text-slate-700">/referrers</code>
                        </div>
                        <p class="text-slate-600 mb-4">Alle Empfehler abrufen</p>
                        
                        <h4 class="font-semibold text-slate-700 mb-2">Response</h4>
                        <div class="bg-slate-800 rounded-lg p-4">
                            <pre class="text-green-400 text-xs overflow-x-auto">{
  "success": true,
  "data": {
    "items": [
      {
        "id": 1,
        "name": "Anna Referrer",
        "email": "anna@example.com",
        "referral_code": "ABC123",
        "stats": {
          "total_leads": 15,
          "conversions": 8,
          "points": 240,
          "streak_days": 5
        },
        "created_at": "2024-01-01T00:00:00Z"
      }
    ]
  }
}</pre>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-xl border border-slate-200 p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="method-get px-2 py-1 rounded text-xs font-bold text-white">GET</span>
                            <code class="text-slate-700">/referrers/{code}</code>
                        </div>
                        <p class="text-slate-600">Einzelnen Empfehler mit Badges und Rewards abrufen</p>
                    </div>
                </section>
                
                <!-- Stats -->
                <section id="stats">
                    <h2 class="text-2xl font-bold text-slate-800 mb-4">Statistiken</h2>
                    
                    <div class="bg-white rounded-xl border border-slate-200 p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="method-get px-2 py-1 rounded text-xs font-bold text-white">GET</span>
                            <code class="text-slate-700">/stats</code>
                        </div>
                        <p class="text-slate-600 mb-4">Umfassende Statistiken abrufen</p>
                        
                        <h4 class="font-semibold text-slate-700 mb-2">Query Parameter</h4>
                        <table class="w-full text-sm mb-4">
                            <tr>
                                <td class="py-2"><code>days</code></td>
                                <td class="py-2 text-slate-600">Zeitraum in Tagen (1-365, Standard: 30)</td>
                            </tr>
                        </table>
                        
                        <h4 class="font-semibold text-slate-700 mb-2">Response enthält</h4>
                        <ul class="list-disc list-inside text-slate-600 text-sm space-y-1">
                            <li>Gesamt-Statistiken (Leads, Conversions, Empfehler)</li>
                            <li>Tägliche Aufschlüsselung (für Charts)</li>
                            <li>Top 10 Empfehler</li>
                            <li>Lead-Quellen</li>
                        </ul>
                    </div>
                </section>
                
                <!-- Rewards -->
                <section id="rewards">
                    <h2 class="text-2xl font-bold text-slate-800 mb-4">Belohnungen</h2>
                    
                    <div class="bg-white rounded-xl border border-slate-200 p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="method-get px-2 py-1 rounded text-xs font-bold text-white">GET</span>
                            <code class="text-slate-700">/rewards</code>
                        </div>
                        <p class="text-slate-600">Alle konfigurierten Belohnungsstufen abrufen (nur lesen)</p>
                    </div>
                </section>
                
                <!-- Webhooks -->
                <section id="webhooks">
                    <h2 class="text-2xl font-bold text-slate-800 mb-4">Webhooks</h2>
                    <div class="prose prose-slate max-w-none">
                        <p class="text-slate-600 mb-4">
                            Webhooks ermöglichen es Ihnen, bei bestimmten Events in Echtzeit benachrichtigt zu werden.
                            Konfigurieren Sie Webhooks im <a href="/dashboard/webhooks.php" class="text-blue-600 hover:underline">Dashboard</a>.
                        </p>
                    </div>
                </section>
                
                <section id="webhook-events">
                    <h3 class="text-xl font-bold text-slate-800 mb-4">Verfügbare Events</h3>
                    
                    <table class="w-full border-collapse bg-white rounded-xl border border-slate-200 overflow-hidden">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">Event</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">Beschreibung</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">Plan</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            <tr class="border-b border-slate-100">
                                <td class="px-4 py-3"><code>lead.created</code></td>
                                <td class="px-4 py-3 text-slate-600">Neuer Lead wurde erstellt</td>
                                <td class="px-4 py-3"><span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs">Pro</span></td>
                            </tr>
                            <tr class="border-b border-slate-100">
                                <td class="px-4 py-3"><code>lead.converted</code></td>
                                <td class="px-4 py-3 text-slate-600">Lead wurde konvertiert</td>
                                <td class="px-4 py-3"><span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs">Pro</span></td>
                            </tr>
                            <tr class="border-b border-slate-100">
                                <td class="px-4 py-3"><code>referrer.created</code></td>
                                <td class="px-4 py-3 text-slate-600">Neuer Empfehler registriert</td>
                                <td class="px-4 py-3"><span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs">Pro</span></td>
                            </tr>
                            <tr class="border-b border-slate-100">
                                <td class="px-4 py-3"><code>referrer.reward_earned</code></td>
                                <td class="px-4 py-3 text-slate-600">Empfehler erreicht Belohnungsstufe</td>
                                <td class="px-4 py-3"><span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs">Pro</span></td>
                            </tr>
                            <tr class="border-b border-slate-100">
                                <td class="px-4 py-3"><code>lead.updated</code></td>
                                <td class="px-4 py-3 text-slate-600">Lead wurde aktualisiert</td>
                                <td class="px-4 py-3"><span class="bg-amber-100 text-amber-700 px-2 py-0.5 rounded text-xs">Enterprise</span></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3"><code>daily.summary</code></td>
                                <td class="px-4 py-3 text-slate-600">Tägliche Zusammenfassung</td>
                                <td class="px-4 py-3"><span class="bg-amber-100 text-amber-700 px-2 py-0.5 rounded text-xs">Enterprise</span></td>
                            </tr>
                        </tbody>
                    </table>
                </section>
                
                <section id="webhook-security">
                    <h3 class="text-xl font-bold text-slate-800 mb-4">Signatur-Verifizierung</h3>
                    <div class="prose prose-slate max-w-none">
                        <p class="text-slate-600 mb-4">
                            Jeder Webhook-Request enthält einen <code>X-Webhook-Signature</code> Header. 
                            Verwenden Sie diesen, um die Authentizität des Requests zu verifizieren.
                        </p>
                        
                        <div class="bg-slate-800 rounded-xl p-4">
                            <p class="text-slate-400 text-sm mb-2">PHP Beispiel</p>
                            <pre class="text-green-400 text-sm overflow-x-auto">&lt;?php
$payload = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_WEBHOOK_SIGNATURE'];
$secret = 'whsec_xxxxx'; // Ihr Webhook-Secret

$expected = hash_hmac('sha256', $payload, $secret);

if (hash_equals($expected, $signature)) {
    // Signatur gültig - Request verarbeiten
    $data = json_decode($payload, true);
    // ...
} else {
    // Ungültige Signatur - Request ablehnen
    http_response_code(401);
    exit;
}</pre>
                        </div>
                    </div>
                </section>
                
            </main>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 mt-16 py-8">
        <div class="max-w-6xl mx-auto px-6 text-center text-sm text-slate-500">
            <p>&copy; <?= date('Y') ?> Leadbusiness. Alle Rechte vorbehalten.</p>
        </div>
    </footer>
    
</body>
</html>
