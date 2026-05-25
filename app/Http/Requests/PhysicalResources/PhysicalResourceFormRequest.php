<?php

namespace App\Http\Requests\PhysicalResources;

use App\Models\Resource;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Compartilha o contrato de entrada dos recursos fisicos para manter criacao e atualizacao consistentes.
 */
abstract class PhysicalResourceFormRequest extends FormRequest
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
            'status',
            'quantity_available',
        ]);

        if ($this->hasFile('cover_image')) {
            $data['cover_image'] = $this->file('cover_image')->store('resource-covers', 'public');
        }

        return $data;
    }

    /**
     * Retorna apenas os campos especificos do subtipo fisico.
     *
     * @return array<string, mixed>
     */
    public function physicalResourceData(): array
    {
        return $this->only([
            'location',
            'max_loan_days',
            'condition',
        ]);
    }

    /**
     * Define as regras compartilhadas do recurso fisico.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'authors' => ['nullable', 'string', 'max:1000'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:available,reserved,active'],
            'quantity_available' => ['required', 'integer', 'min:1'],
            'cover_image' => ['nullable', 'image', 'max:4096'],
            'location' => ['required', 'string', 'max:255'],
            'max_loan_days' => ['required', 'integer', 'min:1'],
            'condition' => ['required', 'string', 'max:100'],
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
            'location' => trim((string) $this->input('location')),
            'condition' => trim((string) $this->input('condition')),
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
        $id = $this->route('physical_resource') ?? $this->route('resource');

        return $id instanceof Resource ? (int) $id->id : ($id ? (int) $id : null);
    }
}
