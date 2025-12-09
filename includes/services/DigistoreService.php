<?php
/**
 * Leadbusiness - Digistore24 Service
 * 
 * Handles Digistore24 API interactions and helper functions
 */

require_once __DIR__ . '/../../config/digistore.php';

class DigistoreService {
    
    private $config;
    private $apiKey;
    private $baseUrl;
    
    public function __construct() {
        global $digistore_config;
        $this->config = $digistore_config;
        $this->apiKey = $digistore_config['api_key'];
        $this->baseUrl = $digistore_config['sandbox_mode'] 
            ? 'https://www.digistore24.com/api/v1/' 
            : 'https://www.digistore24.com/api/v1/';
    }
    
    /**
     * Get checkout URL for a specific product
     * 
     * @param string $plan 'starter' or 'professional'
     * @param array $prefillData Optional data to prefill in checkout
     * @return string Checkout URL
     */
    public function getCheckoutUrl($plan, $prefillData = []) {
        $productIds = $this->config['product_ids'];
        
        // Produkt-ID basierend auf Plan
        $productId = match($plan) {
            'starter' => $productIds['starter_setup'][0] ?? null,
            'professional' => $productIds['professional_setup'][0] ?? null,
            default => null
        };
        
        if (!$productId) {
            throw new Exception("Invalid plan: $plan");
        }
        
        // Basis-URL
        $url = "https://www.digistore24.com/product/{$productId}";
        
        // Prefill-Parameter hinzufügen
        $params = [];
        
        if (!empty($prefillData['email'])) {
            $params['email'] = $prefillData['email'];
        }
        if (!empty($prefillData['first_name'])) {
            $params['first_name'] = $prefillData['first_name'];
        }
        if (!empty($prefillData['last_name'])) {
            $params['last_name'] = $prefillData['last_name'];
        }
        if (!empty($prefillData['company'])) {
            $params['company'] = $prefillData['company'];
        }
        
        // Affiliate-Parameter
        if (!empty($prefillData['affiliate_id'])) {
            $params['aff'] = $prefillData['affiliate_id'];
        }
        
        // Custom Parameter für Tracking
        if (!empty($prefillData['custom'])) {
            $params['custom'] = $prefillData['custom'];
        }
        
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        
        return $url;
    }
    
    /**
     * Generate Digistore24 Buy Button HTML
     * 
     * @param string $plan 'starter' or 'professional'
     * @param string $buttonText Button text
     * @param string $cssClass Additional CSS classes
     * @return string HTML for button
     */
    public function getBuyButton($plan, $buttonText = 'Jetzt kaufen', $cssClass = '') {
        $url = $this->getCheckoutUrl($plan);
        
        return sprintf(
            '<a href="%s" class="ds24-buy-button %s" target="_blank" rel="noopener">%s</a>',
            htmlspecialchars($url),
            htmlspecialchars($cssClass),
            htmlspecialchars($buttonText)
        );
    }
    
    /**
     * Verify IPN signature
     * 
     * @param array $data IPN data
     * @return bool True if signature is valid
     */
    public function verifyIPNSignature($data) {
        if (empty($data['sha_sign'])) {
            return true; // No signature to verify
        }
        
        $secret = $this->config['ipn_passphrase'];
        
        $signData = $data;
        unset($signData['sha_sign']);
        ksort($signData);
        
        $signString = '';
        foreach ($signData as $key => $value) {
            if ($value !== '') {
                $signString .= $key . '=' . $value . $secret;
            }
        }
        
        $calculatedSign = strtoupper(sha1($signString));
        
        return $calculatedSign === strtoupper($data['sha_sign']);
    }
    
    /**
     * Get plan details from product ID
     * 
     * @param string $productId Digistore24 product ID
     * @return array|null Plan details or null if not found
     */
    public function getPlanFromProductId($productId) {
        $products = $this->config['product_ids'];
        
        // Check Starter Setup
        if (in_array($productId, $products['starter_setup'] ?? [])) {
            return [
                'plan' => 'starter',
                'type' => 'setup',
                'price' => 499 + 49,
                'description' => 'Starter Einrichtung + 1. Monat'
            ];
        }
        
        // Check Professional Setup
        if (in_array($productId, $products['professional_setup'] ?? [])) {
            return [
                'plan' => 'professional',
                'type' => 'setup',
                'price' => 499 + 99,
                'description' => 'Professional Einrichtung + 1. Monat'
            ];
        }
        
        // Check Starter Monthly
        if (in_array($productId, $products['starter_monthly'] ?? [])) {
            return [
                'plan' => 'starter',
                'type' => 'monthly',
                'price' => 49,
                'description' => 'Starter Monatlich'
            ];
        }
        
        // Check Professional Monthly
        if (in_array($productId, $products['professional_monthly'] ?? [])) {
            return [
                'plan' => 'professional',
                'type' => 'monthly',
                'price' => 99,
                'description' => 'Professional Monatlich'
            ];
        }
        
        return null;
    }
    
    /**
     * Make API request to Digistore24
     * 
     * @param string $endpoint API endpoint
     * @param string $method HTTP method
     * @param array $data Request data
     * @return array Response data
     */
    public function apiRequest($endpoint, $method = 'GET', $data = []) {
        $url = $this->baseUrl . ltrim($endpoint, '/');
        
        $headers = [
            'X-DS-API-KEY: ' . $this->apiKey,
            'Content-Type: application/json',
            'Accept: application/json'
        ];
        
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } elseif ($method === 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } elseif ($method === 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        
        curl_close($ch);
        
        if ($error) {
            throw new Exception("Digistore24 API error: $error");
        }
        
        $result = json_decode($response, true);
        
        if ($httpCode >= 400) {
            $errorMessage = $result['error']['message'] ?? 'Unknown error';
            throw new Exception("Digistore24 API error ($httpCode): $errorMessage");
        }
        
        return $result;
    }
    
    /**
     * Get order details from Digistore24
     * 
     * @param string $orderId Order ID
     * @return array Order details
     */
    public function getOrder($orderId) {
        return $this->apiRequest("/order/$orderId");
    }
    
    /**
     * Cancel a rebilling (subscription)
     * 
     * @param string $orderId Order ID
     * @return array Result
     */
    public function cancelRebilling($orderId) {
        return $this->apiRequest("/rebilling/$orderId/stop", 'POST');
    }
    
    /**
     * Resume a rebilling (subscription)
     * 
     * @param string $orderId Order ID
     * @return array Result
     */
    public function resumeRebilling($orderId) {
        return $this->apiRequest("/rebilling/$orderId/resume", 'POST');
    }
    
    /**
     * Get pricing table HTML for landing page
     * 
     * @return string HTML for pricing comparison
     */
    public function getPricingTable() {
        $starterUrl = $this->getCheckoutUrl('starter');
        $professionalUrl = $this->getCheckoutUrl('professional');
        
        return <<<HTML
<div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
    <!-- Starter Plan -->
    <div class="bg-white rounded-2xl border-2 border-gray-200 p-8 hover:border-primary-500 transition-colors">
        <h3 class="text-2xl font-bold mb-2">Starter</h3>
        <p class="text-gray-500 mb-6">Für den Einstieg</p>
        
        <div class="mb-6">
            <span class="text-4xl font-extrabold">49€</span>
            <span class="text-gray-500">/Monat</span>
        </div>
        
        <p class="text-sm text-gray-500 mb-6">+ 499€ einmalige Einrichtung</p>
        
        <ul class="space-y-3 mb-8">
            <li class="flex items-center gap-2 text-gray-600">
                <i class="fas fa-check text-green-500"></i>
                Bis 200 Empfehler
            </li>
            <li class="flex items-center gap-2 text-gray-600">
                <i class="fas fa-check text-green-500"></i>
                3 Belohnungsstufen
            </li>
            <li class="flex items-center gap-2 text-gray-600">
                <i class="fas fa-check text-green-500"></i>
                Eigene Subdomain
            </li>
            <li class="flex items-center gap-2 text-gray-600">
                <i class="fas fa-check text-green-500"></i>
                E-Mail-Support
            </li>
        </ul>
        
        <a href="$starterUrl" class="btn-secondary w-full text-center block" target="_blank" rel="noopener">
            Starter wählen
        </a>
    </div>
    
    <!-- Professional Plan -->
    <div class="bg-white rounded-2xl border-2 border-primary-500 p-8 shadow-xl relative">
        <div class="absolute -top-4 left-1/2 transform -translate-x-1/2 bg-primary-500 text-white px-4 py-1 rounded-full text-sm font-semibold">
            Empfohlen
        </div>
        
        <h3 class="text-2xl font-bold mb-2">Professional</h3>
        <p class="text-gray-500 mb-6">Für wachsende Unternehmen</p>
        
        <div class="mb-6">
            <span class="text-4xl font-extrabold">99€</span>
            <span class="text-gray-500">/Monat</span>
        </div>
        
        <p class="text-sm text-gray-500 mb-6">+ 499€ einmalige Einrichtung</p>
        
        <ul class="space-y-3 mb-8">
            <li class="flex items-center gap-2 text-gray-600">
                <i class="fas fa-check text-green-500"></i>
                Bis 5.000 Empfehler
            </li>
            <li class="flex items-center gap-2 text-gray-600">
                <i class="fas fa-check text-green-500"></i>
                5 Belohnungsstufen
            </li>
            <li class="flex items-center gap-2 text-gray-600">
                <i class="fas fa-check text-green-500"></i>
                Mehrere Kampagnen
            </li>
            <li class="flex items-center gap-2 text-gray-600">
                <i class="fas fa-check text-green-500"></i>
                Lead-Export & API
            </li>
            <li class="flex items-center gap-2 text-gray-600">
                <i class="fas fa-check text-green-500"></i>
                Prioritäts-Support
            </li>
        </ul>
        
        <a href="$professionalUrl" class="btn-primary w-full text-center block" target="_blank" rel="noopener">
            Professional wählen
        </a>
    </div>
</div>
HTML;
    }
    
    /**
     * Check if customer subscription is active
     * 
     * @param string $orderId Digistore24 Order ID
     * @return bool True if subscription is active
     */
    public function isSubscriptionActive($orderId) {
        try {
            $order = $this->getOrder($orderId);
            return ($order['status'] ?? '') === 'active';
        } catch (Exception $e) {
            return false;
        }
    }
}
