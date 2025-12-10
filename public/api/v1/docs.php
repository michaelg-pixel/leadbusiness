<?php
/**
 * Leadbusiness REST API v1 - Documentation
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../config/settings.php';

$pageTitle = 'API Dokumentation';
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - Leadbusiness</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        code, pre { font-family: 'JetBrains Mono', monospace; }
        .method-get { background: #10b981; }
        .method-post { background: #3b82f6; }
        .method-put { background: #f59e0b; }
        .method-delete { background: #ef4444; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800">
    
    <!-- Header -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="/" class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-purple-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-share-nodes text-white"></i>
                    </div>
                    <span class="text-xl font-bold">Leadbusiness</span>
                </a>
                <span class="text-slate-300">|</span>
                <span class="text-slate-600 font-medium">API Dokumentation</span>
            </div>
            <a href="/dashboard/api.php" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 rounded-lg text-sm font-medium transition">
                <i class="fas fa-key mr-2"></i>Meine API-Keys
            </a>
        </div>
    </header>
    
    <div class="max-w-6xl mx-auto px-4 py-8">
        <div class="grid lg:grid-cols-4 gap-8">
            
            <!-- Sidebar Navigation -->
            <nav class="lg:col-span-1">
                <div class="sticky top-24 space-y-6">
                    <div>
                        <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Erste Schritte</h3>
                        <ul class="space-y-2">
                            <li><a href="#introduction" class="text-slate-600 hover:text-primary-600">Einführung</a></li>
                            <li><a href="#authentication" class="text-slate-600 hover:text-primary-600">Authentifizierung</a></li>
                            <li><a href="#rate-limits" class="text-slate-600 hover:text-primary-600">Rate Limits</a></li>
                            <li><a href="#errors" class="text-slate-600 hover:text-primary-600">Fehlerbehandlung</a></li>
                        </ul>
                    </div>
                    
                    <div>
                        <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Endpunkte</h3>
                        <ul class="space-y-2">
                            <li><a href="#referrers" class="text-slate-600 hover:text-primary-600">Empfehler</a></li>
                            <li><a href="#conversions" class="text-slate-600 hover:text-primary-600">Conversions</a></li>
                            <li><a href="#stats" class="text-slate-600 hover:text-primary-600">Statistiken</a></li>
                            <li><a href="#rewards" class="text-slate-600 hover:text-primary-600">Belohnungen</a></li>
                            <li><a href="#webhooks" class="text-slate-600 hover:text-primary-600">Webhooks <span class="text-xs bg-purple-100 text-purple-700 px-1.5 py-0.5 rounded">ENT</span></a></li>
                            <li><a href="#export" class="text-slate-600 hover:text-primary-600">Export <span class="text-xs bg-purple-100 text-purple-700 px-1.5 py-0.5 rounded">ENT</span></a></li>
                        </ul>
                    </div>
                </div>
            </nav>
            
            <!-- Main Content -->
            <main class="lg:col-span-3 space-y-12">
                
                <!-- Introduction -->
                <section id="introduction">
                    <h1 class="text-3xl font-bold mb-4">Leadbusiness REST API</h1>
                    <p class="text-slate-600 mb-6">
                        Die Leadbusiness API ermöglicht es Ihnen, Ihr Empfehlungsprogramm programmatisch zu verwalten. 
                        Sie können Empfehler erstellen, Conversions tracken, Statistiken abrufen und vieles mehr.
                    </p>
                    
                    <div class="bg-white rounded-xl border border-slate-200 p-6">
                        <h3 class="font-semibold mb-3">Base URL</h3>
                        <code class="bg-slate-100 px-4 py-2 rounded-lg block text-sm">https://empfehlungen.cloud/api/v1/</code>
                    </div>
                </section>
                
                <!-- Authentication -->
                <section id="authentication">
                    <h2 class="text-2xl font-bold mb-4">Authentifizierung</h2>
                    <p class="text-slate-600 mb-6">
                        Alle API-Anfragen müssen mit einem API-Key und optionalem Secret authentifiziert werden. 
                        Diese werden als HTTP-Header übergeben.
                    </p>
                    
                    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                        <div class="bg-slate-800 text-slate-100 p-4">
                            <p class="text-sm text-slate-400 mb-2">HTTP Headers</p>
                            <pre class="text-sm"><code>X-API-Key: lb_xxxxxxxxxxxxxxxxxxxx
X-API-Secret: lbs_xxxxxxxxxxxxxxxxxxxx</code></pre>
                        </div>
                        <div class="p-4 bg-amber-50 border-t border-amber-200">
                            <p class="text-sm text-amber-800">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <strong>Wichtig:</strong> Halten Sie Ihre API-Credentials geheim und teilen Sie sie niemals im Frontend-Code.
                            </p>
                        </div>
                    </div>
                    
                    <h3 class="font-semibold mt-6 mb-3">cURL Beispiel</h3>
                    <div class="bg-slate-800 text-slate-100 rounded-xl p-4 overflow-x-auto">
                        <pre class="text-sm"><code>curl -X GET "https://empfehlungen.cloud/api/v1/referrers" \
  -H "X-API-Key: lb_xxxxxxxxxxxxxxxxxxxx" \
  -H "X-API-Secret: lbs_xxxxxxxxxxxxxxxxxxxx"</code></pre>
                    </div>
                </section>
                
                <!-- Rate Limits -->
                <section id="rate-limits">
                    <h2 class="text-2xl font-bold mb-4">Rate Limits</h2>
                    <p class="text-slate-600 mb-6">
                        Die API hat Rate Limits, um faire Nutzung zu gewährleisten. Die Limits variieren je nach Plan.
                    </p>
                    
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div class="bg-white rounded-xl border border-slate-200 p-6">
                            <div class="flex items-center gap-3 mb-4">
                                <i class="fas fa-crown text-primary-500"></i>
                                <h3 class="font-semibold">Professional</h3>
                            </div>
                            <ul class="space-y-2 text-sm text-slate-600">
                                <li><strong>60</strong> Anfragen/Minute</li>
                                <li><strong>5.000</strong> Anfragen/Tag</li>
                                <li><strong>100.000</strong> Anfragen/Monat</li>
                            </ul>
                        </div>
                        
                        <div class="bg-white rounded-xl border border-purple-200 p-6 bg-purple-50">
                            <div class="flex items-center gap-3 mb-4">
                                <i class="fas fa-building text-purple-500"></i>
                                <h3 class="font-semibold">Enterprise</h3>
                            </div>
                            <ul class="space-y-2 text-sm text-slate-600">
                                <li><strong>300</strong> Anfragen/Minute</li>
                                <li><strong>50.000</strong> Anfragen/Tag</li>
                                <li><strong>1.000.000</strong> Anfragen/Monat</li>
                            </ul>
                        </div>
                    </div>
                    
                    <h3 class="font-semibold mt-6 mb-3">Rate Limit Headers</h3>
                    <div class="bg-slate-800 text-slate-100 rounded-xl p-4">
                        <pre class="text-sm"><code>X-RateLimit-Limit-Day: 5000
X-RateLimit-Remaining-Day: 4985
X-RateLimit-Limit-Minute: 60
X-RateLimit-Remaining-Minute: 58</code></pre>
                    </div>
                </section>
                
                <!-- Errors -->
                <section id="errors">
                    <h2 class="text-2xl font-bold mb-4">Fehlerbehandlung</h2>
                    <p class="text-slate-600 mb-6">
                        Die API verwendet HTTP-Statuscodes und gibt strukturierte Fehlermeldungen zurück.
                    </p>
                    
                    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th class="text-left px-4 py-3 font-semibold">Code</th>
                                    <th class="text-left px-4 py-3 font-semibold">Bedeutung</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr><td class="px-4 py-3"><code>200</code></td><td class="px-4 py-3">Erfolg</td></tr>
                                <tr><td class="px-4 py-3"><code>201</code></td><td class="px-4 py-3">Ressource erstellt</td></tr>
                                <tr><td class="px-4 py-3"><code>400</code></td><td class="px-4 py-3">Ungültige Anfrage</td></tr>
                                <tr><td class="px-4 py-3"><code>401</code></td><td class="px-4 py-3">Nicht authentifiziert</td></tr>
                                <tr><td class="px-4 py-3"><code>403</code></td><td class="px-4 py-3">Keine Berechtigung</td></tr>
                                <tr><td class="px-4 py-3"><code>404</code></td><td class="px-4 py-3">Nicht gefunden</td></tr>
                                <tr><td class="px-4 py-3"><code>422</code></td><td class="px-4 py-3">Validierungsfehler</td></tr>
                                <tr><td class="px-4 py-3"><code>429</code></td><td class="px-4 py-3">Rate Limit erreicht</td></tr>
                                <tr><td class="px-4 py-3"><code>500</code></td><td class="px-4 py-3">Serverfehler</td></tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <h3 class="font-semibold mt-6 mb-3">Fehler-Response Format</h3>
                    <div class="bg-slate-800 text-slate-100 rounded-xl p-4">
                        <pre class="text-sm"><code>{
  "success": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Valid email is required",
    "status": 422
  },
  "meta": {
    "timestamp": "2024-01-15T10:30:00+01:00",
    "response_time_ms": 15.4
  }
}</code></pre>
                    </div>
                </section>
                
                <!-- Referrers Endpoint -->
                <section id="referrers">
                    <h2 class="text-2xl font-bold mb-4">Empfehler</h2>
                    <p class="text-slate-600 mb-6">Verwalten Sie Ihre Empfehler über die API.</p>
                    
                    <!-- GET /referrers -->
                    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden mb-6">
                        <div class="flex items-center gap-3 p-4 border-b border-slate-200">
                            <span class="method-get px-2 py-1 text-white text-xs font-bold rounded">GET</span>
                            <code class="text-sm">/api/v1/referrers</code>
                            <span class="text-slate-500 text-sm ml-auto">Alle Empfehler abrufen</span>
                        </div>
                        <div class="p-4">
                            <h4 class="font-medium mb-2">Query Parameter</h4>
                            <table class="w-full text-sm mb-4">
                                <tr class="border-b border-slate-100">
                                    <td class="py-2"><code>page</code></td>
                                    <td class="py-2 text-slate-500">Seite (Standard: 1)</td>
                                </tr>
                                <tr class="border-b border-slate-100">
                                    <td class="py-2"><code>limit</code></td>
                                    <td class="py-2 text-slate-500">Einträge pro Seite (1-100, Standard: 20)</td>
                                </tr>
                                <tr class="border-b border-slate-100">
                                    <td class="py-2"><code>status</code></td>
                                    <td class="py-2 text-slate-500">Filter nach Status (active, pending, deleted)</td>
                                </tr>
                                <tr class="border-b border-slate-100">
                                    <td class="py-2"><code>search</code></td>
                                    <td class="py-2 text-slate-500">Suche nach Name, E-Mail oder Code</td>
                                </tr>
                                <tr>
                                    <td class="py-2"><code>order_by</code></td>
                                    <td class="py-2 text-slate-500">Sortierung (id, name, conversions, created_at)</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <!-- POST /referrers -->
                    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden mb-6">
                        <div class="flex items-center gap-3 p-4 border-b border-slate-200">
                            <span class="method-post px-2 py-1 text-white text-xs font-bold rounded">POST</span>
                            <code class="text-sm">/api/v1/referrers</code>
                            <span class="text-slate-500 text-sm ml-auto">Neuen Empfehler erstellen</span>
                        </div>
                        <div class="p-4">
                            <h4 class="font-medium mb-2">Request Body</h4>
                            <div class="bg-slate-800 text-slate-100 rounded-lg p-4 mb-4">
                                <pre class="text-sm"><code>{
  "email": "max@example.de",
  "name": "Max Mustermann",
  "phone": "+49 123 456789",
  "notes": "VIP Kunde"
}</code></pre>
                            </div>
                            <p class="text-sm text-slate-500"><strong>email</strong> ist erforderlich. Der Referral-Code wird automatisch generiert.</p>
                        </div>
                    </div>
                    
                    <!-- GET /referrers/{id} -->
                    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden mb-6">
                        <div class="flex items-center gap-3 p-4 border-b border-slate-200">
                            <span class="method-get px-2 py-1 text-white text-xs font-bold rounded">GET</span>
                            <code class="text-sm">/api/v1/referrers/{id}</code>
                            <span class="text-slate-500 text-sm ml-auto">Einzelnen Empfehler abrufen</span>
                        </div>
                        <div class="p-4">
                            <p class="text-sm text-slate-600">Akzeptiert sowohl die ID als auch den Referral-Code.</p>
                        </div>
                    </div>
                    
                    <!-- PUT /referrers/{id} -->
                    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden mb-6">
                        <div class="flex items-center gap-3 p-4 border-b border-slate-200">
                            <span class="method-put px-2 py-1 text-white text-xs font-bold rounded">PUT</span>
                            <code class="text-sm">/api/v1/referrers/{id}</code>
                            <span class="text-slate-500 text-sm ml-auto">Empfehler aktualisieren</span>
                        </div>
                        <div class="p-4">
                            <p class="text-sm text-slate-600 mb-3">Aktualisierbare Felder: <code>name</code>, <code>phone</code>, <code>notes</code>, <code>status</code></p>
                        </div>
                    </div>
                    
                    <!-- DELETE /referrers/{id} -->
                    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                        <div class="flex items-center gap-3 p-4 border-b border-slate-200">
                            <span class="method-delete px-2 py-1 text-white text-xs font-bold rounded">DELETE</span>
                            <code class="text-sm">/api/v1/referrers/{id}</code>
                            <span class="text-slate-500 text-sm ml-auto">Empfehler löschen (Soft Delete)</span>
                        </div>
                    </div>
                </section>
                
                <!-- Conversions Endpoint -->
                <section id="conversions">
                    <h2 class="text-2xl font-bold mb-4">Conversions</h2>
                    <p class="text-slate-600 mb-6">Tracken und verwalten Sie erfolgreiche Empfehlungen.</p>
                    
                    <!-- POST /conversions -->
                    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden mb-6">
                        <div class="flex items-center gap-3 p-4 border-b border-slate-200">
                            <span class="method-post px-2 py-1 text-white text-xs font-bold rounded">POST</span>
                            <code class="text-sm">/api/v1/conversions</code>
                            <span class="text-slate-500 text-sm ml-auto">Neue Conversion tracken</span>
                        </div>
                        <div class="p-4">
                            <div class="bg-slate-800 text-slate-100 rounded-lg p-4 mb-4">
                                <pre class="text-sm"><code>{
  "referral_code": "ABC123XY",
  "converted_email": "neukunde@example.de",
  "converted_name": "Neuer Kunde",
  "order_id": "ORD-2024-001",
  "order_value": 99.90
}</code></pre>
                            </div>
                            <p class="text-sm text-slate-500">Entweder <code>referral_code</code> oder <code>referrer_id</code> ist erforderlich.</p>
                        </div>
                    </div>
                    
                    <!-- GET /conversions -->
                    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                        <div class="flex items-center gap-3 p-4 border-b border-slate-200">
                            <span class="method-get px-2 py-1 text-white text-xs font-bold rounded">GET</span>
                            <code class="text-sm">/api/v1/conversions</code>
                            <span class="text-slate-500 text-sm ml-auto">Alle Conversions abrufen</span>
                        </div>
                        <div class="p-4">
                            <h4 class="font-medium mb-2">Query Parameter</h4>
                            <table class="w-full text-sm">
                                <tr class="border-b border-slate-100">
                                    <td class="py-2"><code>referrer_id</code></td>
                                    <td class="py-2 text-slate-500">Filter nach Empfehler</td>
                                </tr>
                                <tr class="border-b border-slate-100">
                                    <td class="py-2"><code>status</code></td>
                                    <td class="py-2 text-slate-500">Filter nach Status</td>
                                </tr>
                                <tr class="border-b border-slate-100">
                                    <td class="py-2"><code>from</code></td>
                                    <td class="py-2 text-slate-500">Von Datum (YYYY-MM-DD)</td>
                                </tr>
                                <tr>
                                    <td class="py-2"><code>to</code></td>
                                    <td class="py-2 text-slate-500">Bis Datum (YYYY-MM-DD)</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </section>
                
                <!-- Stats Endpoint -->
                <section id="stats">
                    <h2 class="text-2xl font-bold mb-4">Statistiken</h2>
                    <p class="text-slate-600 mb-6">Rufen Sie Statistiken zu Ihrem Empfehlungsprogramm ab.</p>
                    
                    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden mb-6">
                        <div class="flex items-center gap-3 p-4 border-b border-slate-200">
                            <span class="method-get px-2 py-1 text-white text-xs font-bold rounded">GET</span>
                            <code class="text-sm">/api/v1/stats</code>
                            <span class="text-slate-500 text-sm ml-auto">Übersicht</span>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden mb-6">
                        <div class="flex items-center gap-3 p-4 border-b border-slate-200">
                            <span class="method-get px-2 py-1 text-white text-xs font-bold rounded">GET</span>
                            <code class="text-sm">/api/v1/stats/daily?days=30</code>
                            <span class="text-slate-500 text-sm ml-auto">Tägliche Statistiken</span>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                        <div class="flex items-center gap-3 p-4 border-b border-slate-200">
                            <span class="method-get px-2 py-1 text-white text-xs font-bold rounded">GET</span>
                            <code class="text-sm">/api/v1/stats/top?limit=10&by=conversions</code>
                            <span class="text-slate-500 text-sm ml-auto">Top Empfehler</span>
                        </div>
                    </div>
                </section>
                
                <!-- Webhooks Endpoint -->
                <section id="webhooks">
                    <h2 class="text-2xl font-bold mb-4">
                        Webhooks
                        <span class="text-sm bg-purple-100 text-purple-700 px-2 py-1 rounded ml-2">Enterprise</span>
                    </h2>
                    <p class="text-slate-600 mb-6">
                        Empfangen Sie Echtzeit-Benachrichtigungen über Events in Ihrem Empfehlungsprogramm.
                    </p>
                    
                    <div class="bg-white rounded-xl border border-slate-200 p-6 mb-6">
                        <h4 class="font-medium mb-3">Verfügbare Events</h4>
                        <div class="grid sm:grid-cols-2 gap-2 text-sm">
                            <div><code>referrer.created</code> - Neuer Empfehler</div>
                            <div><code>referrer.updated</code> - Empfehler aktualisiert</div>
                            <div><code>conversion.created</code> - Neue Conversion</div>
                            <div><code>conversion.confirmed</code> - Conversion bestätigt</div>
                            <div><code>reward.unlocked</code> - Belohnung freigeschaltet</div>
                            <div><code>reward.claimed</code> - Belohnung eingelöst</div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden mb-6">
                        <div class="flex items-center gap-3 p-4 border-b border-slate-200">
                            <span class="method-post px-2 py-1 text-white text-xs font-bold rounded">POST</span>
                            <code class="text-sm">/api/v1/webhooks</code>
                            <span class="text-slate-500 text-sm ml-auto">Webhook registrieren</span>
                        </div>
                        <div class="p-4">
                            <div class="bg-slate-800 text-slate-100 rounded-lg p-4">
                                <pre class="text-sm"><code>{
  "url": "https://ihre-seite.de/webhooks/leadbusiness",
  "events": ["conversion.created", "reward.unlocked"]
}</code></pre>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-xl border border-slate-200 p-6">
                        <h4 class="font-medium mb-3">Webhook Payload Beispiel</h4>
                        <div class="bg-slate-800 text-slate-100 rounded-lg p-4">
                            <pre class="text-sm"><code>{
  "event": "conversion.created",
  "timestamp": "2024-01-15T10:30:00+01:00",
  "data": {
    "conversion_id": 123,
    "referrer_id": 456,
    "referral_code": "ABC123XY",
    "converted_email": "neukunde@example.de",
    "order_value": 99.90
  }
}</code></pre>
                        </div>
                        <p class="text-sm text-slate-500 mt-3">
                            <i class="fas fa-shield-alt mr-1"></i>
                            Webhooks werden mit dem Header <code>X-Webhook-Signature</code> signiert (HMAC-SHA256).
                        </p>
                    </div>
                </section>
                
                <!-- Export Endpoint -->
                <section id="export">
                    <h2 class="text-2xl font-bold mb-4">
                        Export
                        <span class="text-sm bg-purple-100 text-purple-700 px-2 py-1 rounded ml-2">Enterprise</span>
                    </h2>
                    <p class="text-slate-600 mb-6">Exportieren Sie Ihre Daten als JSON oder CSV.</p>
                    
                    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden mb-6">
                        <div class="flex items-center gap-3 p-4 border-b border-slate-200">
                            <span class="method-get px-2 py-1 text-white text-xs font-bold rounded">GET</span>
                            <code class="text-sm">/api/v1/export/referrers?format=csv</code>
                            <span class="text-slate-500 text-sm ml-auto">Empfehler exportieren</span>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                        <div class="flex items-center gap-3 p-4 border-b border-slate-200">
                            <span class="method-get px-2 py-1 text-white text-xs font-bold rounded">GET</span>
                            <code class="text-sm">/api/v1/export/conversions?from=2024-01-01&to=2024-01-31</code>
                            <span class="text-slate-500 text-sm ml-auto">Conversions exportieren</span>
                        </div>
                    </div>
                </section>
                
            </main>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 mt-12 py-8">
        <div class="max-w-6xl mx-auto px-4 text-center text-sm text-slate-500">
            <p>&copy; <?= date('Y') ?> Leadbusiness. Alle Rechte vorbehalten.</p>
            <p class="mt-2">
                <a href="/impressum" class="hover:text-slate-700">Impressum</a> · 
                <a href="/datenschutz" class="hover:text-slate-700">Datenschutz</a> · 
                <a href="/dashboard" class="hover:text-slate-700">Dashboard</a>
            </p>
        </div>
    </footer>
    
</body>
</html>
