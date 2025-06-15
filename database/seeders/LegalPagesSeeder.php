<?php

namespace Database\Seeders;

use App\Models\LegalPage;
use Illuminate\Database\Seeder;

class LegalPagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Privacy Policy
        LegalPage::create([
            'title' => [
                'en' => 'Privacy Policy',
                'fr' => 'Politique de Confidentialité',
                'es' => 'Política de Privacidad',
            ],
            'slug' => [
                'en' => 'privacy-policy',
                'fr' => 'politique-de-confidentialite',
                'es' => 'politica-de-privacidad',
            ],
            'content' => [
                'en' => '<h1>Privacy Policy</h1><p>This privacy policy explains how we collect, use, and protect your personal information.</p>',
                'fr' => '<h1>Politique de Confidentialité</h1><p>Cette politique de confidentialité explique comment nous collectons, utilisons et protégeons vos informations personnelles.</p>',
                'es' => '<h1>Política de Privacidad</h1><p>Esta política de privacidad explica cómo recopilamos, usamos y protegemos su información personal.</p>',
            ],
            'is_published' => true,
            'meta_title' => [
                'en' => 'Privacy Policy - Your Rights and Our Responsibilities',
                'fr' => 'Politique de Confidentialité - Vos Droits et Nos Responsabilités',
                'es' => 'Política de Privacidad - Sus Derechos y Nuestras Responsabilidades',
            ],
            'meta_description' => [
                'en' => 'Learn about how we handle your personal information and your privacy rights.',
                'fr' => 'Découvrez comment nous gérons vos informations personnelles et vos droits à la vie privée.',
                'es' => 'Aprenda cómo manejamos su información personal y sus derechos de privacidad.',
            ],
        ]);

        // Terms of Service
        LegalPage::create([
            'title' => [
                'en' => 'Terms of Service',
                'fr' => 'Conditions d\'Utilisation',
                'es' => 'Términos de Servicio',
            ],
            'slug' => [
                'en' => 'terms-of-service',
                'fr' => 'conditions-d-utilisation',
                'es' => 'terminos-de-servicio',
            ],
            'content' => [
                'en' => '<h1>Terms of Service</h1><p>By using our service, you agree to these terms and conditions.</p>',
                'fr' => '<h1>Conditions d\'Utilisation</h1><p>En utilisant notre service, vous acceptez ces conditions générales.</p>',
                'es' => '<h1>Términos de Servicio</h1><p>Al usar nuestro servicio, acepta estos términos y condiciones.</p>',
            ],
            'is_published' => true,
            'meta_title' => [
                'en' => 'Terms of Service - Rules and Guidelines',
                'fr' => 'Conditions d\'Utilisation - Règles et Directives',
                'es' => 'Términos de Servicio - Reglas y Directrices',
            ],
            'meta_description' => [
                'en' => 'Read our terms of service to understand the rules and guidelines for using our platform.',
                'fr' => 'Lisez nos conditions d\'utilisation pour comprendre les règles et directives d\'utilisation de notre plateforme.',
                'es' => 'Lea nuestros términos de servicio para comprender las reglas y directrices para usar nuestra plataforma.',
            ],
        ]);

        // Cookie Policy
        LegalPage::create([
            'title' => [
                'en' => 'Cookie Policy',
                'fr' => 'Politique des Cookies',
                'es' => 'Política de Cookies',
            ],
            'slug' => [
                'en' => 'cookie-policy',
                'fr' => 'politique-des-cookies',
                'es' => 'politica-de-cookies',
            ],
            'content' => [
                'en' => '<h1>Cookie Policy</h1><p>Learn about how we use cookies and similar technologies on our website.</p>',
                'fr' => '<h1>Politique des Cookies</h1><p>Découvrez comment nous utilisons les cookies et technologies similaires sur notre site web.</p>',
                'es' => '<h1>Política de Cookies</h1><p>Aprenda cómo usamos cookies y tecnologías similares en nuestro sitio web.</p>',
            ],
            'is_published' => true,
            'meta_title' => [
                'en' => 'Cookie Policy - How We Use Cookies',
                'fr' => 'Politique des Cookies - Comment Nous Utilisons les Cookies',
                'es' => 'Política de Cookies - Cómo Usamos las Cookies',
            ],
            'meta_description' => [
                'en' => 'Information about the cookies we use and how to control them.',
                'fr' => 'Informations sur les cookies que nous utilisons et comment les contrôler.',
                'es' => 'Información sobre las cookies que usamos y cómo controlarlas.',
            ],
        ]);
    }
} 