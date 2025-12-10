<?php
/**
 * Branchenseite: Fitnessstudios
 */

$pageTitle = 'Empfehlungsprogramm für Fitnessstudios';
$metaDescription = 'Automatisches Empfehlungsprogramm für Fitnessstudios und Gyms. Mitglieder werben Mitglieder und erhalten Belohnungen wie Gratismonate oder Personal Training.';
$currentPage = 'branchen';

require_once __DIR__ . '/../../templates/marketing/header.php';

// Branchenspezifische Daten
$branche = [
    'name' => 'Fitnessstudios',
    'slug' => 'fitness',
    'icon' => 'fa-dumbbell',
    'color' => 'orange',
    'heroTitle' => 'Mehr Mitglieder durch Empfehlungen',
    'heroSubtitle' => 'Fitness ist Lifestyle – und Lifestyle wird geteilt. Nutzen Sie die Begeisterung Ihrer Mitglieder für organisches Studiowachstum.',
];

$vorteile = [
    [
        'icon' => 'fa-users',
        'title' => 'Motivierte Neumitglieder',
        'text' => 'Wer durch Freunde kommt, bleibt länger dabei. Die gemeinsame Motivation steigert die Retention deutlich.'
    ],
    [
        'icon' => 'fa-chart-line',
        'title' => 'Höhere Retention',
        'text' => 'Empfohlene Mitglieder haben eine 37% höhere Verweildauer als Mitglieder aus klassischer Werbung.'
    ],
    [
        'icon' => 'fa-euro-sign',
        'title' => 'Niedrige Akquisekosten',
        'text' => 'Empfehlungen kosten einen Bruchteil von Facebook-Ads oder Flyern – bei besserer Qualität.'
    ],
    [
        'icon' => 'fa-heart',
        'title' => 'Community stärken',
        'text' => 'Ein Empfehlungsprogramm fördert das Wir-Gefühl und macht Mitglieder zu echten Fans.'
    ],
];

$belohnungen = [
    ['stufe' => 1, 'belohnung' => '1 Woche Mitgliedschaft gratis'],
    ['stufe' => 3, 'belohnung' => 'Gratis Personal Training Session'],
    ['stufe' => 5, 'belohnung' => '1 Monat Mitgliedschaft gratis'],
];

$testimonial = [
    'text' => 'Wir haben unser Empfehlungsprogramm von Excel auf Leadbusiness umgestellt. Seitdem haben wir 3x mehr Empfehlungen – weil es für die Mitglieder so einfach ist zu teilen.',
    'name' => 'Sarah Berger',
    'rolle' => 'FitLife Studio Hamburg',
    'initialen' => 'SB',
];

$stats = [
    'empfehler' => '412',
    'conversions' => '156',
    'rate' => '38%',
];
?>

<!-- Hero Section -->
<section class="relative py-16 md:py-24 overflow-hidden">
    <!-- Background -->
    <div class="absolute inset-0 bg-gradient-to-br from-orange-500 to-red-600"></div>
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-10 left-10 w-40 h-40 bg-white rounded-full blur-3xl"></div>
        <div class="absolute bottom-10 right-10 w-60 h-60 bg-white rounded-full blur-3xl"></div>
    </div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-8 lg:gap-12 items-center">
            <div class="text-white">
                <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full mb-6">
                    <i class="fas <?= $branche['icon'] ?>"></i>
                    <span class="text-sm font-medium">Für <?= $branche['name'] ?></span>
                </div>
                
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold mb-6 leading-tight">
                    <?= $branche['heroTitle'] ?>
                </h1>
                
                <p class="text-lg md:text-xl text-white/90 mb-8 leading-relaxed">
                    <?= $branche['heroSubtitle'] ?>
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="/onboarding" class="btn-white btn-large inline-flex items-center justify-center gap-2">
                        <span>Jetzt kostenlos starten</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                    <a href="/preise" class="btn-ghost-white btn-large inline-flex items-center justify-center gap-2">
                        <span>Preise ansehen</span>
                    </a>
                </div>
                
                <div class="mt-8 flex flex-wrap gap-6 text-white/80 text-sm">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-green-400"></i>
                        <span>7 Tage kostenlos</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-green-400"></i>
                        <span>DSGVO-konform</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-green-400"></i>
                        <span>Keine Technik nötig</span>
                    </div>
                </div>
            </div>
            
            <!-- Visual -->
            <div class="hidden lg:block">
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                    <div class="bg-white rounded-xl shadow-2xl p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-dumbbell text-orange-600 text-xl"></i>
                            </div>
                            <div>
                                <div class="font-bold text-gray-900">FitLife Studio</div>
                                <div class="text-sm text-gray-500">empfohlen.de/fitlife</div>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-3 mb-4">
                            <div class="text-center p-3 bg-gray-50 rounded-lg">
                                <div class="text-2xl font-bold text-orange-600"><?= $stats['empfehler'] ?></div>
                                <div class="text-xs text-gray-500">Empfehler</div>
                            </div>
                            <div class="text-center p-3 bg-gray-50 rounded-lg">
                                <div class="text-2xl font-bold text-green-600"><?= $stats['conversions'] ?></div>
                                <div class="text-xs text-gray-500">Neumitglieder</div>
                            </div>
                            <div class="text-center p-3 bg-gray-50 rounded-lg">
                                <div class="text-2xl font-bold text-yellow-600"><?= $stats['rate'] ?></div>
                                <div class="text-xs text-gray-500">Conversion</div>
                            </div>
                        </div>
                        <div class="text-center text-sm text-gray-500">
                            <i class="fas fa-fire text-orange-500 mr-1"></i>
                            Besonders stark im Januar & September
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Vorteile Section -->
<section class="py-12 md:py-20 bg-white dark:bg-slate-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                Warum Empfehlungsmarketing für <?= $branche['name'] ?>?
            </h2>
            <p class="text-gray-600 dark:text-gray-400 text-lg max-w-2xl mx-auto">
                Trainingspartner motivieren sich gegenseitig – das beste Fundament für Ihr Wachstum.
            </p>
        </div>
        
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8">
            <?php foreach ($vorteile as $vorteil): ?>
            <div class="bg-gray-50 dark:bg-slate-800 rounded-2xl p-6 hover:shadow-lg transition-shadow">
                <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-xl flex items-center justify-center text-orange-600 dark:text-orange-400 text-xl mb-4">
                    <i class="fas <?= $vorteil['icon'] ?>"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2"><?= $vorteil['title'] ?></h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm"><?= $vorteil['text'] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Belohnungen Section -->
<section class="py-12 md:py-20 bg-gray-50 dark:bg-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-8 lg:gap-16 items-center">
            <div>
                <span class="text-orange-600 dark:text-orange-400 font-semibold uppercase tracking-wide text-sm">Belohnungssystem</span>
                <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mt-2 mb-6">
                    Beispiel-Belohnungen für Ihr Studio
                </h2>
                <p class="text-gray-600 dark:text-gray-400 text-lg mb-8">
                    Belohnen Sie Engagement mit dem, was Ihre Mitglieder wirklich motiviert:
                </p>
                
                <div class="space-y-4">
                    <?php foreach ($belohnungen as $b): ?>
                    <div class="flex items-center gap-4 bg-white dark:bg-slate-700 rounded-xl p-4 shadow-sm">
                        <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-500 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0">
                            <?= $b['stufe'] ?>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500 dark:text-gray-400"><?= $b['stufe'] ?> Empfehlung<?= $b['stufe'] > 1 ? 'en' : '' ?></div>
                            <div class="font-semibold text-gray-900 dark:text-white"><?= $b['belohnung'] ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-6">
                    <i class="fas fa-lightbulb mr-1 text-yellow-500"></i>
                    Tipp: Gratismonate wirken besonders gut bei längerfristigen Mitgliedschaften.
                </p>
            </div>
            
            <div class="bg-white dark:bg-slate-700 rounded-2xl p-6 md:p-8 shadow-lg">
                <div class="text-center mb-6">
                    <div class="text-5xl mb-3">💪</div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Belohnung freigeschaltet!</h3>
                    <p class="text-gray-500 dark:text-gray-400">Du hast Stufe 3 erreicht</p>
                </div>
                
                <div class="bg-gradient-to-r from-orange-50 to-red-50 dark:from-orange-900/20 dark:to-red-900/10 rounded-xl p-5 border border-orange-200 dark:border-orange-700/30 mb-6">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-orange-500 rounded-full flex items-center justify-center text-2xl">
                            🏋️
                        </div>
                        <div>
                            <div class="font-bold text-gray-900 dark:text-white text-lg">Gratis Personal Training</div>
                            <div class="text-gray-600 dark:text-gray-300">60 Min mit unserem Trainer</div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center">
                    <div class="inline-flex items-center gap-2 text-green-600 dark:text-green-400 font-medium">
                        <i class="fas fa-calendar-check"></i>
                        <span>Termin jetzt buchbar</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonial Section -->
<section class="py-12 md:py-20 bg-white dark:bg-slate-900">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-gradient-to-br from-orange-50 to-red-50 dark:from-slate-800 dark:to-slate-700 rounded-2xl p-8 md:p-12 text-center">
            <div class="flex justify-center gap-1 text-yellow-400 mb-6">
                <?php for ($i = 0; $i < 5; $i++): ?>
                <i class="fas fa-star text-xl"></i>
                <?php endfor; ?>
            </div>
            
            <blockquote class="text-xl md:text-2xl font-medium text-gray-900 dark:text-white mb-8 leading-relaxed">
                "<?= $testimonial['text'] ?>"
            </blockquote>
            
            <div class="flex items-center justify-center gap-4">
                <div class="w-14 h-14 bg-orange-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                    <?= $testimonial['initialen'] ?>
                </div>
                <div class="text-left">
                    <div class="font-bold text-gray-900 dark:text-white"><?= $testimonial['name'] ?></div>
                    <div class="text-gray-600 dark:text-gray-400"><?= $testimonial['rolle'] ?></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================== -->
<!-- INTERAKTIVE ANIMATIONEN SECTION               -->
<!-- ============================================== -->
<section class="py-12 md:py-20 bg-gray-50 dark:bg-slate-800">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <span class="inline-flex items-center gap-2 bg-gradient-to-r from-orange-500 to-red-600 text-white px-5 py-2 rounded-full text-sm font-bold shadow-lg mb-4">
                <span>🔥</span> Live erleben
            </span>
            <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                So funktioniert Empfehlungsmarketing im Gym
            </h2>
            <p class="text-gray-600 dark:text-gray-400 text-lg max-w-2xl mx-auto">
                Drei interaktive Demos zeigen, wie Ihre Mitglieder begeistert Freunde werben.
            </p>
        </div>
        
        <!-- Tab Navigation -->
        <div class="flex flex-wrap justify-center gap-3 mb-8" id="fitness-animation-tabs">
            <button onclick="showFitnessAnimation('chat')" id="tab-chat" class="fitness-tab active px-5 py-3 rounded-xl font-semibold text-sm transition-all bg-gradient-to-r from-orange-500 to-red-600 text-white shadow-lg">
                💬 Gym-Buddy Chat
            </button>
            <button onclick="showFitnessAnimation('progress')" id="tab-progress" class="fitness-tab px-5 py-3 rounded-xl font-semibold text-sm transition-all bg-white dark:bg-slate-700 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-slate-600 hover:shadow-md">
                🏋️ Workout-Progress
            </button>
            <button onclick="showFitnessAnimation('leaderboard')" id="tab-leaderboard" class="fitness-tab px-5 py-3 rounded-xl font-semibold text-sm transition-all bg-white dark:bg-slate-700 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-slate-600 hover:shadow-md">
                🏆 Leaderboard Race
            </button>
        </div>
        
        <!-- Animation Containers -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 md:p-8 min-h-[600px] shadow-lg">
            
            <!-- ========================================= -->
            <!-- ANIMATION 1: GYM-BUDDY WHATSAPP CHAT     -->
            <!-- ========================================= -->
            <div id="animation-chat" class="fitness-animation-content">
                <div class="text-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">So läuft's zwischen Gym-Buddies</h3>
                    <p class="text-gray-500 dark:text-gray-400">Eine WhatsApp-Nachricht – und schon hast du einen Trainingspartner</p>
                </div>
                
                <div class="max-w-md mx-auto">
                    <!-- Phone Frame -->
                    <div class="bg-gray-800 rounded-[2.5rem] p-3 shadow-2xl">
                        <div class="w-20 h-5 bg-black rounded-full mx-auto mb-2"></div>
                        <div class="bg-[#0b141a] rounded-[2rem] overflow-hidden">
                            
                            <!-- WhatsApp Header -->
                            <div class="bg-[#1f2c34] px-4 py-3 flex items-center gap-3">
                                <div class="w-2 h-2 rounded-full bg-green-500"></div>
                                <div class="w-10 h-10 bg-gradient-to-br from-orange-400 to-red-500 rounded-full flex items-center justify-center text-white font-bold">T</div>
                                <div class="flex-1">
                                    <div class="text-white font-semibold text-sm">Tom 💪</div>
                                    <div class="text-green-400 text-xs">online</div>
                                </div>
                                <i class="fas fa-video text-gray-400"></i>
                                <i class="fas fa-phone text-gray-400 ml-4"></i>
                            </div>
                            
                            <!-- Chat Background -->
                            <div class="bg-[#0b141a] p-4 min-h-[380px] relative" style="background-image: url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cpath d=\"M30 5 L35 15 L30 25 L25 15 Z\" fill=\"%23ffffff\" fill-opacity=\"0.02\"/%3E%3C/svg%3E');">
                                
                                <!-- Chat Messages Container -->
                                <div id="chat-messages" class="space-y-3">
                                    <!-- Messages will be inserted here by JS -->
                                </div>
                                
                                <!-- Typing Indicator -->
                                <div id="typing-indicator" class="hidden mt-3">
                                    <div class="inline-flex items-center gap-1 bg-[#1f2c34] rounded-2xl rounded-bl-none px-4 py-2">
                                        <div class="typing-dot w-2 h-2 bg-gray-400 rounded-full"></div>
                                        <div class="typing-dot w-2 h-2 bg-gray-400 rounded-full"></div>
                                        <div class="typing-dot w-2 h-2 bg-gray-400 rounded-full"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Input Bar -->
                            <div class="bg-[#1f2c34] px-3 py-2 flex items-center gap-2">
                                <div class="w-10 h-10 bg-[#00a884] rounded-full flex items-center justify-center">
                                    <i class="fas fa-plus text-white"></i>
                                </div>
                                <div class="flex-1 bg-[#2a3942] rounded-full px-4 py-2 text-gray-400 text-sm">
                                    Nachricht
                                </div>
                                <div class="w-10 h-10 bg-[#00a884] rounded-full flex items-center justify-center">
                                    <i class="fas fa-microphone text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Success Notification -->
                    <div id="chat-success" class="hidden mt-6">
                        <div class="bg-gradient-to-r from-orange-500 to-red-600 rounded-xl p-4 text-white text-center animate-bounce-in">
                            <div class="text-2xl mb-2">🎉</div>
                            <div class="font-bold">Tom hat sich angemeldet!</div>
                            <div class="text-white/80 text-sm">Max erhält: 1 Woche gratis</div>
                        </div>
                    </div>
                    
                    <!-- Replay Button -->
                    <button onclick="restartChatAnimation()" id="chat-replay" class="hidden mt-4 mx-auto block px-6 py-3 bg-gradient-to-r from-orange-500 to-red-600 text-white rounded-full font-semibold text-sm hover:shadow-lg transition-all">
                        ↻ Animation wiederholen
                    </button>
                </div>
            </div>
            
            <!-- ========================================= -->
            <!-- ANIMATION 2: WORKOUT PROGRESS TRACKER    -->
            <!-- ========================================= -->
            <div id="animation-progress" class="fitness-animation-content hidden">
                <div class="text-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Deine Empfehlungs-Reise</h3>
                    <p class="text-gray-500 dark:text-gray-400">Jede Empfehlung macht dich stärker – und schaltet Belohnungen frei!</p>
                </div>
                
                <div class="max-w-lg mx-auto">
                    <div class="bg-gradient-to-br from-orange-50 to-red-50 dark:from-slate-800 dark:to-slate-700 rounded-2xl p-6 md:p-8 shadow-lg">
                        
                        <!-- Character Display -->
                        <div class="text-center mb-8">
                            <div class="relative inline-block">
                                <!-- Progress Ring -->
                                <svg class="w-40 h-40 transform -rotate-90" viewBox="0 0 100 100">
                                    <circle cx="50" cy="50" r="45" fill="none" stroke="#e5e7eb" stroke-width="8" class="dark:stroke-slate-600"/>
                                    <circle id="progress-ring" cx="50" cy="50" r="45" fill="none" stroke="url(#gradient)" stroke-width="8" stroke-linecap="round" stroke-dasharray="283" stroke-dashoffset="283" class="transition-all duration-500"/>
                                    <defs>
                                        <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                            <stop offset="0%" stop-color="#f97316"/>
                                            <stop offset="100%" stop-color="#dc2626"/>
                                        </linearGradient>
                                    </defs>
                                </svg>
                                <!-- Character -->
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div id="workout-character" class="text-6xl transition-all duration-300">🏃</div>
                                </div>
                            </div>
                            <div id="workout-level" class="mt-4 text-lg font-bold text-gray-700 dark:text-gray-300">Level: Anfänger</div>
                            <div id="workout-count" class="text-3xl font-black text-transparent bg-clip-text bg-gradient-to-r from-orange-500 to-red-600">0 / 5</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Empfehlungen</div>
                        </div>
                        
                        <!-- Weights Animation -->
                        <div class="flex justify-center items-end gap-4 mb-8 h-20">
                            <div id="weight-left" class="transition-all duration-300" style="font-size: 2rem;">🏋️</div>
                            <div id="weight-center" class="text-4xl transition-all duration-300">💪</div>
                            <div id="weight-right" class="transition-all duration-300" style="font-size: 2rem;">🏋️</div>
                        </div>
                        
                        <!-- Rewards Track -->
                        <div class="grid grid-cols-3 gap-3 mb-6">
                            <div id="fitness-reward-1" class="fitness-reward bg-white dark:bg-slate-700 rounded-xl p-3 text-center transition-all duration-300 opacity-50 scale-95">
                                <div class="text-2xl mb-1">📅</div>
                                <div class="text-xs font-bold text-gray-700 dark:text-gray-300">1 Woche gratis</div>
                                <div class="text-[10px] text-gray-500 dark:text-gray-400 mt-1">1 Empf.</div>
                                <div class="mt-2">
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gray-200 dark:bg-slate-600 text-gray-400 text-xs">🔒</span>
                                </div>
                            </div>
                            <div id="fitness-reward-2" class="fitness-reward bg-white dark:bg-slate-700 rounded-xl p-3 text-center transition-all duration-300 opacity-50 scale-95">
                                <div class="text-2xl mb-1">🏋️</div>
                                <div class="text-xs font-bold text-gray-700 dark:text-gray-300">Personal Training</div>
                                <div class="text-[10px] text-gray-500 dark:text-gray-400 mt-1">3 Empf.</div>
                                <div class="mt-2">
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gray-200 dark:bg-slate-600 text-gray-400 text-xs">🔒</span>
                                </div>
                            </div>
                            <div id="fitness-reward-3" class="fitness-reward bg-white dark:bg-slate-700 rounded-xl p-3 text-center transition-all duration-300 opacity-50 scale-95">
                                <div class="text-2xl mb-1">🗓️</div>
                                <div class="text-xs font-bold text-gray-700 dark:text-gray-300">1 Monat gratis</div>
                                <div class="text-[10px] text-gray-500 dark:text-gray-400 mt-1">5 Empf.</div>
                                <div class="mt-2">
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gray-200 dark:bg-slate-600 text-gray-400 text-xs">🔒</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Action Button -->
                        <button onclick="addWorkoutReferral()" id="workout-btn" class="w-full py-4 bg-gradient-to-r from-orange-500 to-red-600 text-white rounded-xl font-bold text-lg hover:shadow-lg hover:scale-[1.02] transition-all flex items-center justify-center gap-2">
                            <i class="fas fa-share-alt"></i>
                            <span>Freund einladen</span>
                        </button>
                        
                        <!-- Reset -->
                        <button onclick="resetWorkoutProgress()" class="w-full mt-3 py-2 text-gray-500 dark:text-gray-400 text-sm hover:text-orange-600 dark:hover:text-orange-400 transition-colors">
                            ↻ Demo zurücksetzen
                        </button>
                    </div>
                </div>
                
                <!-- Workout Celebration Modal -->
                <div id="workout-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
                    <div class="absolute inset-0 bg-black/50" onclick="closeWorkoutModal()"></div>
                    <div class="relative bg-white dark:bg-slate-700 rounded-2xl p-8 shadow-2xl text-center max-w-sm mx-4 animate-bounce-in">
                        <div id="workout-modal-icon" class="text-6xl mb-4">🎉</div>
                        <div id="workout-modal-title" class="text-2xl font-black text-gray-800 dark:text-white mb-2">Level Up!</div>
                        <div id="workout-modal-text" class="text-gray-600 dark:text-gray-300 mb-6">Du hast eine Belohnung freigeschaltet!</div>
                        <button onclick="closeWorkoutModal()" class="px-8 py-3 bg-gradient-to-r from-orange-500 to-red-600 text-white rounded-xl font-semibold hover:shadow-lg transition-all">
                            Weiter trainieren! 💪
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- ========================================= -->
            <!-- ANIMATION 3: LIVE LEADERBOARD RACE       -->
            <!-- ========================================= -->
            <div id="animation-leaderboard" class="fitness-animation-content hidden">
                <div class="text-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Live Leaderboard Race</h3>
                    <p class="text-gray-500 dark:text-gray-400">Kämpfe um die Top-Plätze und werde Top-Werber des Monats!</p>
                </div>
                
                <div class="max-w-xl mx-auto">
                    <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-2xl p-6 md:p-8 shadow-2xl">
                        
                        <!-- Leaderboard Header -->
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-trophy text-white"></i>
                                </div>
                                <div>
                                    <div class="text-white font-bold">Top Empfehler</div>
                                    <div class="text-gray-400 text-sm">Januar 2025</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-orange-400 font-bold text-sm">🔥 Live</div>
                            </div>
                        </div>
                        
                        <!-- Leaderboard List -->
                        <div id="leaderboard-list" class="space-y-3">
                            <!-- Rank 1 -->
                            <div id="rank-1" class="leaderboard-row flex items-center gap-3 bg-gradient-to-r from-yellow-500/20 to-transparent p-3 rounded-xl border border-yellow-500/30 transition-all duration-500">
                                <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center text-black font-bold text-sm">1</div>
                                <div class="w-10 h-10 bg-gradient-to-br from-pink-400 to-pink-600 rounded-full flex items-center justify-center text-white font-bold">A</div>
                                <div class="flex-1">
                                    <div class="text-white font-semibold">Anna M.</div>
                                    <div class="text-gray-400 text-xs">🏆 Top-Werber</div>
                                </div>
                                <div class="w-32 bg-gray-700 rounded-full h-3 overflow-hidden">
                                    <div id="bar-1" class="h-full bg-gradient-to-r from-yellow-400 to-orange-500 rounded-full transition-all duration-700" style="width: 100%"></div>
                                </div>
                                <div id="score-1" class="text-white font-bold w-8 text-right">12</div>
                            </div>
                            
                            <!-- Rank 2 -->
                            <div id="rank-2" class="leaderboard-row flex items-center gap-3 bg-gray-700/30 p-3 rounded-xl transition-all duration-500">
                                <div class="w-8 h-8 bg-gray-400 rounded-full flex items-center justify-center text-black font-bold text-sm">2</div>
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-bold">T</div>
                                <div class="flex-1">
                                    <div class="text-white font-semibold">Tom S.</div>
                                </div>
                                <div class="w-32 bg-gray-700 rounded-full h-3 overflow-hidden">
                                    <div id="bar-2" class="h-full bg-gradient-to-r from-gray-400 to-gray-500 rounded-full transition-all duration-700" style="width: 83%"></div>
                                </div>
                                <div id="score-2" class="text-white font-bold w-8 text-right">10</div>
                            </div>
                            
                            <!-- Rank 3 -->
                            <div id="rank-3" class="leaderboard-row flex items-center gap-3 bg-gray-700/30 p-3 rounded-xl transition-all duration-500">
                                <div class="w-8 h-8 bg-orange-700 rounded-full flex items-center justify-center text-white font-bold text-sm">3</div>
                                <div class="w-10 h-10 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center text-white font-bold">L</div>
                                <div class="flex-1">
                                    <div class="text-white font-semibold">Lisa K.</div>
                                </div>
                                <div class="w-32 bg-gray-700 rounded-full h-3 overflow-hidden">
                                    <div id="bar-3" class="h-full bg-gradient-to-r from-orange-600 to-orange-700 rounded-full transition-all duration-700" style="width: 67%"></div>
                                </div>
                                <div id="score-3" class="text-white font-bold w-8 text-right">8</div>
                            </div>
                            
                            <!-- Rank 4 -->
                            <div id="rank-4" class="leaderboard-row flex items-center gap-3 bg-gray-700/30 p-3 rounded-xl transition-all duration-500">
                                <div class="w-8 h-8 bg-gray-600 rounded-full flex items-center justify-center text-white font-bold text-sm">4</div>
                                <div class="w-10 h-10 bg-gradient-to-br from-purple-400 to-purple-600 rounded-full flex items-center justify-center text-white font-bold">M</div>
                                <div class="flex-1">
                                    <div class="text-white font-semibold">Max B.</div>
                                </div>
                                <div class="w-32 bg-gray-700 rounded-full h-3 overflow-hidden">
                                    <div id="bar-4" class="h-full bg-gray-500 rounded-full transition-all duration-700" style="width: 50%"></div>
                                </div>
                                <div id="score-4" class="text-white font-bold w-8 text-right">6</div>
                            </div>
                            
                            <!-- Rank 5: YOU -->
                            <div id="rank-5" class="leaderboard-row you-row flex items-center gap-3 bg-gradient-to-r from-orange-500/20 to-red-500/20 p-3 rounded-xl border-2 border-orange-500 transition-all duration-500 animate-pulse-subtle">
                                <div id="your-rank-badge" class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center text-white font-bold text-sm">5</div>
                                <div class="w-10 h-10 bg-gradient-to-br from-orange-400 to-red-500 rounded-full flex items-center justify-center text-white font-bold ring-2 ring-orange-400 ring-offset-2 ring-offset-slate-800">DU</div>
                                <div class="flex-1">
                                    <div class="text-white font-semibold">Du 🔥</div>
                                    <div id="your-status" class="text-orange-400 text-xs">Klicke um aufzusteigen!</div>
                                </div>
                                <div class="w-32 bg-gray-700 rounded-full h-3 overflow-hidden">
                                    <div id="bar-5" class="h-full bg-gradient-to-r from-orange-500 to-red-500 rounded-full transition-all duration-700" style="width: 33%"></div>
                                </div>
                                <div id="score-5" class="text-orange-400 font-bold w-8 text-right">4</div>
                            </div>
                        </div>
                        
                        <!-- Action Button -->
                        <button onclick="climbLeaderboard()" id="climb-btn" class="w-full mt-6 py-4 bg-gradient-to-r from-orange-500 to-red-600 text-white rounded-xl font-bold text-lg hover:shadow-lg hover:scale-[1.02] transition-all flex items-center justify-center gap-2">
                            <i class="fas fa-arrow-up"></i>
                            <span>Freund empfehlen (+1)</span>
                        </button>
                        
                        <!-- Reset -->
                        <button onclick="resetLeaderboard()" class="w-full mt-3 py-2 text-gray-400 text-sm hover:text-orange-400 transition-colors">
                            ↻ Demo zurücksetzen
                        </button>
                    </div>
                    
                    <!-- Winner Celebration -->
                    <div id="winner-celebration" class="hidden mt-6">
                        <div class="bg-gradient-to-r from-yellow-500 to-orange-500 rounded-xl p-6 text-center animate-bounce-in">
                            <div class="text-4xl mb-2">🏆👑🎉</div>
                            <div class="text-white font-black text-xl">PLATZ 1 ERREICHT!</div>
                            <div class="text-white/90">Du bist Top-Werber des Monats!</div>
                            <div class="mt-3 inline-flex items-center gap-2 bg-white/20 px-4 py-2 rounded-full text-white text-sm">
                                <i class="fas fa-medal"></i>
                                Badge freigeschaltet: Champion 🏅
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Animation Styles -->
<style>
    /* Keyframes */
    @keyframes bounceIn {
        0% { transform: scale(0); opacity: 0; }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); opacity: 1; }
    }
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes pulse-subtle {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.8; }
    }
    @keyframes typing {
        0%, 60%, 100% { opacity: 0.3; transform: translateY(0); }
        30% { opacity: 1; transform: translateY(-2px); }
    }
    @keyframes confetti-fall {
        0% { transform: translateY(-100%) rotate(0deg); opacity: 1; }
        100% { transform: translateY(100vh) rotate(720deg); opacity: 0; }
    }
    @keyframes muscle-flex {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.2); }
    }
    @keyframes rank-up {
        0% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
        100% { transform: translateY(0); }
    }
    
    /* Animation Classes */
    .animate-bounce-in { animation: bounceIn 0.5s ease forwards; }
    .animate-slide-up { animation: slideUp 0.4s ease forwards; }
    .animate-pulse-subtle { animation: pulse-subtle 2s ease-in-out infinite; }
    .animate-muscle-flex { animation: muscle-flex 0.5s ease; }
    .animate-rank-up { animation: rank-up 0.5s ease; }
    
    /* Typing Indicator */
    .typing-dot {
        animation: typing 1.4s infinite;
    }
    .typing-dot:nth-child(2) { animation-delay: 0.2s; }
    .typing-dot:nth-child(3) { animation-delay: 0.4s; }
    
    /* Tab Styling */
    .fitness-tab.active {
        background: linear-gradient(to right, #f97316, #dc2626);
        color: white;
        box-shadow: 0 10px 15px -3px rgba(249, 115, 22, 0.3);
        border: none;
    }
    
    /* Fitness Reward Cards */
    .fitness-reward.unlocked {
        opacity: 1 !important;
        transform: scale(1) !important;
        box-shadow: 0 0 20px rgba(249, 115, 22, 0.4);
        border: 2px solid #f97316;
    }
    .fitness-reward.unlocked span {
        background: linear-gradient(to right, #f97316, #dc2626) !important;
        color: white !important;
    }
    
    /* Chat Message Bubbles */
    .chat-bubble-sent {
        background: #005c4b;
        color: white;
        border-radius: 12px 12px 0 12px;
        margin-left: auto;
        max-width: 85%;
    }
    .chat-bubble-received {
        background: #1f2c34;
        color: white;
        border-radius: 12px 12px 12px 0;
        max-width: 85%;
    }
    
    /* Leaderboard Row Highlight */
    .leaderboard-row.moving-up {
        animation: rank-up 0.5s ease;
        background: linear-gradient(to right, rgba(34, 197, 94, 0.3), transparent) !important;
    }
    
    /* Confetti */
    .confetti {
        position: fixed;
        width: 10px;
        height: 10px;
        top: -10px;
        animation: confetti-fall 3s linear forwards;
        z-index: 100;
    }
</style>

<!-- Animation JavaScript -->
<script>
// ==================== TAB SWITCHING ====================
function showFitnessAnimation(type) {
    // Hide all animation contents
    document.querySelectorAll('.fitness-animation-content').forEach(el => el.classList.add('hidden'));
    document.getElementById('animation-' + type).classList.remove('hidden');
    
    // Update tab styles
    document.querySelectorAll('.fitness-tab').forEach(tab => {
        tab.classList.remove('active', 'bg-gradient-to-r', 'from-orange-500', 'to-red-600', 'text-white', 'shadow-lg');
        tab.classList.add('bg-white', 'dark:bg-slate-700', 'text-gray-600', 'dark:text-gray-300', 'border', 'border-gray-200', 'dark:border-slate-600');
    });
    const activeTab = document.getElementById('tab-' + type);
    activeTab.classList.add('active');
    activeTab.classList.remove('bg-white', 'dark:bg-slate-700', 'text-gray-600', 'dark:text-gray-300', 'border', 'border-gray-200', 'dark:border-slate-600');
    
    // Start animations
    if (type === 'chat') restartChatAnimation();
    if (type === 'progress') resetWorkoutProgress();
    if (type === 'leaderboard') resetLeaderboard();
}

// ==================== ANIMATION 1: GYM-BUDDY CHAT ====================
let chatTimeout = null;
const chatMessages = [
    { sender: 'max', text: 'Alter, komm doch mal mit ins FitLife! 💪', delay: 800 },
    { sender: 'tom', text: 'Hmm keine Ahnung... teuer bestimmt 😅', delay: 1500 },
    { sender: 'max', text: 'Hab nen Link für dich – bekommst Probetraining GRATIS + 20% Rabatt!', delay: 1500 },
    { sender: 'max', type: 'link', delay: 800 },
    { sender: 'tom', text: 'Ok deal! Wann trainierst du?', delay: 1500 },
    { sender: 'max', text: 'Morgen 18 Uhr Brust/Trizeps? 😎', delay: 1200 },
    { sender: 'tom', text: 'Bin dabei! 🔥💪', delay: 1000 },
];

function restartChatAnimation() {
    clearTimeout(chatTimeout);
    document.getElementById('chat-messages').innerHTML = '';
    document.getElementById('chat-success').classList.add('hidden');
    document.getElementById('chat-replay').classList.add('hidden');
    document.getElementById('typing-indicator').classList.add('hidden');
    
    let messageIndex = 0;
    
    function showNextMessage() {
        if (messageIndex >= chatMessages.length) {
            // Show success after all messages
            chatTimeout = setTimeout(() => {
                document.getElementById('chat-success').classList.remove('hidden');
                document.getElementById('chat-replay').classList.remove('hidden');
                createConfetti();
            }, 1000);
            return;
        }
        
        const msg = chatMessages[messageIndex];
        
        // Show typing indicator
        if (msg.sender === 'tom') {
            document.getElementById('typing-indicator').classList.remove('hidden');
        }
        
        chatTimeout = setTimeout(() => {
            document.getElementById('typing-indicator').classList.add('hidden');
            
            const container = document.getElementById('chat-messages');
            const bubble = document.createElement('div');
            bubble.className = 'animate-slide-up';
            
            if (msg.type === 'link') {
                bubble.innerHTML = `
                    <div class="chat-bubble-sent px-3 py-2">
                        <div class="bg-[#0d4037] rounded-lg p-3 mb-1">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-8 h-8 bg-orange-500 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-dumbbell text-white text-xs"></i>
                                </div>
                                <div class="text-xs text-white/80">empfohlen.de</div>
                            </div>
                            <div class="text-white font-semibold text-sm">FitLife Studio – Dein Gutschein</div>
                            <div class="text-green-300 text-xs">20% Rabatt auf deine Mitgliedschaft!</div>
                        </div>
                        <div class="text-right text-[10px] text-white/60">18:33 ✓✓</div>
                    </div>
                `;
            } else {
                const isSent = msg.sender === 'max';
                const time = isSent ? '18:3' + (2 + messageIndex) : '18:3' + (2 + messageIndex);
                bubble.innerHTML = `
                    <div class="${isSent ? 'chat-bubble-sent' : 'chat-bubble-received'} px-3 py-2">
                        <div class="text-sm">${msg.text}</div>
                        <div class="text-right text-[10px] text-white/60 mt-1">${time} ${isSent ? '✓✓' : ''}</div>
                    </div>
                `;
            }
            
            container.appendChild(bubble);
            container.scrollTop = container.scrollHeight;
            
            messageIndex++;
            showNextMessage();
        }, msg.delay);
    }
    
    chatTimeout = setTimeout(showNextMessage, 500);
}

// ==================== ANIMATION 2: WORKOUT PROGRESS ====================
let workoutReferrals = 0;
const workoutMilestones = [
    { count: 1, reward: '1 Woche gratis', icon: '📅', id: 'fitness-reward-1', level: 'Einsteiger', char: '🏋️' },
    { count: 3, reward: 'Personal Training', icon: '🏋️', id: 'fitness-reward-2', level: 'Fortgeschritten', char: '💪' },
    { count: 5, reward: '1 Monat gratis', icon: '🗓️', id: 'fitness-reward-3', level: 'Champion', char: '🏆' },
];

function addWorkoutReferral() {
    if (workoutReferrals >= 5) return;
    
    workoutReferrals++;
    updateWorkoutUI();
    
    // Check milestones
    const milestone = workoutMilestones.find(m => m.count === workoutReferrals);
    if (milestone) {
        unlockWorkoutReward(milestone);
    }
}

function updateWorkoutUI() {
    // Update count
    document.getElementById('workout-count').textContent = workoutReferrals + ' / 5';
    
    // Update progress ring (283 is circumference)
    const progress = (workoutReferrals / 5) * 283;
    document.getElementById('progress-ring').style.strokeDashoffset = 283 - progress;
    
    // Update character and level
    const characters = ['🏃', '🏋️', '🏋️', '💪', '💪', '🏆'];
    const levels = ['Anfänger', 'Einsteiger', 'Einsteiger', 'Fortgeschritten', 'Fortgeschritten', 'Champion'];
    document.getElementById('workout-character').textContent = characters[workoutReferrals];
    document.getElementById('workout-level').textContent = 'Level: ' + levels[workoutReferrals];
    
    // Animate character
    document.getElementById('workout-character').classList.add('animate-muscle-flex');
    setTimeout(() => {
        document.getElementById('workout-character').classList.remove('animate-muscle-flex');
    }, 500);
    
    // Update weights size
    const weightSize = 2 + (workoutReferrals * 0.4);
    document.getElementById('weight-left').style.fontSize = weightSize + 'rem';
    document.getElementById('weight-right').style.fontSize = weightSize + 'rem';
    
    // Disable button at max
    if (workoutReferrals >= 5) {
        document.getElementById('workout-btn').innerHTML = '<i class="fas fa-check"></i> <span>Alle Belohnungen freigeschaltet!</span>';
        document.getElementById('workout-btn').disabled = true;
        document.getElementById('workout-btn').classList.add('opacity-70', 'cursor-not-allowed');
    }
}

function unlockWorkoutReward(milestone) {
    const reward = document.getElementById(milestone.id);
    reward.classList.add('unlocked');
    reward.querySelector('span').textContent = '✓';
    
    // Show modal
    document.getElementById('workout-modal-icon').textContent = milestone.icon;
    document.getElementById('workout-modal-title').textContent = 'Level Up: ' + milestone.level + '!';
    document.getElementById('workout-modal-text').textContent = 'Belohnung: ' + milestone.reward;
    document.getElementById('workout-modal').classList.remove('hidden');
    
    createConfetti();
}

function closeWorkoutModal() {
    document.getElementById('workout-modal').classList.add('hidden');
}

function resetWorkoutProgress() {
    workoutReferrals = 0;
    document.getElementById('workout-count').textContent = '0 / 5';
    document.getElementById('progress-ring').style.strokeDashoffset = 283;
    document.getElementById('workout-character').textContent = '🏃';
    document.getElementById('workout-level').textContent = 'Level: Anfänger';
    document.getElementById('weight-left').style.fontSize = '2rem';
    document.getElementById('weight-right').style.fontSize = '2rem';
    
    // Reset rewards
    document.querySelectorAll('.fitness-reward').forEach(r => {
        r.classList.remove('unlocked');
        r.querySelector('span').textContent = '🔒';
    });
    
    // Reset button
    document.getElementById('workout-btn').innerHTML = '<i class="fas fa-share-alt"></i> <span>Freund einladen</span>';
    document.getElementById('workout-btn').disabled = false;
    document.getElementById('workout-btn').classList.remove('opacity-70', 'cursor-not-allowed');
}

// ==================== ANIMATION 3: LEADERBOARD RACE ====================
let yourScore = 4;
const maxScore = 15;
let leaderboard = [
    { id: 1, name: 'Anna M.', score: 12, isYou: false },
    { id: 2, name: 'Tom S.', score: 10, isYou: false },
    { id: 3, name: 'Lisa K.', score: 8, isYou: false },
    { id: 4, name: 'Max B.', score: 6, isYou: false },
    { id: 5, name: 'Du', score: 4, isYou: true },
];

function climbLeaderboard() {
    if (yourScore >= maxScore) return;
    
    yourScore++;
    
    // Update your score in leaderboard
    const youIndex = leaderboard.findIndex(p => p.isYou);
    leaderboard[youIndex].score = yourScore;
    
    // Sort leaderboard
    leaderboard.sort((a, b) => b.score - a.score);
    
    // Update UI
    updateLeaderboardUI();
    
    // Check if reached #1
    if (leaderboard[0].isYou) {
        setTimeout(() => {
            document.getElementById('winner-celebration').classList.remove('hidden');
            document.getElementById('climb-btn').innerHTML = '<i class="fas fa-crown"></i> <span>Du bist #1!</span>';
            document.getElementById('climb-btn').disabled = true;
            document.getElementById('climb-btn').classList.add('opacity-70', 'cursor-not-allowed');
            createConfetti();
        }, 600);
    }
}

function updateLeaderboardUI() {
    const yourRank = leaderboard.findIndex(p => p.isYou) + 1;
    
    // Update score display
    document.getElementById('score-5').textContent = yourScore;
    
    // Update your bar width
    const barWidth = (yourScore / maxScore) * 100;
    document.getElementById('bar-5').style.width = barWidth + '%';
    
    // Update rank badge
    document.getElementById('your-rank-badge').textContent = yourRank;
    
    // Update status text
    if (yourRank === 1) {
        document.getElementById('your-status').textContent = '🏆 Top-Werber!';
    } else {
        const nextPerson = leaderboard[yourRank - 2];
        const needed = nextPerson.score - yourScore + 1;
        document.getElementById('your-status').textContent = `Noch ${needed} bis Platz ${yourRank - 1}`;
    }
    
    // Animate row movement
    document.getElementById('rank-5').classList.add('moving-up');
    setTimeout(() => {
        document.getElementById('rank-5').classList.remove('moving-up');
    }, 500);
    
    // Update other scores (they slowly increase too for realism)
    if (Math.random() > 0.7 && yourScore > 6) {
        const randomIndex = Math.floor(Math.random() * 4);
        if (!leaderboard[randomIndex].isYou) {
            leaderboard[randomIndex].score++;
            const scoreEl = document.getElementById('score-' + (randomIndex + 1));
            if (scoreEl && !leaderboard[randomIndex].isYou) {
                scoreEl.textContent = leaderboard[randomIndex].score;
            }
        }
    }
}

function resetLeaderboard() {
    yourScore = 4;
    leaderboard = [
        { id: 1, name: 'Anna M.', score: 12, isYou: false },
        { id: 2, name: 'Tom S.', score: 10, isYou: false },
        { id: 3, name: 'Lisa K.', score: 8, isYou: false },
        { id: 4, name: 'Max B.', score: 6, isYou: false },
        { id: 5, name: 'Du', score: 4, isYou: true },
    ];
    
    document.getElementById('score-5').textContent = '4';
    document.getElementById('bar-5').style.width = '33%';
    document.getElementById('your-rank-badge').textContent = '5';
    document.getElementById('your-status').textContent = 'Klicke um aufzusteigen!';
    document.getElementById('winner-celebration').classList.add('hidden');
    
    // Reset button
    document.getElementById('climb-btn').innerHTML = '<i class="fas fa-arrow-up"></i> <span>Freund empfehlen (+1)</span>';
    document.getElementById('climb-btn').disabled = false;
    document.getElementById('climb-btn').classList.remove('opacity-70', 'cursor-not-allowed');
    
    // Reset other scores
    document.getElementById('score-1').textContent = '12';
    document.getElementById('score-2').textContent = '10';
    document.getElementById('score-3').textContent = '8';
    document.getElementById('score-4').textContent = '6';
}

// ==================== CONFETTI HELPER ====================
function createConfetti() {
    const colors = ['#f97316', '#dc2626', '#22c55e', '#eab308', '#3b82f6'];
    for (let i = 0; i < 50; i++) {
        const confetti = document.createElement('div');
        confetti.className = 'confetti';
        confetti.style.left = Math.random() * 100 + 'vw';
        confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
        confetti.style.animationDelay = Math.random() * 2 + 's';
        confetti.style.animationDuration = (2 + Math.random() * 2) + 's';
        document.body.appendChild(confetti);
        
        setTimeout(() => confetti.remove(), 5000);
    }
}

// ==================== INITIALIZE ====================
document.addEventListener('DOMContentLoaded', function() {
    // Start first animation
    restartChatAnimation();
});
</script>

<!-- So funktioniert's -->
<section class="py-12 md:py-20 bg-white dark:bg-slate-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                So einfach funktioniert's
            </h2>
        </div>
        
        <div class="grid md:grid-cols-3 gap-6 md:gap-8">
            <div class="text-center">
                <div class="w-16 h-16 bg-orange-600 rounded-2xl flex items-center justify-center text-white text-2xl font-bold mx-auto mb-4">1</div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Mitglied meldet sich an</h3>
                <p class="text-gray-600 dark:text-gray-400">QR-Code im Studio, Link per E-Mail oder direkt an der Theke – viele Wege zum Programm.</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-orange-600 rounded-2xl flex items-center justify-center text-white text-2xl font-bold mx-auto mb-4">2</div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Link wird geteilt</h3>
                <p class="text-gray-600 dark:text-gray-400">Per WhatsApp an Trainingspartner, Kollegen oder Familie – mit einem Klick.</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-orange-600 rounded-2xl flex items-center justify-center text-white text-2xl font-bold mx-auto mb-4">3</div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Automatische Belohnung</h3>
                <p class="text-gray-600 dark:text-gray-400">Bei Anmeldung eines Neumitglieds gibt's automatisch die Belohnung – ohne Papierkram.</p>
            </div>
        </div>
    </div>
</section>

<!-- Bonus: Saisonales -->
<section class="py-12 md:py-20 bg-gray-50 dark:bg-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-gradient-to-r from-orange-500 to-red-600 rounded-2xl p-8 md:p-12 text-white">
            <div class="grid md:grid-cols-2 gap-8 items-center">
                <div>
                    <h3 class="text-2xl md:text-3xl font-bold mb-4">
                        🔥 Perfekt für Neujahrs-Aktionen
                    </h3>
                    <p class="text-white/90 text-lg mb-6">
                        Januar ist Hauptsaison fürs Gym. Kombinieren Sie Ihr Empfehlungsprogramm mit 
                        Neujahrs-Specials und maximieren Sie Ihre Anmeldungen.
                    </p>
                    <ul class="space-y-3 text-white/90">
                        <li class="flex items-center gap-2">
                            <i class="fas fa-check-circle text-green-300"></i>
                            <span>Doppelte Punkte im Januar</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fas fa-check-circle text-green-300"></i>
                            <span>Bonus-Belohnungen für Top-Werber</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fas fa-check-circle text-green-300"></i>
                            <span>Leaderboard-Wettbewerb</span>
                        </li>
                    </ul>
                </div>
                <div class="text-center">
                    <div class="text-6xl mb-4">🎯</div>
                    <div class="text-4xl font-bold">+67%</div>
                    <div class="text-white/80">mehr Empfehlungen im Januar</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-12 md:py-20 bg-gradient-to-r from-orange-500 to-red-600 text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-2xl md:text-3xl lg:text-4xl font-extrabold mb-4 md:mb-6">
            Bereit für mehr Mitglieder durch Empfehlungen?
        </h2>
        <p class="text-lg md:text-xl text-white/90 mb-6 md:mb-8">
            Starten Sie noch heute und machen Sie Ihre Mitglieder zu Ihren besten Werbern.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="/onboarding" class="btn-white btn-large inline-flex items-center justify-center gap-2">
                <span>Jetzt 7 Tage kostenlos testen</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <p class="text-white/70 mt-6 text-sm">
            Keine Kreditkarte erforderlich · Einrichtung in 5 Minuten · DSGVO-konform
        </p>
    </div>
</section>

<!-- Andere Branchen -->
<section class="py-12 md:py-16 bg-white dark:bg-slate-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h3 class="text-center text-lg font-semibold text-gray-900 dark:text-white mb-8">
            Leadbusiness für andere Branchen
        </h3>
        <div class="flex flex-wrap justify-center gap-3">
            <a href="/branchen/zahnarzt" class="px-4 py-2 bg-gray-100 dark:bg-slate-800 hover:bg-primary-100 dark:hover:bg-primary-900/30 rounded-full text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors text-sm font-medium">
                <i class="fas fa-tooth mr-1"></i> Zahnärzte
            </a>
            <a href="/branchen/friseur" class="px-4 py-2 bg-gray-100 dark:bg-slate-800 hover:bg-primary-100 dark:hover:bg-primary-900/30 rounded-full text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors text-sm font-medium">
                <i class="fas fa-cut mr-1"></i> Friseure
            </a>
            <a href="/branchen/restaurant" class="px-4 py-2 bg-gray-100 dark:bg-slate-800 hover:bg-primary-100 dark:hover:bg-primary-900/30 rounded-full text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors text-sm font-medium">
                <i class="fas fa-utensils mr-1"></i> Restaurants
            </a>
            <a href="/branchen/coach" class="px-4 py-2 bg-gray-100 dark:bg-slate-800 hover:bg-primary-100 dark:hover:bg-primary-900/30 rounded-full text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors text-sm font-medium">
                <i class="fas fa-lightbulb mr-1"></i> Coaches
            </a>
            <a href="/branchen/onlineshop" class="px-4 py-2 bg-gray-100 dark:bg-slate-800 hover:bg-primary-100 dark:hover:bg-primary-900/30 rounded-full text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors text-sm font-medium">
                <i class="fas fa-shopping-cart mr-1"></i> Online-Shops
            </a>
            <a href="/branchen/onlinemarketing" class="px-4 py-2 bg-gray-100 dark:bg-slate-800 hover:bg-primary-100 dark:hover:bg-primary-900/30 rounded-full text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors text-sm font-medium">
                <i class="fas fa-bullhorn mr-1"></i> Online-Marketing
            </a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../../templates/marketing/footer.php'; ?>
