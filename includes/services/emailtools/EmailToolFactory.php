<?php
/**
 * Leadbusiness - E-Mail Tool Factory
 * 
 * Erstellt das richtige Service-Objekt basierend auf dem Tool-Namen.
 */

namespace Leadbusiness\EmailTools;

require_once __DIR__ . '/EmailToolInterface.php';
require_once __DIR__ . '/KlickTippService.php';
require_once __DIR__ . '/QuentnService.php';
require_once __DIR__ . '/CleverReachService.php';

class EmailToolFactory
{
    /**
     * Verfügbare Tools mit Metadaten
     */
    public static function getAvailableTools(): array
    {
        return [
            'klicktipp' => [
                'name' => 'KlickTipp',
                'logo' => '/assets/images/emailtools/klicktipp.svg',
                'popular' => true,
                'country' => 'DE'
            ],
            'quentn' => [
                'name' => 'Quentn',
                'logo' => '/assets/images/emailtools/quentn.svg',
                'popular' => true,
                'country' => 'DE'
            ],
            'cleverreach' => [
                'name' => 'CleverReach',
                'logo' => '/assets/images/emailtools/cleverreach.svg',
                'popular' => true,
                'country' => 'DE'
            ],
            'activecampaign' => [
                'name' => 'ActiveCampaign',
                'logo' => '/assets/images/emailtools/activecampaign.svg',
                'popular' => false,
                'country' => 'US'
            ],
            'brevo' => [
                'name' => 'Brevo (Sendinblue)',
                'logo' => '/assets/images/emailtools/brevo.svg',
                'popular' => false,
                'country' => 'FR'
            ],
            'getresponse' => [
                'name' => 'GetResponse',
                'logo' => '/assets/images/emailtools/getresponse.svg',
                'popular' => false,
                'country' => 'PL'
            ],
            'mailchimp' => [
                'name' => 'Mailchimp',
                'logo' => '/assets/images/emailtools/mailchimp.svg',
                'popular' => false,
                'country' => 'US'
            ]
        ];
    }
    
    /**
     * Service-Instanz erstellen
     * 
     * @param string $toolName Name des Tools
     * @param array $credentials API-Credentials
     * @return EmailToolInterface|null
     */
    public static function create(string $toolName, array $credentials): ?EmailToolInterface
    {
        switch ($toolName) {
            case 'klicktipp':
                if (empty($credentials['api_key']) || empty($credentials['api_secret'])) {
                    return null;
                }
                return new KlickTippService(
                    $credentials['api_key'],
                    $credentials['api_secret']
                );
                
            case 'quentn':
                if (empty($credentials['api_key']) || empty($credentials['api_secret']) || empty($credentials['api_url'])) {
                    return null;
                }
                return new QuentnService(
                    $credentials['api_key'],
                    $credentials['api_secret'],
                    $credentials['api_url']
                );
                
            case 'cleverreach':
                if (empty($credentials['api_key'])) {
                    return null;
                }
                $service = new CleverReachService($credentials['api_key']);
                if (!empty($credentials['list_id'])) {
                    $service->setListId($credentials['list_id']);
                }
                return $service;
                
            // TODO: Weitere Tools implementieren
            case 'activecampaign':
            case 'brevo':
            case 'getresponse':
            case 'mailchimp':
                // Noch nicht implementiert
                return null;
                
            default:
                return null;
        }
    }
    
    /**
     * Setup-Hilfe für ein Tool abrufen
     */
    public static function getSetupHelp(string $toolName): array
    {
        $service = self::createDummy($toolName);
        if ($service) {
            return $service->getSetupHelp();
        }
        
        return [
            'api_key_label' => 'API-Key',
            'api_key_help' => 'Bitte API-Dokumentation des Anbieters prüfen',
            'requires_secret' => false,
            'requires_url' => false,
            'has_lists' => false,
            'has_tags' => false
        ];
    }
    
    /**
     * Dummy-Instanz erstellen (für Metadaten-Abfrage)
     */
    private static function createDummy(string $toolName): ?EmailToolInterface
    {
        switch ($toolName) {
            case 'klicktipp':
                return new KlickTippService('dummy', 'dummy');
            case 'quentn':
                return new QuentnService('dummy', 'dummy', 'dummy');
            case 'cleverreach':
                return new CleverReachService('dummy');
            default:
                return null;
        }
    }
    
    /**
     * Prüfen ob ein Tool implementiert ist
     */
    public static function isImplemented(string $toolName): bool
    {
        return in_array($toolName, ['klicktipp', 'quentn', 'cleverreach']);
    }
}
