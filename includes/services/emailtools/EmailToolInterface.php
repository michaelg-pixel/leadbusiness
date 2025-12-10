<?php
/**
 * Leadbusiness - E-Mail Tool Interface
 * 
 * Gemeinsames Interface für alle E-Mail-Tool Integrationen.
 * Leads werden nur passiv synchronisiert - Marketing läuft über Mailgun.
 */

namespace Leadbusiness\EmailTools;

interface EmailToolInterface
{
    /**
     * Verbindung testen
     * 
     * @return array ['success' => bool, 'message' => string]
     */
    public function testConnection(): array;
    
    /**
     * Verfügbare Listen abrufen
     * 
     * @return array [['id' => string, 'name' => string], ...]
     */
    public function getLists(): array;
    
    /**
     * Verfügbare Tags abrufen
     * 
     * @return array [['id' => string, 'name' => string], ...]
     */
    public function getTags(): array;
    
    /**
     * Lead zur Liste hinzufügen (Subscribe)
     * 
     * @param string $email E-Mail-Adresse
     * @param string $name Name (optional)
     * @param array $customFields Zusätzliche Felder
     * @return array ['success' => bool, 'subscriber_id' => string|null, 'message' => string]
     */
    public function subscribe(string $email, string $name = '', array $customFields = []): array;
    
    /**
     * Tag zu Subscriber hinzufügen
     * 
     * @param string $email E-Mail-Adresse
     * @param string $tagId Tag-ID
     * @return array ['success' => bool, 'message' => string]
     */
    public function addTag(string $email, string $tagId): array;
    
    /**
     * Tool-Name für Anzeige
     * 
     * @return string
     */
    public function getDisplayName(): string;
    
    /**
     * Tool-spezifische Einrichtungshilfe
     * 
     * @return array ['api_key_label' => string, 'api_key_help' => string, 'docs_url' => string]
     */
    public function getSetupHelp(): array;
}
