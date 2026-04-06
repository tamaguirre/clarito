<?php

namespace Database\Seeders;

use App\Models\CompanyConfigType;
use Illuminate\Database\Seeder;

class CompanyConfigTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            ['key' => 'action_type_id', 'label' => 'Tipo de accion', 'data_type' => 'catalog_id'],
            ['key' => 'allow_multiple_confirmation', 'label' => 'Permite multiple', 'data_type' => 'boolean'],
            ['key' => 'link_expiration_hours', 'label' => 'Vigencia del link en horas', 'data_type' => 'integer'],
            ['key' => 'access_method_id', 'label' => 'Metodo de acceso', 'data_type' => 'catalog_id'],
            ['key' => 'ai_tone_id', 'label' => 'Tono de la IA', 'data_type' => 'catalog_id'],
            ['key' => 'return_button', 'label' => 'Boton de retorno', 'data_type' => 'object'],
            ['key' => 'allow_calendar_dates', 'label' => 'Permitir calendario de fechas', 'data_type' => 'boolean'],
            ['key' => 'send_summary_pdf_by_email', 'label' => 'Enviar resumen en PDF por mail', 'data_type' => 'boolean'],
        ];

        collect($items)->each(function (array $item): void {
            CompanyConfigType::query()->updateOrCreate(
                ['key' => $item['key']],
                [
                    'label' => $item['label'],
                    'data_type' => $item['data_type'],
                ]
            );
        });
    }
}
