<?php
/**
 * Leadbusiness - CleverReach Integration
 * 
 * CleverReach API Dokumentation: https://rest.cleverreach.com/docs/
 * 
 * CleverReach nutzt OAuth2 oder API-Token für Authentifizierung.
 * Wir nutzen den einfacheren API-Token Ansatz.
 */

namespace Leadbusiness\EmailTools;

class CleverReachService implements EmailToolInterface
{
    private string $apiUrl = 'https://rest.cleverreach.com/v3';
    private string $apiToken;
    
    /**
     * @param string $apiToken CleverReach API-Token
     */
    public function __construct(string $apiToken)
    {
        $this->apiToken = $apiToken;
    }
    
    /**
     * API Request ausführen
     */
    private function request(string $method, string $endpoint, array $data = []): array
    {
        $url = $this->apiUrl . $endpoint;
        
        $ch = curl_init();
        
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiToken
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
            $result['error'] = $result['error']['message'] ?? $result['message'] ?? "HTTP Error: $httpCode";
        }
        
        return $result;
    }
    
    /**
     * {@inheritdoc}
     */
    public function testConnection(): array
    {
        $response = $this->request('GET', '/debug/whoami');
        
        if ($response['http_code'] === 200 && isset($response['id'])) {
            return [
                'success' => true,
                'message' => 'Verbindung zu CleverReach erfolgreich!'
            ];
        }
        
        if ($response['http_code'] === 401) {
            return [
                'success' => false,
                'message' => 'API-Token ungültig. Bitte prüfen Sie den Token.'
            ];
        }
        
        return [
            'success' => false,
            'message' => $response['error'] ?? 'Verbindung fehlgeschlagen'
        ];
    }
    
    /**
     * {@inheritdoc}
     * CleverReach arbeitet mit Gruppen (Listen)
     */
    public function getLists(): array
    {
        $response = $this->request('GET', '/groups');
        
        if (isset($response['error']) || !is_array($response)) {
            return [];
        }
        
        $lists = [];
        foreach ($response as $group) {
            if (isset($group['id']) && isset($group['name'])) {
                $lists[] = [
                    'id' => (string)$group['id'],
                    'name' => $group['name']
                ];
            }
        }
        
        return $lists;
    }
    
    /**
     * {@inheritdoc}
     * CleverReach Tags sind global
     */
    public function getTags(): array
    {
        $response = $this->request('GET', '/tags');
        
        if (isset($response['error']) || !is_array($response)) {
            return [];
        }
        
        $tags = [];
        foreach ($response as $tag) {
            if (is_string($tag)) {
                $tags[] = [
                    'id' => $tag,
                    'name' => $tag
                ];
            } elseif (isset($tag['tag'])) {
                $tags[] = [
                    'id' => $tag['tag'],
                    'name' => $tag['tag']
                ];
            }
        }
        
        return $tags;
    }
    
    private ?string $listId = null;
    
    /**
     * Liste/Gruppe setzen für Subscribe
     */
    public function setListId(string $listId): void
    {
        $this->listId = $listId;
    }
    
    /**
     * {@inheritdoc}
     */
    public function subscribe(string $email, string $name = '', array $customFields = []): array
    {
        if (!$this->listId) {
            return [
                'success' => false,
                'subscriber_id' => null,
                'message' => 'Keine Liste/Gruppe ausgewählt'
            ];
        }
        
        $data = [
            'email' => $email,
            'registered' => time(),
            'activated' => time(), // Direkt aktiviert (Double-Opt-In wurde bereits bei Leadbusiness gemacht)
            'source' => 'Leadbusiness Empfehlungsprogramm'
        ];
        
        // Name aufteilen falls vorhanden
        if (!empty($name)) {
            $nameParts = explode(' ', $name, 2);
            $data['global_attributes'] = [
                'firstname' => $nameParts[0] ?? '',
                'lastname' => $nameParts[1] ?? ''
            ];
        }
        
        // Custom Fields hinzufügen
        if (!empty($customFields)) {
            $data['global_attributes'] = array_merge($data['global_attributes'] ?? [], $customFields);
        }
        
        $response = $this->request('POST', "/groups/{$this->listId}/receivers", $data);
        
        if (isset($response['error'])) {
            // Prüfen ob bereits vorhanden
            if (strpos($response['error'], 'duplicate') !== false || $response['http_code'] === 409) {
                return [
                    'success' => true,
                    'subscriber_id' => null,
                    'message' => 'Kontakt existiert bereits in der Liste'
                ];
            }
            
            return [
                'success' => false,
                'subscriber_id' => null,
                'message' => $response['error']
            ];
        }
        
        return [
            'success' => true,
            'subscriber_id' => $response['id'] ?? null,
            'message' => 'Kontakt erfolgreich angelegt'
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function addTag(string $email, string $tagId): array
    {
        if (!$this->listId) {
            return [
                'success' => false,
                'message' => 'Keine Liste/Gruppe ausgewählt'
            ];
        }
        
        // Kontakt in der Gruppe finden
        $response = $this->request('GET', "/groups/{$this->listId}/receivers/{$email}");
        
        if (isset($response['error'])) {
            return [
                'success' => false,
                'message' => 'Kontakt nicht gefunden: ' . $response['error']
            ];
        }
        
        $receiverId = $response['id'] ?? null;
        if (!$receiverId) {
            return [
                'success' => false,
                'message' => 'Kontakt-ID nicht gefunden'
            ];
        }
        
        // Tag hinzufügen
        $tagResponse = $this->request('POST', "/receivers/{$receiverId}/tags", [
            'tag' => $tagId
        ]);
        
        if (isset($tagResponse['error'])) {
            return [
                'success' => false,
                'message' => $tagResponse['error']
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
        // Bei CleverReach können Tags direkt beim Anlegen gesetzt werden
        if (!$this->listId) {
            return [
                'success' => false,
                'subscriber_id' => null,
                'message' => 'Keine Liste/Gruppe ausgewählt'
            ];
        }
        
        $data = [
            'email' => $email,
            'registered' => time(),
            'activated' => time(),
            'source' => 'Leadbusiness Empfehlungsprogramm'
        ];
        
        if (!empty($tagId)) {
            $data['tags'] = [$tagId];
        }
        
        if (!empty($name)) {
            $nameParts = explode(' ', $name, 2);
            $data['global_attributes'] = [
                'firstname' => $nameParts[0] ?? '',
                'lastname' => $nameParts[1] ?? ''
            ];
        }
        
        if (!empty($customFields)) {
            $data['global_attributes'] = array_merge($data['global_attributes'] ?? [], $customFields);
        }
        
        $response = $this->request('POST', "/groups/{$this->listId}/receivers", $data);
        
        if (isset($response['error'])) {
            if (strpos($response['error'], 'duplicate') !== false || $response['http_code'] === 409) {
                // Existiert bereits - Tag nachträglich hinzufügen
                if (!empty($tagId)) {
                    $this->addTag($email, $tagId);
                }
                return [
                    'success' => true,
                    'subscriber_id' => null,
                    'message' => 'Kontakt existiert bereits'
                ];
            }
            
            return [
                'success' => false,
                'subscriber_id' => null,
                'message' => $response['error']
            ];
        }
        
        return [
            'success' => true,
            'subscriber_id' => $response['id'] ?? null,
            'message' => 'Kontakt mit Tag erfolgreich angelegt'
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function getDisplayName(): string
    {
        return 'CleverReach';
    }
    
    /**
     * {@inheritdoc}
     */
    public function getSetupHelp(): array
    {
        return [
            'api_key_label' => 'API-Token',
            'api_key_help' => 'Finden Sie unter: CleverReach → Mein Account → Extras → REST API',
            'docs_url' => 'https://rest.cleverreach.com/docs/',
            'requires_secret' => false,
            'requires_url' => false,
            'has_lists' => true,
            'has_tags' => true
        ];
    }
}
