<?php

declare(strict_types=1);

namespace App\Http\Requests\Article;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // L'ownership est vérifié dans le controller
    }

    /**
     * Règles de validation pour la mise à jour (tous les champs sont optionnels).
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title'       => ['sometimes', 'string', 'max:255'],
            'content'     => ['sometimes', 'string'],
            'excerpt'     => ['nullable', 'string', 'max:500'],
            'status'      => ['nullable', Rule::in(['draft', 'published', 'archived'])],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            'tags'        => ['nullable', 'array'],
            'tags.*'      => ['string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.max'         => 'Le titre ne peut pas dépasser 255 caractères.',
            'status.in'         => 'Le statut doit être : draft, published ou archived.',
            'cover_image.image' => 'Le fichier doit être une image.',
            'cover_image.mimes' => 'L\'image doit être au format jpeg, jpg, png ou webp.',
            'cover_image.max'   => 'L\'image ne doit pas dépasser 5 Mo.',
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
