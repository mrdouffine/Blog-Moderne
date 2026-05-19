<?php

declare(strict_types=1);

namespace App\Http\Requests\Article;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class StoreArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // L'authentification est gérée par le middleware auth:sanctum
    }

    /**
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:255'],
            'content'     => ['required', 'string'],
            'excerpt'     => ['nullable', 'string', 'max:500'],
            'category'    => ['nullable', 'string', 'max:100'],
            'status'      => ['nullable', Rule::in(['draft', 'published', 'archived'])],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'], // 5 Mo max
            'tags'        => ['nullable', 'array'],
            'tags.*'      => ['string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'         => 'Le titre est obligatoire.',
            'title.max'              => 'Le titre ne peut pas dépasser 255 caractères.',
            'content.required'       => 'Le contenu est obligatoire.',
            'status.in'              => 'Le statut doit être : draft, published ou archived.',
            'cover_image.image'      => 'Le fichier doit être une image.',
            'cover_image.mimes'      => 'L\'image doit être au format jpeg, jpg, png ou webp.',
            'cover_image.max'        => 'L\'image ne doit pas dépasser 5 Mo.',
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
