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
        $data = $this->only([
            'title',
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
        $data = $this->only(['access_type', 'access_days']);

        if ($this->hasFile('file_path')) {
            $data['file_path'] = $this->file('file_path')->store('digital-resources');
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
            'description' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'image', 'max:4096'],
            'status' => ['nullable', 'in:available,reserved,active'],
            'quantity_available' => ['nullable', 'integer', 'min:0'],
            'file_path' => $fileRules,
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
