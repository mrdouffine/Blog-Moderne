<?php

declare(strict_types=1);

namespace App\Http\Requests\Media;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UploadMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'file'       => [
                'required',
                'file',
                'mimes:jpeg,jpg,png,webp,gif,mp4,pdf,doc,docx,zip',
                'max:20480', // 20 Mo max
            ],
            'article_id' => ['nullable', 'integer', 'exists:articles,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required'    => 'Le fichier est obligatoire.',
            'file.file'        => 'Le champ doit être un fichier valide.',
            'file.mimes'       => 'Le format du fichier n\'est pas supporté.',
            'file.max'         => 'Le fichier ne peut pas dépasser 20 Mo.',
            'article_id.exists' => 'L\'article spécifié n\'existe pas.',
        ];
    }

    protected function failedValidation(Validator $validator): never
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'data'    => $validator->errors(),
                'message' => 'Erreur de validation.',
            ], 422)
        );
    }
}
