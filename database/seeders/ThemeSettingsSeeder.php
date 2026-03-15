<?php

namespace Matteomascellani\FilamentSettings\Database\Seeders;

use Illuminate\Database\Seeder;
use Matteomascellani\FilamentSettings\Models\Setting;

class ThemeSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            [
                'group' => 'theme',
                'key' => 'theme.primary',
                'label' => 'Colore primario',
                'description' => 'Colore principale dei bottoni/action.',
                'value_type' => 'color',
                'sort' => 10,
                'value' => '#1f3f66',
            ],
            [
                'group' => 'theme',
                'key' => 'theme.primary_hover',
                'label' => 'Colore primario hover',
                'description' => 'Colore hover per bottoni/action principali.',
                'value_type' => 'color',
                'sort' => 20,
                'value' => '#193452',
            ],
            [
                'group' => 'theme',
                'key' => 'theme.topbar_link',
                'label' => 'Colore link top menu',
                'description' => 'Colore testo/icona dei link in topbar.',
                'value_type' => 'color',
                'sort' => 30,
                'value' => '#1f3f66',
            ],
            [
                'group' => 'theme',
                'key' => 'theme.topbar_link_hover_bg',
                'label' => 'Sfondo hover link top menu',
                'description' => 'Sfondo hover/focus/active dei link topbar.',
                'value_type' => 'string',
                'sort' => 40,
                'value' => 'rgba(31, 63, 102, 0.08)',
            ],
        ];

        foreach ($rows as $row) {
            $setting = Setting::query()->updateOrCreate(
                ['key' => $row['key']],
                [
                    'group' => $row['group'],
                    'label' => $row['label'],
                    'description' => $row['description'],
                    'value_type' => $row['value_type'],
                    'is_active' => true,
                    'sort' => $row['sort'],
                ]
            );

            $setting->values()->updateOrCreate(
                [
                    'name' => 'default',
                    'settable_type' => null,
                    'settable_id' => null,
                ],
                [
                    'value' => $row['value'],
                    'sort' => 10,
                ]
            );
        }
    }
}
