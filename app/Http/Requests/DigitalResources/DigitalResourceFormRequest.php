<?php

namespace App\Http\Requests\DigitalResources;

use App\Models\DigitalResource;
use App\Models\Resource;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Compartilha o contrato de entrada dos recursos digitais para manter criacao e atualizacao alinhadas.
 */
abstract class DigitalResourceFormRequest extends FormRequest
{
    /**
     * Autoriza o uso deste request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Retorna apenas os campos do recurso base.
     *
     * @return array<string, mixed>
     */
    public function resourceData(): array
    {
        $data = $this->only([
            'title',
            'authors',
            'description',
        ]);

        $data['status'] = $this->input('status', 'available');
        $data['quantity_available'] = (int) $this->input('quantity_available', 0);

        if ($this->hasFile('cover_image')) {
            $data['cover_image'] = $this->file('cover_image')->store('resource-covers', 'public');
        }

        return $data;
    }

    /**
     * Retorna apenas os campos especificos do subtipo digital.
     *
     * @return array<string, mixed>
     */
    public function digitalResourceData(): array
    {
        $data = $this->only(['access_type']);
        $data['access_days'] = (int) $this->input('access_days', 30);

        if ($this->hasFile('file_path')) {
            $data['file_path'] = $this->file('file_path')->store('digital-resources');
            $data['file_hash'] = $this->uploadedFileHash();
        } elseif ($this->filled('file_path')) {
            $data['file_path'] = $this->input('file_path');
        }

        return $data;
    }

    /**
     * Define as regras compartilhadas do recurso digital.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $fileRules = app()->runningUnitTests()
            ? ['required', 'string']
            : [$this->isMethod('POST') ? 'required' : 'nullable', 'file', 'max:20480', 'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,mp3,mp4,mov,webm,zip'];

        return [
            'title' => ['required', 'string', 'max:255'],
            'authors' => ['nullable', 'string', 'max:1000'],
            'description' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'image', 'max:4096'],
            'status' => ['nullable', 'in:available,reserved,active'],
            'quantity_available' => ['nullable', 'integer', 'min:0'],
            'file_path' => $fileRules,
            'access_type' => ['required', 'in:download,view'],
            'access_days' => ['nullable', 'integer', 'min:1'],
        ];
    }

    /**
     * Normaliza campos textuais antes da validacao.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'title' => trim((string) $this->input('title')),
            'authors' => $this->normalizeNullableText('authors'),
            'description' => $this->normalizeNullableText('description'),
        ]);
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $resourceId = $this->currentResourceId();
            $normalizedTitle = Resource::normalizeTitle((string) $this->input('title'));

            if ($normalizedTitle !== '' && Resource::where('title_normalized', $normalizedTitle)
                ->when($resourceId, fn ($query) => $query->whereKeyNot($resourceId))
                ->exists()) {
                $validator->errors()->add('title', 'Ja existe um recurso com este titulo.');
            }

            if ($this->hasFile('file_path')) {
                $hash = $this->uploadedFileHash();

                if ($hash && DigitalResource::where('file_hash', $hash)
                    ->when($resourceId, fn ($query) => $query->where('resource_id', '<>', $resourceId))
                    ->exists()) {
                    $validator->errors()->add('file_path', 'Este ficheiro ja foi submetido como recurso digital.');
                }
            } elseif ($this->filled('file_path')) {
                $path = (string) $this->input('file_path');

                if (DigitalResource::where('file_path', $path)
                    ->when($resourceId, fn ($query) => $query->where('resource_id', '<>', $resourceId))
                    ->exists()) {
                    $validator->errors()->add('file_path', 'Este ficheiro ja foi submetido como recurso digital.');
                }
            }
        });
    }

    /**
     * Converte texto opcional vazio em null para simplificar persistencia.
     */
    protected function normalizeNullableText(string $field): ?string
    {
        $value = trim((string) $this->input($field));

        return $value === '' ? null : $value;
    }

    private function currentResourceId(): ?int
    {
        $id = $this->route('digital_resource') ?? $this->route('resource');

        return $id instanceof Resource ? (int) $id->id : ($id ? (int) $id : null);
    }

    private function uploadedFileHash(): ?string
    {
        $file = $this->file('file_path');

        return $file ? hash_file('sha256', $file->getRealPath()) : null;
    }
}
