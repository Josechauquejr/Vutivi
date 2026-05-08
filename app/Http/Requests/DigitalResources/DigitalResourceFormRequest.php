<?php

namespace App\Http\Requests\DigitalResources;

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
        return $this->only([
            'title',
            'description',
            'status',
            'quantity_available',
        ]);
    }

    /**
     * Retorna apenas os campos especificos do subtipo digital.
     *
     * @return array<string, mixed>
     */
    public function digitalResourceData(): array
    {
        return $this->only([
            'file_path',
            'access_type',
            'access_days',
        ]);
    }

    /**
     * Define as regras compartilhadas do recurso digital.
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
            'file_path' => ['required', 'string'],
            'access_type' => ['required', 'in:download,view'],
            'access_days' => ['required', 'integer', 'min:1'],
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
            'file_path' => trim((string) $this->input('file_path')),
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
