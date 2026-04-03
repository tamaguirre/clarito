<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ResumeStoreRequest;
use App\Http\Requests\Api\ResumeSummaryRequest;
use App\Http\Resources\ResumeResource;
use App\Models\Resume;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;

class ResumeController extends Controller
{
    private const DEFAULT_TAGS = [
        'Arriendo',
        'Legal',
        'Chile',
        'IPC',
        'Garantía',
        'Vivienda',
    ];

    private const DEFAULT_SUMMARY = [
        'resume' => [
            [
                'id' => 1,
                'section' => 'Identificación de las Partes',
                'resume' => 'Individualización legal del arrendador y arrendatario, incluyendo RUT y domicilios para notificaciones.',
                'faq' => [
                    [
                        'question' => '¿Es necesario que ambos firmen ante notario?',
                        'answer' => 'Es lo ideal para otorgar fecha cierta al documento y facilitar procesos de cobranza o desalojo futuros.',
                    ],
                ],
            ],
            [
                'id' => 2,
                'section' => 'Renta y Reajustabilidad',
                'resume' => 'Establece el monto mensual del arriendo y el mecanismo de actualización (IPC) cada 6 meses.',
                'faq' => [
                    [
                        'question' => '¿Cuándo se aplica el primer reajuste?',
                        'answer' => 'Generalmente al cumplirse el sexto mes de vigencia del contrato, según la variación acumulada del IPC.',
                    ],
                ],
            ],
            [
                'id' => 3,
                'section' => 'Vigencia y Plazos',
                'resume' => 'Define la duración del contrato (habitualmente un año) y las condiciones de renovación automática.',
                'faq' => [
                    [
                        'question' => '¿Con cuánto tiempo debo avisar si no quiero renovar?',
                        'answer' => 'El plazo estándar es de 30 a 60 días antes del vencimiento del periodo actual.',
                    ],
                ],
            ],
            [
                'id' => 4,
                'section' => 'Mes de Garantía',
                'resume' => 'Monto destinado a cubrir daños en la propiedad o cuentas de servicios pendientes al finalizar el arriendo.',
                'faq' => [
                    [
                        'question' => '¿Me pueden devolver la garantía el mismo día que entrego la llave?',
                        'answer' => 'Normalmente el arrendador tiene hasta 30 días para devolverla, previa revisión de las cuentas de luz, agua y gas.',
                    ],
                ],
            ],
            [
                'id' => 5,
                'section' => 'Uso y Restricciones',
                'resume' => 'Prohibiciones sobre el uso comercial del inmueble, subarrendamiento y alteraciones estructurales sin permiso.',
                'faq' => [
                    [
                        'question' => '¿Puedo subarrendar una habitación?',
                        'answer' => 'No, a menos que el contrato lo autorice expresamente por escrito.',
                    ],
                ],
            ],
        ],
    ];

    public function store(ResumeStoreRequest $request): JsonResponse
    {
        $file = $request->file('file');
        $path = $file->store('uploads', 'public');

        $resume = Resume::create([
            'token' => $this->generateUniqueToken(),
            'user_id' => $request->user()->id,
            'original_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'summary_text' => self::DEFAULT_SUMMARY,
        ]);

        $this->syncDefaultTags($resume);

        return (new ResumeResource($resume->load('tags')))
            ->response()
            ->setStatusCode(201);
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $resumes = Resume::query()
            ->where('user_id', $request->user()->id)
            ->with('tags')
            ->latest('id')
            ->get();

        return ResumeResource::collection($resumes);
    }

    public function show(Request $request, string $token): ResumeResource
    {
        $resume = Resume::query()
            ->where('token', $token)
            ->where('user_id', $request->user()->id)
            ->with('tags')
            ->firstOrFail();

        return new ResumeResource($resume);
    }

    public function update(ResumeSummaryRequest $request, string $token): ResumeResource
    {
        $resume = Resume::query()
            ->where('token', $token)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $resume->update([
            'summary_text' => self::DEFAULT_SUMMARY,
        ]);

        $this->syncDefaultTags($resume);

        return new ResumeResource($resume->load('tags'));
    }

    private function syncDefaultTags(Resume $resume): void
    {
        $tagIds = collect(self::DEFAULT_TAGS)
            ->map(function (string $name): int {
                return Tag::query()->firstOrCreate(['name' => $name])->id;
            })
            ->all();

        $resume->tags()->sync($tagIds);
    }

    private function generateUniqueToken(): string
    {
        do {
            $token = Str::random(64);
        } while (Resume::query()->where('token', $token)->exists());

        return $token;
    }
}
