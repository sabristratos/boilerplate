<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\SettingType;
use App\Models\Setting;
use App\Models\SettingGroup;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $settingGroups = [
            [
                'slug' => 'general',
                'icon' => 'cog-6-tooth',
                'order' => 1,
                'name' => ['en' => 'General', 'fr' => 'Général'],
                'description' => ['en' => 'General system settings and preferences', 'fr' => 'Paramètres et préférences générales du système'],
            ],
            [
                'slug' => 'appearance',
                'icon' => 'paint-brush',
                'order' => 2,
                'name' => ['en' => 'Appearance', 'fr' => 'Apparence'],
                'description' => ['en' => 'Customize the look and feel of your application', 'fr' => 'Personnalisez l\'apparence de votre application'],
            ],
            [
                'slug' => 'notifications',
                'icon' => 'bell',
                'order' => 3,
                'name' => ['en' => 'Notifications', 'fr' => 'Notifications'],
                'description' => ['en' => 'Configure how and when you receive notifications', 'fr' => 'Configurez comment et quand vous recevez des notifications'],
            ],
            [
                'slug' => 'security',
                'icon' => 'shield-check',
                'order' => 4,
                'name' => ['en' => 'Security', 'fr' => 'Sécurité'],
                'description' => ['en' => 'Configure security settings for your application', 'fr' => 'Configurez les paramètres de sécurité de votre application'],
            ],
            [
                'slug' => 'email',
                'icon' => 'envelope',
                'order' => 5,
                'name' => ['en' => 'Email', 'fr' => 'E-mail'],
                'description' => ['en' => 'Configure email settings for your application', 'fr' => 'Configurez les paramètres d\'e-mail pour votre application'],
            ],
            [
                'slug' => 'attachments',
                'icon' => 'paper-clip',
                'order' => 6,
                'name' => ['en' => 'Attachments', 'fr' => 'Pièces Jointes'],
                'description' => ['en' => 'Configure settings for file attachments and uploads.', 'fr' => 'Configurez les paramètres pour les pièces jointes et les téléversements.'],
            ],
            [
                'slug' => 'site-identity',
                'icon' => 'identification',
                'order' => 7,
                'name' => ['en' => 'Site Identity', 'fr' => 'Identité du site'],
                'description' => ['en' => 'Configure site identity settings like favicon and copyright.', 'fr' => 'Configurez les paramètres d\'identité du site comme le favicon et le copyright.'],
            ],
            [
                'slug' => 'integrations',
                'icon' => 'puzzle-piece',
                'order' => 8,
                'name' => ['en' => 'Integrations', 'fr' => 'Intégrations'],
                'description' => ['en' => 'Configure settings for third-party integrations.', 'fr' => 'Configurez les paramètres pour les intégrations tierces.'],
            ],
            [
                'slug' => 'contact',
                'icon' => 'user-circle',
                'order' => 9,
                'name' => ['en' => 'Contact Information', 'fr' => 'Coordonnées'],
                'description' => ['en' => 'Configure contact information for your site.', 'fr' => 'Configurez les coordonnées de votre site.'],
            ],
            [
                'slug' => 'seo',
                'icon' => 'chart-pie',
                'order' => 10,
                'name' => ['en' => 'SEO', 'fr' => 'SEO'],
                'description' => ['en' => 'Configure SEO settings for your site.', 'fr' => 'Configurez les paramètres SEO de votre site.'],
            ],
        ];

        foreach ($settingGroups as $groupData) {
            SettingGroup::create($groupData);
        }

        $generalGroup = SettingGroup::where('slug', 'general')->first();
        $appearanceGroup = SettingGroup::where('slug', 'appearance')->first();
        $notificationsGroup = SettingGroup::where('slug', 'notifications')->first();
        $securityGroup = SettingGroup::where('slug', 'security')->first();
        $emailGroup = SettingGroup::where('slug', 'email')->first();
        $attachmentGroup = SettingGroup::where('slug', 'attachments')->first();
        $siteIdentityGroup = SettingGroup::where('slug', 'site-identity')->first();
        $integrationsGroup = SettingGroup::where('slug', 'integrations')->first();
        $contactGroup = SettingGroup::where('slug', 'contact')->first();
        $seoGroup = SettingGroup::where('slug', 'seo')->first();

        $settings = [
            // General Settings
            [
                'setting_group_id' => $generalGroup->id,
                'key' => 'site_name',
                'value' => 'My Awesome Site',
                'type' => SettingType::TEXT,
                'is_public' => true,
                'is_required' => true,
                'order' => 1,
                'display_name' => ['en' => 'Site Name', 'fr' => 'Nom du site'],
                'description' => ['en' => 'The name of your site', 'fr' => 'Le nom de votre site'],
            ],
            [
                'setting_group_id' => $generalGroup->id,
                'key' => 'site_description',
                'value' => 'A TALL Stack Boilerplate',
                'type' => SettingType::TEXTAREA,
                'is_public' => true,
                'is_required' => false,
                'order' => 2,
                'display_name' => ['en' => 'Site Description', 'fr' => 'Description du site'],
                'description' => ['en' => 'A brief description of your site', 'fr' => 'Une brève description de votre site'],
            ],
            [
                'setting_group_id' => $generalGroup->id,
                'key' => 'default_language',
                'value' => 'en',
                'type' => SettingType::SELECT,
                'is_public' => true,
                'is_required' => true,
                'order' => 3,
                'display_name' => ['en' => 'Default Language', 'fr' => 'Langue par défaut'],
                'description' => ['en' => 'The default language for your site', 'fr' => 'La langue par défaut de votre site'],
                'options' => [
                    'en' => ['en' => 'English', 'es' => 'Spanish', 'fr' => 'French'],
                    'fr' => ['en' => 'Anglais', 'es' => 'Espagnol', 'fr' => 'Français'],
                ]
            ],
            [
                'setting_group_id' => $generalGroup->id,
                'key' => 'available_languages',
                'value' => json_encode(['en', 'fr']),
                'type' => SettingType::MULTISELECT,
                'is_public' => true,
                'is_required' => true,
                'order' => 4,
                'display_name' => ['en' => 'Available Languages', 'fr' => 'Langues disponibles'],
                'description' => ['en' => 'The languages available on your site', 'fr' => 'Les langues disponibles sur votre site'],
                'options' => [
                    'en' => ['en' => 'English', 'fr' => 'French'],
                    'fr' => ['en' => 'Anglais', 'fr' => 'Français'],
                ]
            ],

            // Appearance Settings
            [
                'setting_group_id' => $appearanceGroup->id,
                'key' => 'theme',
                'value' => 'light',
                'type' => SettingType::SELECT,
                'is_public' => true,
                'is_required' => true,
                'order' => 1,
                'display_name' => ['en' => 'Theme', 'fr' => 'Thème'],
                'description' => ['en' => 'The theme for your application', 'fr' => 'Le thème de votre application'],
                'options' => [
                    'en' => ['light' => 'Light', 'dark' => 'Dark', 'system' => 'System Default'],
                    'fr' => ['light' => 'Clair', 'dark' => 'Sombre', 'system' => 'Défaut du système'],
                ]
            ],
            [
                'setting_group_id' => $appearanceGroup->id,
                'key' => 'primary_color',
                'value' => 'blue',
                'type' => SettingType::SELECT,
                'is_public' => true,
                'is_required' => true,
                'order' => 2,
                'display_name' => ['en' => 'Primary Color', 'fr' => 'Couleur principale'],
                'description' => ['en' => 'The primary color for your application', 'fr' => 'La couleur principale de votre application'],
                'options' => [
                    'en' => ['blue' => 'Blue', 'green' => 'Green', 'purple' => 'Purple', 'red' => 'Red'],
                    'fr' => ['blue' => 'Bleu', 'green' => 'Vert', 'purple' => 'Violet', 'red' => 'Rouge'],
                ]
            ],
            [
                'setting_group_id' => $appearanceGroup->id,
                'key' => 'show_logo_in_header',
                'value' => '1',
                'type' => SettingType::CHECKBOX,
                'is_public' => true,
                'is_required' => false,
                'order' => 3,
                'display_name' => ['en' => 'Show Logo in Header', 'fr' => 'Afficher le logo dans l\'en-tête'],
                'description' => ['en' => 'Whether to show the logo in the header', 'fr' => 'Indique s\'il faut afficher le logo dans l\'en-tête'],
            ],
            [
                'setting_group_id' => $appearanceGroup->id,
                'key' => 'enable_dark_mode',
                'value' => '1',
                'type' => SettingType::CHECKBOX,
                'is_public' => true,
                'is_required' => false,
                'order' => 4,
                'display_name' => ['en' => 'Enable Dark Mode', 'fr' => 'Activer le mode sombre'],
                'description' => ['en' => 'Whether to enable dark mode for the application', 'fr' => 'Indique s\'il faut activer le mode sombre pour l\'application'],
            ],

            // Notification Settings
            [
                'setting_group_id' => $notificationsGroup->id,
                'key' => 'email_notifications',
                'value' => '1',
                'type' => SettingType::CHECKBOX,
                'is_public' => false,
                'is_required' => false,
                'order' => 1,
                'display_name' => ['en' => 'Email Notifications', 'fr' => 'Notifications par e-mail'],
                'description' => ['en' => 'Whether to send email notifications', 'fr' => 'Indique s\'il faut envoyer des notifications par e-mail'],
            ],
            [
                'setting_group_id' => $notificationsGroup->id,
                'key' => 'browser_notifications',
                'value' => '0',
                'type' => SettingType::CHECKBOX,
                'is_public' => false,
                'is_required' => false,
                'order' => 2,
                'display_name' => ['en' => 'Browser Notifications', 'fr' => 'Notifications du navigateur'],
                'description' => ['en' => 'Whether to send browser notifications', 'fr' => 'Indique s\'il faut envoyer des notifications de navigateur'],
            ],
            [
                'setting_group_id' => $notificationsGroup->id,
                'key' => 'mobile_push_notifications',
                'value' => '0',
                'type' => SettingType::CHECKBOX,
                'is_public' => false,
                'is_required' => false,
                'order' => 3,
                'display_name' => ['en' => 'Mobile Push Notifications', 'fr' => 'Notifications push mobiles'],
                'description' => ['en' => 'Whether to send mobile push notifications', 'fr' => 'Indique s\'il faut envoyer des notifications push mobiles'],
            ],
            [
                'setting_group_id' => $notificationsGroup->id,
                'key' => 'notification_frequency',
                'value' => 'immediately',
                'type' => SettingType::SELECT,
                'is_public' => false,
                'is_required' => true,
                'order' => 4,
                'display_name' => ['en' => 'Notification Frequency', 'fr' => 'Fréquence des notifications'],
                'description' => ['en' => 'How often to send notifications', 'fr' => 'À quelle fréquence envoyer les notifications'],
                'options' => [
                    'en' => ['immediately' => 'Immediately', 'hourly' => 'Hourly Digest', 'daily' => 'Daily Digest', 'weekly' => 'Weekly Digest'],
                    'fr' => ['immediately' => 'Immédiatement', 'hourly' => 'Résumé horaire', 'daily' => 'Résumé quotidien', 'weekly' => 'Résumé hebdomadaire'],
                ]
            ],

            // Security Settings
            [
                'setting_group_id' => $securityGroup->id,
                'key' => 'require_two_factor_auth',
                'value' => '0',
                'type' => SettingType::CHECKBOX,
                'is_public' => false,
                'is_required' => false,
                'order' => 1,
                'display_name' => ['en' => 'Require Two-Factor Authentication', 'fr' => 'Exiger l\'authentification à deux facteurs'],
                'description' => ['en' => 'Whether to require two-factor authentication for all users', 'fr' => 'Indique s\'il faut exiger l\'authentification à deux facteurs pour tous les utilisateurs'],
            ],
            [
                'setting_group_id' => $securityGroup->id,
                'key' => 'session_timeout',
                'value' => '30',
                'type' => SettingType::SELECT,
                'is_public' => false,
                'is_required' => true,
                'order' => 2,
                'display_name' => ['en' => 'Session Timeout', 'fr' => 'Délai d\'expiration de la session'],
                'description' => ['en' => 'How long before a session times out (in minutes)', 'fr' => 'Durée avant l\'expiration d\'une session (en minutes)'],
                'options' => [
                    'en' => ['15' => '15 minutes', '30' => '30 minutes', '60' => '1 hour', '120' => '2 hours'],
                    'fr' => ['15' => '15 minutes', '30' => '30 minutes', '60' => '1 heure', '120' => '2 heures'],
                ]
            ],
            [
                'setting_group_id' => $securityGroup->id,
                'key' => 'log_failed_login_attempts',
                'value' => '1',
                'type' => SettingType::CHECKBOX,
                'is_public' => false,
                'is_required' => false,
                'order' => 3,
                'display_name' => ['en' => 'Log Failed Login Attempts', 'fr' => 'Journaliser les tentatives de connexion échouées'],
                'description' => ['en' => 'Whether to log failed login attempts', 'fr' => 'Indique s\'il faut journaliser les tentatives de connexion échouées'],
            ],

            // Email Settings
            [
                'setting_group_id' => $emailGroup->id,
                'key' => 'mail_mailer',
                'value' => 'smtp',
                'type' => SettingType::SELECT,
                'is_public' => false,
                'is_required' => true,
                'order' => 1,
                'display_name' => ['en' => 'Mail Driver', 'fr' => 'Pilote de messagerie'],
                'description' => ['en' => 'The mail driver to use for sending emails', 'fr' => 'Le pilote de messagerie à utiliser pour l\'envoi d\'e-mails'],
                'options' => [
                    'en' => ['smtp' => 'SMTP', 'sendmail' => 'Sendmail', 'mailgun' => 'Mailgun', 'ses' => 'Amazon SES', 'postmark' => 'Postmark', 'log' => 'Log', 'array' => 'Array'],
                    'fr' => ['smtp' => 'SMTP', 'sendmail' => 'Sendmail', 'mailgun' => 'Mailgun', 'ses' => 'Amazon SES', 'postmark' => 'Postmark', 'log' => 'Journal', 'array' => 'Tableau'],
                ]
            ],
            [
                'setting_group_id' => $emailGroup->id,
                'key' => 'mail_host',
                'value' => 'smtp.mailtrap.io',
                'type' => SettingType::TEXT,
                'is_public' => false,
                'is_required' => true,
                'order' => 2,
                'display_name' => ['en' => 'SMTP Host', 'fr' => 'Hôte SMTP'],
                'description' => ['en' => 'The SMTP server host', 'fr' => 'L\'hôte du serveur SMTP'],
            ],
            [
                'setting_group_id' => $emailGroup->id,
                'key' => 'mail_port',
                'value' => '2525',
                'type' => SettingType::NUMBER,
                'is_public' => false,
                'is_required' => true,
                'order' => 3,
                'display_name' => ['en' => 'SMTP Port', 'fr' => 'Port SMTP'],
                'description' => ['en' => 'The SMTP server port', 'fr' => 'Le port du serveur SMTP'],
            ],
            [
                'setting_group_id' => $emailGroup->id,
                'key' => 'mail_username',
                'value' => '',
                'type' => SettingType::TEXT,
                'is_public' => false,
                'is_required' => false,
                'order' => 4,
                'display_name' => ['en' => 'SMTP Username', 'fr' => 'Nom d\'utilisateur SMTP'],
                'description' => ['en' => 'The SMTP server username', 'fr' => 'Le nom d\'utilisateur du serveur SMTP'],
            ],
            [
                'setting_group_id' => $emailGroup->id,
                'key' => 'mail_password',
                'value' => '',
                'type' => SettingType::PASSWORD,
                'is_public' => false,
                'is_required' => false,
                'order' => 5,
                'display_name' => ['en' => 'SMTP Password', 'fr' => 'Mot de passe SMTP'],
                'description' => ['en' => 'The SMTP server password', 'fr' => 'Le mot de passe du serveur SMTP'],
            ],
            [
                'setting_group_id' => $emailGroup->id,
                'key' => 'mail_encryption',
                'value' => 'tls',
                'type' => SettingType::SELECT,
                'is_public' => false,
                'is_required' => false,
                'order' => 6,
                'display_name' => ['en' => 'SMTP Encryption', 'fr' => 'Chiffrement SMTP'],
                'description' => ['en' => 'The encryption protocol to use for SMTP', 'fr' => 'Le protocole de chiffrement à utiliser pour SMTP'],
                'options' => [
                    'en' => ['tls' => 'TLS', 'ssl' => 'SSL', '' => 'None'],
                    'fr' => ['tls' => 'TLS', 'ssl' => 'SSL', '' => 'Aucun'],
                ]
            ],
            [
                'setting_group_id' => $emailGroup->id,
                'key' => 'mail_from_address',
                'value' => 'hello@example.com',
                'type' => SettingType::EMAIL,
                'is_public' => false,
                'is_required' => true,
                'order' => 7,
                'display_name' => ['en' => 'From Address', 'fr' => 'Adresse de l\'expéditeur'],
                'description' => ['en' => 'The email address that will be used to send emails', 'fr' => 'L\'adresse e-mail qui sera utilisée pour envoyer des e-mails'],
            ],
            [
                'setting_group_id' => $emailGroup->id,
                'key' => 'mail_from_name',
                'value' => 'Example',
                'type' => SettingType::TEXT,
                'is_public' => false,
                'is_required' => true,
                'order' => 8,
                'display_name' => ['en' => 'From Name', 'fr' => 'Nom de l\'expéditeur'],
                'description' => ['en' => 'The name that will be used to send emails', 'fr' => 'Le nom qui sera utilisé pour envoyer des e-mails'],
            ],

            // Attachments Settings
            [
                'setting_group_id' => $attachmentGroup->id,
                'key' => 'attachments_max_upload_size_kb',
                'value' => '10240',
                'type' => SettingType::NUMBER,
                'is_public' => false,
                'is_required' => true,
                'order' => 1,
                'display_name' => ['en' => 'Max Upload Size (KB)', 'fr' => 'Taille Maximale de Téléversement (Ko)'],
                'description' => ['en' => 'Maximum file size for uploads in kilobytes (e.g., 10240 for 10MB).', 'fr' => 'Taille maximale des fichiers pour les téléversements en kilo-octets (ex: 10240 pour 10 Mo).'],
            ],
            [
                'setting_group_id' => $attachmentGroup->id,
                'key' => 'attachments_allowed_extensions',
                'value' => 'jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,txt',
                'type' => SettingType::TEXT,
                'is_public' => false,
                'is_required' => false,
                'order' => 2,
                'display_name' => ['en' => 'Allowed File Extensions', 'fr' => 'Extensions de Fichiers Autorisées'],
                'description' => ['en' => 'Comma-separated list of allowed file extensions (e.g., jpg,jpeg,png,pdf,doc). Leave empty to allow all. Used for client-side validation.', 'fr' => 'Liste d\'extensions de fichiers autorisées séparées par des virgules (ex: jpg,jpeg,png,pdf,doc). Laisser vide pour tout autoriser. Utilisé pour la validation côté client.'],
            ],
            [
                'setting_group_id' => $attachmentGroup->id,
                'key' => 'attachments_allowed_mime_types',
                'value' => 'image/jpeg,image/png,image/gif,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,text/plain',
                'type' => SettingType::TEXTAREA,
                'is_public' => false,
                'is_required' => false,
                'order' => 3,
                'display_name' => ['en' => 'Allowed MIME Types', 'fr' => 'Types MIME Autorisés'],
                'description' => ['en' => 'Comma-separated list of allowed MIME types (e.g., image/jpeg,application/pdf). Leave empty to allow all. Used for server-side validation.', 'fr' => 'Liste de types MIME autorisés séparés par des virgules (ex: image/jpeg,application/pdf). Laisser vide pour tout autoriser. Utilisé pour la validation côté serveur.'],
            ],
            [
                'setting_group_id' => $attachmentGroup->id,
                'key' => 'attachments_image_optimization_enabled',
                'value' => '1',
                'type' => SettingType::CHECKBOX,
                'is_public' => false,
                'is_required' => false,
                'order' => 4,
                'display_name' => ['en' => 'Enable Image Optimization', 'fr' => 'Activer l\'Optimisation d\'Image'],
                'description' => ['en' => 'Automatically optimize uploaded images (e.g., compression, resizing).', 'fr' => 'Optimiser automatiquement les images téléversées (ex: compression, redimensionnement).'],
            ],
            [
                'setting_group_id' => $attachmentGroup->id,
                'key' => 'attachments_image_quality',
                'value' => '80',
                'type' => SettingType::NUMBER,
                'is_public' => false,
                'is_required' => true,
                'order' => 5,
                'display_name' => ['en' => 'Image Optimization Quality', 'fr' => 'Qualité d\'Optimisation d\'Image'],
                'description' => ['en' => 'Default quality for image optimization (1-100). Higher is better quality but larger file size.', 'fr' => 'Qualité par défaut pour l\'optimisation d\'image (1-100). Une valeur plus élevée signifie une meilleure qualité mais une taille de fichier plus grande.'],
            ],
            [
                'setting_group_id' => $attachmentGroup->id,
                'key' => 'attachments_image_max_width',
                'value' => '1920',
                'type' => SettingType::NUMBER,
                'is_public' => false,
                'is_required' => false,
                'order' => 6,
                'display_name' => ['en' => 'Max Image Width (px)', 'fr' => 'Largeur Maximale de l\'Image (px)'],
                'description' => ['en' => 'Images wider than this will be resized down. Set to 0 or leave empty to disable max width constraint.', 'fr' => 'Les images plus larges que cette valeur seront redimensionnées. Mettre à 0 ou laisser vide pour désactiver la contrainte.'],
            ],
            [
                'setting_group_id' => $attachmentGroup->id,
                'key' => 'attachments_image_max_height',
                'value' => '1080',
                'type' => SettingType::NUMBER,
                'is_public' => false,
                'is_required' => false,
                'order' => 7,
                'display_name' => ['en' => 'Max Image Height (px)', 'fr' => 'Hauteur Maximale de l\'Image (px)'],
                'description' => ['en' => 'Images taller than this will be resized down. Set to 0 or leave empty to disable max height constraint.', 'fr' => 'Les images plus hautes que cette valeur seront redimensionnées. Mettre à 0 ou laisser vide pour désactiver la contrainte.'],
            ],
            [
                'setting_group_id' => $attachmentGroup->id,
                'key' => 'attachments_default_disk',
                'value' => 'public',
                'type' => SettingType::SELECT,
                'is_public' => false,
                'is_required' => true,
                'order' => 8,
                'display_name' => ['en' => 'Default Storage Disk', 'fr' => 'Disque de Stockage par Défaut'],
                'description' => ['en' => 'The default filesystem disk for storing attachments (e.g., public, s3).', 'fr' => 'Le disque du système de fichiers par défaut pour stocker les pièces jointes (ex: public, s3).'],
                'options' => [
                    'en' => ['public' => 'Public', 'local' => 'Local (Private)', 's3' => 'S3 (Amazon AWS)'],
                    'fr' => ['public' => 'Public', 'local' => 'Local (Privé)', 's3' => 'S3 (Amazon AWS)'],
                ]
            ],

            // Site Identity Settings
            [
                'setting_group_id' => $siteIdentityGroup->id,
                'key' => 'favicon',
                'type' => SettingType::FILE,
                'is_public' => true,
                'is_required' => false,
                'order' => 1,
                'display_name' => ['en' => 'Favicon', 'fr' => 'Favicon'],
                'description' => ['en' => 'The favicon for your site.', 'fr' => 'Le favicon de votre site.'],
            ],
            [
                'setting_group_id' => $siteIdentityGroup->id,
                'key' => 'footer_copyright_text',
                'type' => SettingType::TEXTAREA,
                'is_public' => true,
                'is_required' => false,
                'order' => 2,
                'display_name' => ['en' => 'Footer Copyright Text', 'fr' => 'Texte de copyright du pied de page'],
                'description' => ['en' => 'The copyright text displayed in the footer. You can use PHP tags like {year}.', 'fr' => 'Le texte de copyright affiché dans le pied de page. Vous pouvez utiliser des balises PHP comme {year}.'],
                'value' => '© {year} My Awesome Site. All rights reserved.',
            ],

            // Integrations Settings
            [
                'setting_group_id' => $integrationsGroup->id,
                'key' => 'meta_tags',
                'type' => SettingType::TEXTAREA,
                'is_public' => true,
                'is_required' => false,
                'order' => 1,
                'display_name' => ['en' => 'Meta Tags', 'fr' => 'Balises Méta'],
                'description' => ['en' => 'Custom meta tags to be added to the head of your site.', 'fr' => 'Balises méta personnalisées à ajouter à l\'en-tête de votre site.'],
            ],
            [
                'setting_group_id' => $integrationsGroup->id,
                'key' => 'google_analytics_id',
                'type' => SettingType::TEXT,
                'is_public' => true,
                'is_required' => false,
                'order' => 2,
                'display_name' => ['en' => 'Google Analytics 4 ID', 'fr' => 'ID Google Analytics 4'],
                'description' => ['en' => 'Your Google Analytics 4 measurement ID (e.g., G-XXXXXXXXXX).', 'fr' => 'Votre ID de mesure Google Analytics 4 (par exemple, G-XXXXXXXXXX).'],
            ],
            [
                'setting_group_id' => $integrationsGroup->id,
                'key' => 'google_tag_manager_id',
                'type' => SettingType::TEXT,
                'is_public' => true,
                'is_required' => false,
                'order' => 3,
                'display_name' => ['en' => 'Google Tag Manager ID', 'fr' => 'ID Google Tag Manager'],
                'description' => ['en' => 'Your Google Tag Manager container ID (e.g., GTM-XXXXXXX).', 'fr' => 'Votre ID de conteneur Google Tag Manager (par exemple, GTM-XXXXXXX).'],
            ],
            [
                'setting_group_id' => $integrationsGroup->id,
                'key' => 'custom_header_code',
                'type' => SettingType::TEXTAREA,
                'is_public' => true,
                'is_required' => false,
                'order' => 4,
                'display_name' => ['en' => 'Custom Header Code', 'fr' => 'Code d\'en-tête personnalisé'],
                'description' => ['en' => 'Custom code to be added to the head of your site (e.g., for analytics, verification).', 'fr' => 'Code personnalisé à ajouter à l\'en-tête de votre site (par exemple, pour les analyses, la vérification).'],
            ],
            [
                'setting_group_id' => $integrationsGroup->id,
                'key' => 'custom_body_code',
                'type' => SettingType::TEXTAREA,
                'is_public' => true,
                'is_required' => false,
                'order' => 5,
                'display_name' => ['en' => 'Custom Body Code', 'fr' => 'Code de corps personnalisé'],
                'description' => ['en' => 'Custom code to be added to the beginning of the body tag.', 'fr' => 'Code personnalisé à ajouter au début de la balise body.'],
            ],

            // Contact Information Settings
            [
                'setting_group_id' => $contactGroup->id,
                'key' => 'primary_phone',
                'type' => SettingType::TEXT,
                'is_public' => true,
                'is_required' => false,
                'order' => 1,
                'display_name' => ['en' => 'Primary Phone', 'fr' => 'Téléphone principal'],
            ],
            [
                'setting_group_id' => $contactGroup->id,
                'key' => 'secondary_phone',
                'type' => SettingType::TEXT,
                'is_public' => true,
                'is_required' => false,
                'order' => 2,
                'display_name' => ['en' => 'Secondary Phone', 'fr' => 'Téléphone secondaire'],
            ],
            [
                'setting_group_id' => $contactGroup->id,
                'key' => 'primary_email',
                'type' => SettingType::TEXT,
                'is_public' => true,
                'is_required' => false,
                'order' => 3,
                'display_name' => ['en' => 'Primary Email', 'fr' => 'E-mail principal'],
            ],
            [
                'setting_group_id' => $contactGroup->id,
                'key' => 'secondary_email',
                'type' => SettingType::TEXT,
                'is_public' => true,
                'is_required' => false,
                'order' => 4,
                'display_name' => ['en' => 'Secondary Email', 'fr' => 'E-mail secondaire'],
            ],
            [
                'setting_group_id' => $contactGroup->id,
                'key' => 'address',
                'type' => SettingType::TEXTAREA,
                'is_public' => true,
                'is_required' => false,
                'order' => 5,
                'display_name' => ['en' => 'Address', 'fr' => 'Adresse'],
            ],
            [
                'setting_group_id' => $contactGroup->id,
                'key' => 'google_maps_embed',
                'type' => SettingType::TEXTAREA,
                'is_public' => true,
                'is_required' => false,
                'order' => 6,
                'display_name' => ['en' => 'Google Maps Embed', 'fr' => 'Intégration Google Maps'],
                'description' => ['en' => 'The HTML embed code for your Google Maps location.', 'fr' => 'Le code d\'intégration HTML pour votre emplacement Google Maps.'],
            ],

            // SEO Settings
            [
                'setting_group_id' => $seoGroup->id,
                'key' => 'seo_title_template',
                'type' => SettingType::TEXT,
                'is_public' => true,
                'is_required' => false,
                'order' => 1,
                'display_name' => ['en' => 'SEO Title Template', 'fr' => 'Modèle de titre SEO'],
                'description' => ['en' => 'Template for SEO titles. Use {title} and {site_name}.', 'fr' => 'Modèle pour les titres SEO. Utilisez {title} et {site_name}.'],
                'value' => '{title} - {site_name}',
            ],
            [
                'setting_group_id' => $seoGroup->id,
                'key' => 'seo_description_template',
                'type' => SettingType::TEXTAREA,
                'is_public' => true,
                'is_required' => false,
                'order' => 2,
                'display_name' => ['en' => 'SEO Description Template', 'fr' => 'Modèle de description SEO'],
                'description' => ['en' => 'Template for SEO meta descriptions. Use {description}.', 'fr' => 'Modèle pour les méta-descriptions SEO. Utilisez {description}.'],
            ],
            [
                'setting_group_id' => $seoGroup->id,
                'key' => 'seo_keywords',
                'type' => SettingType::TEXTAREA,
                'is_public' => true,
                'is_required' => false,
                'order' => 3,
                'display_name' => ['en' => 'SEO Keywords', 'fr' => 'Mots-clés SEO'],
                'description' => ['en' => 'Default keywords for your site, separated by commas.', 'fr' => 'Mots-clés par défaut pour votre site, séparés par des virgules.'],
            ],
            [
                'setting_group_id' => $seoGroup->id,
                'key' => 'seo_use_sitemap',
                'type' => SettingType::CHECKBOX,
                'is_public' => true,
                'is_required' => false,
                'order' => 4,
                'display_name' => ['en' => 'Use Sitemap', 'fr' => 'Utiliser le sitemap'],
                'description' => ['en' => 'Whether to generate and use a sitemap.', 'fr' => 'Indique s\'il faut générer et utiliser un sitemap.'],
                'value' => '1',
            ],
        ];

        foreach ($settings as $settingData) {
            $options = null;
            if (isset($settingData['options'])) {
                $options = $settingData['options'];
                unset($settingData['options']);
            }

            $setting = Setting::create($settingData);

            if ($options) {
                foreach ($options as $locale => $values) {
                    $setting->setTranslation('options', $locale, $values);
                }
                $setting->save();
            }
        }
    }
}
