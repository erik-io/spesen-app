<?php

declare(strict_types=1);

return [
    'app_name' => 'Digitale Spesenabrechnung',
    'modal' => [
        'submission' => [
            'title' => 'Einreichung bestätigen',
            'body' => 'Möchten Sie diese Spesenabrechnung wirklich einreichen? Sie können sie nach dem Absenden nicht mehr bearbeiten.',
        ],
        'rejection' => [
            'title' => 'Ablehnung bestätigen',
            'body' => 'Möchten Sie diese Spesenabrechnung wirklich ablehnen? Der Mitarbeiter wird Ihren Kommentar sehen.',
        ],
        'approval' => [
            'title' => 'Genehmigung bestätigen',
            'body' => 'Möchten Sie diese Spesenabrechnung wirklich genehmigen?',
        ],
    ],
    'welcome' => [
        'hero' => [
            'title' => 'Verwalten Sie ihre Spesen mühelos',
            'subtitle' => 'Optimieren Sie Spesenerfassung, Genehmigungen und Erstattungen mit unserem intuitiven Spesenverwaltungssystem',
        ],
        'cta' => [
            'get_started' => 'Kostenlos starten',
            'sign_in' => 'Anmelden',
            'go_to_dashboard' => 'Zum Dashboard',
            'section' => [
                'title' => 'Bereit loszulegen?',
                'subtitle' => 'Schließen Sie sich Tausenden von Benutzern an, die unserer Plattform für das Spesenmanagement vertrauen',
                'button' => 'Jetzt Spesen verwalten',
                'dashboard_button' => 'Zu Ihrem Dashboard',
            ],
        ],
        'features' => [
            'title' => 'Leistungsstarke Funktionen',
            'subtitle' => 'Alles was Sie brauchen, um Spesen effizient zu verwalten',
            'easy_submission' => [
                'title' => 'Einfache Einreichung',
                'description' => 'Reichen Sie Spesen in Sekunden mit unserem einfachen und intuitiven Formular ein',
            ],
            'quick_approval' => [
                'title' => 'Schnelle Genehmigung',
                'description' => 'Vorgesetzte können Spesen mit nur einem Klick genehmigen oder ablehnen',
            ],
            'real_time_tracking' => [
                'title' => 'Echtzeit-Verfolgung',
                'description' => 'Verfolgen Sie den Status Ihrer Spesen in Echtzeit',
            ],
            'multilingual' => [
                'title' => 'Mehrsprachige Unterstützung',
                'description' => 'Verfügbar in Englisch und Deutsch mit lokalisierten Datums- und Währungsformaten',
            ],
            'dark_mode' => [
                'title' => 'Dunkler Modus',
                'description' => 'Schonend für die Augen mit unserer schönen Dark-Mode-Oberfläche',
            ],
            'secure' => [
                'title' => 'Sicher & Privat',
                'description' => 'Ihre Daten sind mit branchenüblichen Sicherheitsstandards geschützt',
            ],
        ],
        'how_it_works' => [
            'title' => 'So funktioniert es',
            'subtitle' => 'Drei einfache Schritte zur Verwaltung Ihrer Spesen',
            'step1' => [
                'title' => 'Spesen einreichen',
                'description' => 'Geben Sie Ihre Spesendetails ein, einschließlich Datum, Betrag und Kostenstelle',
            ],
            'step2' => [
                'title' => 'Genehmigung erhalten',
                'description' => 'Ihr Vorgesetzter prüft und genehmigt oder lehnt Ihre Einreichung ab',
            ],
            'step3' => [
                'title' => 'Status verfolgen',
                'description' => 'Überwachen Sie Ihre Spesen und erhalten Sie sofortige Updates zu ihrem Status',
            ],
        ],
        'footer' => [
            'all_rights_reserved' => 'Alle Rechte vorbehalten.',
            'made_with' => 'Erstellt mit',
            'using_laravel' => 'mit Laravel',
        ],
    ],
    'empty' => [
        'pending' => 'Keine offenen Spesenabrechnungen gefunden.',
        'all' => 'Keine Spesenabrechnungen gefunden.',
    ],
];
