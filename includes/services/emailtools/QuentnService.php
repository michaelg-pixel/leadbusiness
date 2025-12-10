<?php
/**
 * Leadbusiness - Quentn Integration
 * 
 * Quentn API Dokumentation: https://help.quentn.com/de/articles/api
 * 
 * Quentn nutzt API-Key + API-Secret für Authentifizierung.
 * Base URL ist kundenspezifisch: https://[subdomain].quentn.com/public/api/v1/
 */

namespace Leadbusiness\EmailTools;

class QuentnService implements EmailToolInterface
{
    private string $apiKey;
    private string $apiSecret;
    private string $baseUrl;
    
    /**
     * @param string $apiKey Quentn API-Key
     * @param string $apiSecret Quentn API-Secret
     * @param string $subdomain Quentn Subdomain (z.B. "meinefirma" für meinefirma.quentn.com)
     */
    public function __construct(string $apiKey, string $apiSecret, string $subdomain)
    {
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
        
        // Subdomain bereinigen
        $subdomain = preg_replace('/[^a-z0-9-]/i', '', $subdomain);
        $this->baseUrl = "https://{$subdomain}.quentn.com/public/api/v1";
    }
    
    /**
     * API Request ausführen
     */
    private function request(string $method, string $endpoint, array $data = []): array
    {
        $url = $this->baseUrl . $endpoint;
        
        $ch = curl_init();
        
        // Basic Auth mit API-Key:API-Secret
        $auth = base64_encode($this->apiKey . ':' . $this->apiSecret);
        
        $headers = [
            'Content-Type: application/json',
            'Authorization: Basic ' . $auth
        ];
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => $headers,
        ]);
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if (!empty($data)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        } elseif ($method === 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } elseif ($method === 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        } elseif ($method === 'GET' && !empty($data)) {
            $url .= '?' . http_build_query($data);
            curl_setopt($ch, CURLOPT_URL, $url);
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            return ['error' => $error, 'http_code' => 0];
        }
        
        $result = json_decode($response, true) ?? [];
        $result['http_code'] = $httpCode;
        
        if ($httpCode >= 400) {
            $result['error'] = $result['message'] ?? $result['error'] ?? "HTTP Error: $httpCode";
        }
        
        return $result;
    }
    
    /**
     * {@inheritdoc}
     */
    public function testConnection(): array
    {
        // Versuche Tags abzurufen als Verbindungstest
        $response = $this->request('GET', '/terms');
        
        if ($response['http_code'] === 200) {
            return [
                'success' => true,
                'message' => 'Verbindung zu Quentn erfolgreich!'
            ];
        }
        
        if ($response['http_code'] === 401) {
            return [
                'success' => false,
                'message' => 'Authentifizierung fehlgeschlagen. Bitte prüfen Sie API-Key und API-Secret.'
            ];
        }
        
        return [
            'success' => false,
            'message' => $response['error'] ?? 'Verbindung fehlgeschlagen'
        ];
    }
    
    /**
     * {@inheritdoc}
     * Quentn hat keine klassischen Listen, arbeitet mit Tags
     */
    public function getLists(): array
    {
        return [];
    }
    
    /**
     * {@inheritdoc}
     * In Quentn heißen Tags "Terms"
     */
    public function getTags(): array
    {
        $response = $this->request('GET', '/terms');
        
        if (isset($response['error'])) {
            return [];
        }
        
        $tags = [];
        $data = $response['data'] ?? $response;
        
        if (is_array($data)) {
            foreach ($data as $term) {
                if (isset($term['id']) && isset($term['name'])) {
                    $tags[] = [
                        'id' => (string)$term['id'],
                        'name' => $term['name']
                    ];
                }
            }
        }
        
        return $tags;
    }
    
    /**
     * {@inheritdoc}
     */
    public function subscribe(string $email, string $name = '', array $customFields = []): array
    {
        $data = [
            'mail' => $email
        ];
        
        // Name aufteilen falls vorhanden
        if (!empty($name)) {
            $nameParts = explode(' ', $name, 2);
            $data['first_name'] = $nameParts[0] ?? '';
            $data['family_name'] = $nameParts[1] ?? '';
        }
        
        // Custom Fields hinzufügen
        if (!empty($customFields)) {
            $data = array_merge($data, $customFields);
        }
        
        $response = $this->request('POST', '/contact', $data);
        
        if (isset($response['error'])) {
            // Prüfen ob Kontakt bereits existiert (dann ist es okay)
            if (strpos($response['error'], 'already exists') !== false || $response['http_code'] === 409) {
                // Kontakt existiert bereits, versuche zu finden
                $existing = $this->findContactByEmail($email);
                if ($existing) {
                    return [
                        'success' => true,
                        'subscriber_id' => $existing['id'],
                        'message' => 'Kontakt existiert bereits'
                    ];
                }
            }
            
            return [
                'success' => false,
                'subscriber_id' => null,
                'message' => $response['error']
            ];
        }
        
        return [
            'success' => true,
            'subscriber_id' => $response['id'] ?? $response['data']['id'] ?? null,
            'message' => 'Kontakt erfolgreich angelegt'
        ];
    }
    
    /**
     * Kontakt per E-Mail finden
     */
    public function findContactByEmail(string $email): ?array
    {
        $response = $this->request('GET', '/contact', ['mail' => $email]);
        
        if (isset($response['data']) && is_array($response['data']) && count($response['data']) > 0) {
            return $response['data'][0];
        }
        
        return null;
    }
    
    /**
     * {@inheritdoc}
     */
    public function addTag(string $email, string $tagId): array
    {
        // Erst Kontakt-ID finden
        $contact = $this->findContactByEmail($email);
        
        if (!$contact) {
            return [
                'success' => false,
                'message' => 'Kontakt nicht gefunden'
            ];
        }
        
        $contactId = $contact['id'];
        
        // Tag (Term) zum Kontakt hinzufügen
        $response = $this->request('POST', "/contact/{$contactId}/terms", [
            'term_id' => (int)$tagId
        ]);
        
        if (isset($response['error'])) {
            return [
                'success' => false,
                'message' => $response['error']
            ];
        }
        
        return [
            'success' => true,
            'message' => 'Tag erfolgreich hinzugefügt'
        ];
    }
    
    /**
     * Kontakt anlegen und Tag in einem Schritt
     */
    public function subscribeWithTag(string $email, string $tagId, string $name = '', array $customFields = []): array
    {
        // Erst Kontakt anlegen
        $result = $this->subscribe($email, $name, $customFields);
        
        if (!$result['success']) {
            return $result;
        }
        
        // Dann Tag hinzufügen
        if (!empty($tagId)) {
            $tagResult = $this->addTag($email, $tagId);
            
            if (!$tagResult['success']) {
                // Kontakt wurde angelegt, aber Tag fehlgeschlagen
                return [
                    'success' => true,
                    'subscriber_id' => $result['subscriber_id'],
                    'message' => 'Kontakt angelegt, aber Tag konnte nicht gesetzt werden: ' . $tagResult['message']
                ];
            }
        }
        
        return [
            'success' => true,
            'subscriber_id' => $result['subscriber_id'],
            'message' => 'Kontakt mit Tag erfolgreich angelegt'
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function getDisplayName(): string
    {
        return 'Quentn';
    }
    
    /**
     * {@inheritdoc}
     */
    public function getSetupHelp(): array
    {
        return [
            'api_key_label' => 'API-Key',
            'api_secret_label' => 'API-Secret',
            'api_url_label' => 'Subdomain',
            'api_key_help' => 'Finden Sie unter: Quentn → Einstellungen → API',
            'api_secret_help' => 'Das API-Secret aus den Quentn-Einstellungen',
            'api_url_help' => 'Ihre Quentn-Subdomain (z.B. "meinefirma" für meinefirma.quentn.com)',
            'docs_url' => 'https://help.quentn.com/de/articles/api',
            'requires_secret' => true,
            'requires_url' => true,
            'has_lists' => false,
            'has_tags' => true
        ];
    }
}
