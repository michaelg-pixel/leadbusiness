<?php
/**
 * Leadbusiness - KlickTipp Integration
 * 
 * KlickTipp API Dokumentation: https://www.klicktipp.com/de/support/api
 * 
 * KlickTipp nutzt Username/Password für API-Zugang.
 * Man muss sich einloggen um einen Session-Token zu bekommen.
 */

namespace Leadbusiness\EmailTools;

class KlickTippService implements EmailToolInterface
{
    private string $apiUrl = 'https://api.klicktipp.com';
    private string $username;
    private string $password;
    private ?string $sessionId = null;
    private ?string $sessionName = null;
    
    /**
     * @param string $username KlickTipp API-Benutzername
     * @param string $password KlickTipp API-Passwort
     */
    public function __construct(string $username, string $password)
    {
        $this->username = $username;
        $this->password = $password;
    }
    
    /**
     * Bei KlickTipp einloggen und Session-Token holen
     */
    private function login(): bool
    {
        if ($this->sessionId !== null) {
            return true;
        }
        
        $response = $this->request('POST', '/account/login', [
            'username' => $this->username,
            'password' => $this->password
        ], false);
        
        if (isset($response['session_name']) && isset($response['session_id'])) {
            $this->sessionName = $response['session_name'];
            $this->sessionId = $response['session_id'];
            return true;
        }
        
        return false;
    }
    
    /**
     * Session beenden
     */
    public function logout(): void
    {
        if ($this->sessionId !== null) {
            $this->request('POST', '/account/logout');
            $this->sessionId = null;
            $this->sessionName = null;
        }
    }
    
    /**
     * API Request ausführen
     */
    private function request(string $method, string $endpoint, array $data = [], bool $requiresAuth = true): array
    {
        if ($requiresAuth && !$this->login()) {
            return ['error' => 'Login fehlgeschlagen'];
        }
        
        $url = $this->apiUrl . $endpoint;
        
        $ch = curl_init();
        
        $headers = ['Content-Type: application/json'];
        
        // Session-Cookie hinzufügen wenn eingeloggt
        if ($this->sessionId !== null && $this->sessionName !== null) {
            curl_setopt($ch, CURLOPT_COOKIE, $this->sessionName . '=' . $this->sessionId);
        }
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => $headers,
        ]);
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } elseif ($method === 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } elseif ($method === 'GET' && !empty($data)) {
            $url .= '?' . http_build_query($data);
            curl_setopt($ch, CURLOPT_URL, $url);
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            return ['error' => $error];
        }
        
        $result = json_decode($response, true);
        
        if ($httpCode >= 400) {
            return ['error' => $result['error'] ?? "HTTP Error: $httpCode"];
        }
        
        return $result ?? [];
    }
    
    /**
     * {@inheritdoc}
     */
    public function testConnection(): array
    {
        if ($this->login()) {
            $this->logout();
            return [
                'success' => true,
                'message' => 'Verbindung zu KlickTipp erfolgreich!'
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Login fehlgeschlagen. Bitte prüfen Sie Benutzername und Passwort.'
        ];
    }
    
    /**
     * {@inheritdoc}
     * KlickTipp hat keine Listen, nur Tags
     */
    public function getLists(): array
    {
        // KlickTipp arbeitet nicht mit Listen, sondern nur mit Tags
        return [];
    }
    
    /**
     * {@inheritdoc}
     */
    public function getTags(): array
    {
        $response = $this->request('GET', '/tag');
        
        if (isset($response['error'])) {
            return [];
        }
        
        $tags = [];
        foreach ($response as $id => $name) {
            $tags[] = [
                'id' => (string)$id,
                'name' => $name
            ];
        }
        
        return $tags;
    }
    
    /**
     * {@inheritdoc}
     */
    public function subscribe(string $email, string $name = '', array $customFields = []): array
    {
        // Bei KlickTipp nutzt man die "tag" Funktion um einen Kontakt anzulegen
        // Der Kontakt wird automatisch erstellt wenn er nicht existiert
        
        $data = [
            'email' => $email
        ];
        
        // Name aufteilen falls vorhanden
        if (!empty($name)) {
            $nameParts = explode(' ', $name, 2);
            $data['fields'] = [
                'fieldFirstName' => $nameParts[0] ?? '',
                'fieldLastName' => $nameParts[1] ?? ''
            ];
        }
        
        // Custom Fields hinzufügen
        if (!empty($customFields)) {
            $data['fields'] = array_merge($data['fields'] ?? [], $customFields);
        }
        
        $response = $this->request('POST', '/subscriber', $data);
        
        if (isset($response['error'])) {
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
        $response = $this->request('POST', '/subscriber/tag', [
            'email' => $email,
            'tagid' => $tagId
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
     * Subscriber und Tag in einem Schritt (empfohlene Methode)
     */
    public function subscribeWithTag(string $email, string $tagId, string $name = '', array $customFields = []): array
    {
        $data = [
            'email' => $email,
            'tagid' => $tagId
        ];
        
        // Name aufteilen falls vorhanden
        if (!empty($name)) {
            $nameParts = explode(' ', $name, 2);
            $data['fields'] = [
                'fieldFirstName' => $nameParts[0] ?? '',
                'fieldLastName' => $nameParts[1] ?? ''
            ];
        }
        
        // Custom Fields hinzufügen
        if (!empty($customFields)) {
            $data['fields'] = array_merge($data['fields'] ?? [], $customFields);
        }
        
        $response = $this->request('POST', '/subscriber/tag', $data);
        
        if (isset($response['error'])) {
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
        return 'KlickTipp';
    }
    
    /**
     * {@inheritdoc}
     */
    public function getSetupHelp(): array
    {
        return [
            'api_key_label' => 'API-Benutzername',
            'api_secret_label' => 'API-Passwort',
            'api_key_help' => 'Finden Sie unter: KlickTipp → Einstellungen → API',
            'api_secret_help' => 'Das API-Passwort (nicht Ihr Login-Passwort!)',
            'docs_url' => 'https://www.klicktipp.com/de/support/api',
            'requires_secret' => true,
            'has_lists' => false,
            'has_tags' => true
        ];
    }
    
    public function __destruct()
    {
        $this->logout();
    }
}
