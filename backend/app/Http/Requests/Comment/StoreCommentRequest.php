<?php

declare(strict_types=1);

namespace App\Http\Requests\Comment;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreCommentRequest extends FormRequest
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
            'content' => ['required_without:body', 'nullable', 'string', 'min:2', 'max:2000'],
            'body'    => ['required_without:content', 'nullable', 'string', 'min:2', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'content.required_without' => 'Le contenu du commentaire est obligatoire.',
            'content.min'              => 'Le commentaire doit contenir au moins 2 caractères.',
            'content.max'              => 'Le commentaire ne peut pas dépasser 2000 caractères.',
            'body.required_without'    => 'Le contenu du commentaire est obligatoire.',
            'body.min'                 => 'Le commentaire doit contenir au moins 2 caractères.',
            'body.max'                 => 'Le commentaire ne peut pas dépasser 2000 caractères.',
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
