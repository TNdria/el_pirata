<?php

namespace Database\Seeders;

use App\Models\legal_document;
use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LegalDocumentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        $documents = [
            [
                'type' => 'CGU',
                'title' => 'Conditions Générales d’Utilisation',
                'slug' => 'cgu',
                'content' => null,
                'is_active' => true,
            ],
            [
                'type' => 'CGV',
                'title' => 'Conditions Générales de Vente',
                'slug' => 'cgv',
                'content' => null,
                'is_active' => true,
            ],
            [
                'type' => 'MENTIONS',
                'title' => 'Mentions Légales',
                'slug' => 'mentions-legales',
                'content' => null,
                'is_active' => true,
            ],
            [
                'type' => 'CONFIDENTIALITE',
                'title' => 'Politique de Confidentialité',
                'slug' => 'politique-confidentialite',
                'content' => null,
                'is_active' => true,
            ],
            [
                'type' => 'FAQ',
                'title' => 'Foire Aux Questions',
                'slug' => 'faq',
                'content' => null,
                'is_active' => true,
            ],
            [
                'type' => 'REMBOURSEMENT',
                'title' => 'Politique de Remboursement',
                'slug' => 'politique-remboursement',
                'content' => null,
                'is_active' => true,
            ],
            [
                'type' => 'COOKIES',
                'title' => 'Politique de Cookies',
                'slug' => 'politique-cookies',
                'content' => null,
                'is_active' => true,
            ],
            [
                'type' => 'ABOUT',
                'title' => 'Qui sommes-nous ?',
                'slug' => 'qui-sommes-nous',
                'content' => null,
                'is_active' => true,
            ],
            [
                'type' => 'REGLES',
                'title' => 'Règles du jeu',
                'slug' => 'regles-du-jeu',
                'content' => null,
                'is_active' => true,
            ],
            [
                'type' => 'CHARTE',
                'title' => 'Charte Éthique et Engagements',
                'slug' => 'charte-ethique-engagements',
                'content' => null,
                'is_active' => true,
            ],
            [
                'type' => 'CHARTE',
                'title' => 'Support et Assistance',
                'slug' => 'support-assistance',
                'content' => null,
                'is_active' => true,
            ],
        ];


        foreach ($documents as $key => $value) {
            # code...
            legal_document::create($value);
        }
    }
}
