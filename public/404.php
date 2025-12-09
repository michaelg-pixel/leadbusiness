<?php
/**
 * Leadbusiness - 404 Fehlerseite
 */

$pageTitle = 'Seite nicht gefunden';
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> | Leadbusiness</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center px-4">
    
    <div class="text-center max-w-md">
        <div class="text-8xl mb-6">🔍</div>
        <h1 class="text-4xl font-bold text-gray-900 mb-4">404</h1>
        <h2 class="text-xl text-gray-600 mb-6">Seite nicht gefunden</h2>
        <p class="text-gray-500 mb-8">
            Die gewünschte Seite existiert nicht oder wurde entfernt.
        </p>
        <a href="https://empfohlen.de" 
           class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-500 text-white rounded-xl hover:bg-indigo-600 transition-colors">
            <i class="fas fa-home"></i>
            <span>Zur Startseite</span>
        </a>
    </div>
    
</body>
</html>
