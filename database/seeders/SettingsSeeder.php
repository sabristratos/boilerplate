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
        $generalGroup = SettingGroup::create([
            'name' => __('General'),
            'slug' => 'general',
            'description' => __('General system settings and preferences'),
            'icon' => 'cog',
            'order' => 1,
        ]);

        $appearanceGroup = SettingGroup::create([
            'name' => __('Appearance'),
            'slug' => 'appearance',
            'description' => __('Customize the look and feel of your application'),
            'icon' => 'paint-brush',
            'order' => 2,
        ]);

        $notificationsGroup = SettingGroup::create([
            'name' => __('Notifications'),
            'slug' => 'notifications',
            'description' => __('Configure how and when you receive notifications'),
            'icon' => 'bell',
            'order' => 3,
        ]);

        $securityGroup = SettingGroup::create([
            'name' => __('Security'),
            'slug' => 'security',
            'description' => __('Configure security settings for your application'),
            'icon' => 'shield',
            'order' => 4,
        ]);

        $emailGroup = SettingGroup::create([
            'name' => __('Email'),
            'slug' => 'email',
            'description' => __('Configure email settings for your application'),
            'icon' => 'envelope',
            'order' => 5,
        ]);

        $attachmentGroup = SettingGroup::create([
            'name' => __('Attachments'),
            'slug' => 'attachments',
            'description' => __('Configure settings for file attachments and uploads.'),
            'icon' => 'paper-clip',
            'order' => 6,
        ]);

        Setting::create([
            'setting_group_id' => $generalGroup->id,
            'key' => 'site_name',
            'display_name' => __('Site Name'),
            'description' => __('The name of your site'),
            'value' => 'My Awesome Site',
            'type' => SettingType::TEXT,
            'is_public' => true,
            'is_required' => true,
            'order' => 1,
        ]);

        Setting::create([
            'setting_group_id' => $generalGroup->id,
            'key' => 'site_description',
            'display_name' => __('Site Description'),
            'description' => __('A brief description of your site'),
            'value' => 'A TALL Stack Boilerplate',
            'type' => SettingType::TEXTAREA,
            'is_public' => true,
            'is_required' => false,
            'order' => 2,
        ]);

        Setting::create([
            'setting_group_id' => $generalGroup->id,
            'key' => 'default_language',
            'display_name' => __('Default Language'),
            'description' => __('The default language for your site'),
            'value' => 'en',
            'type' => SettingType::SELECT,
            'options' => [
                'en' => __('English'),
                'es' => __('Spanish'),
                'fr' => __('French'),
            ],
            'is_public' => true,
            'is_required' => true,
            'order' => 3,
        ]);

        Setting::create([
            'setting_group_id' => $appearanceGroup->id,
            'key' => 'theme',
            'display_name' => __('Theme'),
            'description' => __('The theme for your application'),
            'value' => 'light',
            'type' => SettingType::SELECT,
            'options' => [
                'light' => __('Light'),
                'dark' => __('Dark'),
                'system' => __('System Default'),
            ],
            'is_public' => true,
            'is_required' => true,
            'order' => 1,
        ]);

        Setting::create([
            'setting_group_id' => $appearanceGroup->id,
            'key' => 'primary_color',
            'display_name' => __('Primary Color'),
            'description' => __('The primary color for your application'),
            'value' => 'blue',
            'type' => SettingType::SELECT,
            'options' => [
                'blue' => __('Blue'),
                'green' => __('Green'),
                'purple' => __('Purple'),
                'red' => __('Red'),
            ],
            'is_public' => true,
            'is_required' => true,
            'order' => 2,
        ]);

        Setting::create([
            'setting_group_id' => $appearanceGroup->id,
            'key' => 'show_logo_in_header',
            'display_name' => __('Show Logo in Header'),
            'description' => __('Whether to show the logo in the header'),
            'value' => '1',
            'type' => SettingType::CHECKBOX,
            'is_public' => true,
            'is_required' => false,
            'order' => 3,
        ]);

        Setting::create([
            'setting_group_id' => $appearanceGroup->id,
            'key' => 'enable_dark_mode',
            'display_name' => __('Enable Dark Mode'),
            'description' => __('Whether to enable dark mode for the application'),
            'value' => '1',
            'type' => SettingType::CHECKBOX,
            'is_public' => true,
            'is_required' => false,
            'order' => 4,
        ]);

        Setting::create([
            'setting_group_id' => $notificationsGroup->id,
            'key' => 'email_notifications',
            'display_name' => __('Email Notifications'),
            'description' => __('Whether to send email notifications'),
            'value' => '1',
            'type' => SettingType::CHECKBOX,
            'is_public' => false,
            'is_required' => false,
            'order' => 1,
        ]);

        Setting::create([
            'setting_group_id' => $notificationsGroup->id,
            'key' => 'browser_notifications',
            'display_name' => __('Browser Notifications'),
            'description' => __('Whether to send browser notifications'),
            'value' => '0',
            'type' => SettingType::CHECKBOX,
            'is_public' => false,
            'is_required' => false,
            'order' => 2,
        ]);

        Setting::create([
            'setting_group_id' => $notificationsGroup->id,
            'key' => 'mobile_push_notifications',
            'display_name' => __('Mobile Push Notifications'),
            'description' => __('Whether to send mobile push notifications'),
            'value' => '0',
            'type' => SettingType::CHECKBOX,
            'is_public' => false,
            'is_required' => false,
            'order' => 3,
        ]);

        Setting::create([
            'setting_group_id' => $notificationsGroup->id,
            'key' => 'notification_frequency',
            'display_name' => __('Notification Frequency'),
            'description' => __('How often to send notifications'),
            'value' => 'immediately',
            'type' => SettingType::SELECT,
            'options' => [
                'immediately' => __('Immediately'),
                'hourly' => __('Hourly Digest'),
                'daily' => __('Daily Digest'),
                'weekly' => __('Weekly Digest'),
            ],
            'is_public' => false,
            'is_required' => true,
            'order' => 4,
        ]);

        Setting::create([
            'setting_group_id' => $securityGroup->id,
            'key' => 'require_two_factor_auth',
            'display_name' => __('Require Two-Factor Authentication'),
            'description' => __('Whether to require two-factor authentication for all users'),
            'value' => '0',
            'type' => SettingType::CHECKBOX,
            'is_public' => false,
            'is_required' => false,
            'order' => 1,
        ]);

        Setting::create([
            'setting_group_id' => $securityGroup->id,
            'key' => 'session_timeout',
            'display_name' => __('Session Timeout'),
            'description' => __('How long before a session times out (in minutes)'),
            'value' => '30',
            'type' => SettingType::SELECT,
            'options' => [
                '15' => __('15 minutes'),
                '30' => __('30 minutes'),
                '60' => __('1 hour'),
                '120' => __('2 hours'),
            ],
            'is_public' => false,
            'is_required' => true,
            'order' => 2,
        ]);

        Setting::create([
            'setting_group_id' => $securityGroup->id,
            'key' => 'log_failed_login_attempts',
            'display_name' => __('Log Failed Login Attempts'),
            'description' => __('Whether to log failed login attempts'),
            'value' => '1',
            'type' => SettingType::CHECKBOX,
            'is_public' => false,
            'is_required' => false,
            'order' => 3,
        ]);

        Setting::create([
            'setting_group_id' => $emailGroup->id,
            'key' => 'mail_mailer',
            'display_name' => __('Mail Driver'),
            'description' => __('The mail driver to use for sending emails'),
            'value' => 'smtp',
            'type' => SettingType::SELECT,
            'options' => [
                'smtp' => __('SMTP'),
                'sendmail' => __('Sendmail'),
                'mailgun' => __('Mailgun'),
                'ses' => __('Amazon SES'),
                'postmark' => __('Postmark'),
                'log' => __('Log'),
                'array' => __('Array'),
            ],
            'is_public' => false,
            'is_required' => true,
            'order' => 1,
        ]);

        Setting::create([
            'setting_group_id' => $emailGroup->id,
            'key' => 'mail_host',
            'display_name' => __('SMTP Host'),
            'description' => __('The SMTP server host'),
            'value' => 'smtp.mailtrap.io',
            'type' => SettingType::TEXT,
            'is_public' => false,
            'is_required' => true,
            'order' => 2,
        ]);

        Setting::create([
            'setting_group_id' => $emailGroup->id,
            'key' => 'mail_port',
            'display_name' => __('SMTP Port'),
            'description' => __('The SMTP server port'),
            'value' => '2525',
            'type' => SettingType::NUMBER,
            'is_public' => false,
            'is_required' => true,
            'order' => 3,
        ]);

        Setting::create([
            'setting_group_id' => $emailGroup->id,
            'key' => 'mail_username',
            'display_name' => __('SMTP Username'),
            'description' => __('The SMTP server username'),
            'value' => '',
            'type' => SettingType::TEXT,
            'is_public' => false,
            'is_required' => false,
            'order' => 4,
        ]);

        Setting::create([
            'setting_group_id' => $emailGroup->id,
            'key' => 'mail_password',
            'display_name' => __('SMTP Password'),
            'description' => __('The SMTP server password'),
            'value' => '',
            'type' => SettingType::PASSWORD,
            'is_public' => false,
            'is_required' => false,
            'order' => 5,
        ]);

        Setting::create([
            'setting_group_id' => $emailGroup->id,
            'key' => 'mail_encryption',
            'display_name' => __('SMTP Encryption'),
            'description' => __('The encryption protocol to use for SMTP'),
            'value' => 'tls',
            'type' => SettingType::SELECT,
            'options' => [
                'tls' => __('TLS'),
                'ssl' => __('SSL'),
                '' => __('None'),
            ],
            'is_public' => false,
            'is_required' => false,
            'order' => 6,
        ]);

        Setting::create([
            'setting_group_id' => $emailGroup->id,
            'key' => 'mail_from_address',
            'display_name' => __('From Address'),
            'description' => __('The email address that will be used to send emails'),
            'value' => 'hello@example.com',
            'type' => SettingType::EMAIL,
            'is_public' => false,
            'is_required' => true,
            'order' => 7,
        ]);

        Setting::create([
            'setting_group_id' => $emailGroup->id,
            'key' => 'mail_from_name',
            'display_name' => __('From Name'),
            'description' => __('The name that will be used to send emails'),
            'value' => 'Example',
            'type' => SettingType::TEXT,
            'is_public' => false,
            'is_required' => true,
            'order' => 8,
        ]);

        Setting::create([
            'setting_group_id' => $attachmentGroup->id,
            'key' => 'attachments_max_upload_size_kb',
            'display_name' => __('Max Upload Size (KB)'),
            'description' => __('Maximum file size for uploads in kilobytes (e.g., 10240 for 10MB).'),
            'value' => '10240',
            'type' => SettingType::NUMBER,
            'is_public' => false,
            'is_required' => true,
            'order' => 1,
        ]);

        Setting::create([
            'setting_group_id' => $attachmentGroup->id,
            'key' => 'attachments_allowed_extensions',
            'display_name' => __('Allowed File Extensions'),
            'description' => __('Comma-separated list of allowed file extensions (e.g., jpg,jpeg,png,pdf,doc). Leave empty to allow all. Used for client-side validation.'),
            'value' => 'jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,txt',
            'type' => SettingType::TEXT,
            'is_public' => false,
            'is_required' => false,
            'order' => 2,
        ]);

        Setting::create([
            'setting_group_id' => $attachmentGroup->id,
            'key' => 'attachments_allowed_mime_types',
            'display_name' => __('Allowed MIME Types'),
            'description' => __('Comma-separated list of allowed MIME types (e.g., image/jpeg,application/pdf). Leave empty to allow all. Used for server-side validation.'),
            'value' => 'image/jpeg,image/png,image/gif,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,text/plain',
            'type' => SettingType::TEXTAREA,
            'is_public' => false,
            'is_required' => false,
            'order' => 3,
        ]);

        Setting::create([
            'setting_group_id' => $attachmentGroup->id,
            'key' => 'attachments_image_optimization_enabled',
            'display_name' => __('Enable Image Optimization'),
            'description' => __('Automatically optimize uploaded images (e.g., compression, resizing).'),
            'value' => '1',
            'type' => SettingType::CHECKBOX,
            'is_public' => false,
            'is_required' => false,
            'order' => 4,
        ]);

        Setting::create([
            'setting_group_id' => $attachmentGroup->id,
            'key' => 'attachments_image_quality',
            'display_name' => __('Image Optimization Quality'),
            'description' => __('Default quality for image optimization (1-100). Higher is better quality but larger file size.'),
            'value' => '80',
            'type' => SettingType::NUMBER,
            'is_public' => false,
            'is_required' => true,
            'order' => 5,
        ]);

        Setting::create([
            'setting_group_id' => $attachmentGroup->id,
            'key' => 'attachments_image_max_width',
            'display_name' => __('Max Image Width (px)'),
            'description' => __('Images wider than this will be resized down. Set to 0 or leave empty to disable max width constraint.'),
            'value' => '1920',
            'type' => SettingType::NUMBER,
            'is_public' => false,
            'is_required' => false,
            'order' => 6,
        ]);

        Setting::create([
            'setting_group_id' => $attachmentGroup->id,
            'key' => 'attachments_image_max_height',
            'display_name' => __('Max Image Height (px)'),
            'description' => __('Images taller than this will be resized down. Set to 0 or leave empty to disable max height constraint.'),
            'value' => '1080',
            'type' => SettingType::NUMBER,
            'is_public' => false,
            'is_required' => false,
            'order' => 7,
        ]);

        Setting::create([
            'setting_group_id' => $attachmentGroup->id,
            'key' => 'attachments_default_disk',
            'display_name' => __('Default Storage Disk'),
            'description' => __('The default filesystem disk for storing attachments (e.g., public, s3).'),
            'value' => 'public',
            'type' => SettingType::SELECT,
            'options' => [
                'public' => __('Public'),
                'local' => __('Local (Private)'),
                's3' => __('S3 (Amazon AWS)'),
            ],
            'is_public' => false,
            'is_required' => true,
            'order' => 8,
        ]);
    }
}
