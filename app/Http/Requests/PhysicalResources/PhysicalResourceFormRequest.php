<?php

namespace App\Http\Requests\PhysicalResources;

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
        return $this->only([
            'title',
            'description',
            'status',
            'quantity_available',
        ]);
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
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:available,reserved,active'],
            'quantity_available' => ['required', 'integer', 'min:1'],
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
            'description' => $this->normalizeNullableText('description'),
            'location' => trim((string) $this->input('location')),
            'condition' => trim((string) $this->input('condition')),
        ]);
    }

    /**
     * Converte texto opcional vazio em null para simplificar persistencia.
     */
    protected function normalizeNullableText(string $field): ?string
    {
        $value = trim((string) $this->input($field));

        return $value === '' ? null : $value;
    }
}
