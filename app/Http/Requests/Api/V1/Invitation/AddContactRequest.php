<?php

namespace App\Http\Requests\Api\V1\Invitation;

use App\Services\RespondActive;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $contacts = $this->extractContacts();

        if (is_array($contacts)) {
            $this->merge(['contacts' => $contacts]);
        }
    }

    /**
     * @return array<int, array{name?: mixed, phone?: mixed}>|null
     */
    private function extractContacts(): ?array
    {
        $candidates = [
            $this->input('contacts'),
            $this->json('contacts'),
        ];

        foreach ($candidates as $candidate) {
            $normalized = $this->normalizeContactsValue($candidate);
            if ($normalized !== null) {
                return $normalized;
            }
        }

        $rawBody = trim($this->getContent());
        if ($rawBody === '') {
            return null;
        }

        $decodedBody = $this->decodeLooseJson($rawBody);
        if (is_array($decodedBody)) {
            if (isset($decodedBody['contacts'])) {
                return $this->normalizeContactsValue($decodedBody['contacts']);
            }

            if (array_is_list($decodedBody)) {
                return $decodedBody;
            }
        }

        parse_str($rawBody, $parsedBody);
        if (is_array($parsedBody) && isset($parsedBody['contacts'])) {
            return $this->normalizeContactsValue($parsedBody['contacts']);
        }

        return null;
    }

    /**
     * @param  mixed  $value
     * @return array<int, array{name?: mixed, phone?: mixed}>|null
     */
    private function normalizeContactsValue(mixed $value): ?array
    {
        if (is_array($value)) {
            if (isset($value['contacts'])) {
                return $this->normalizeContactsValue($value['contacts']);
            }

            return array_is_list($value) ? $value : null;
        }

        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        $decoded = $this->decodeLooseJson($value);
        if (! is_array($decoded)) {
            return null;
        }

        if (isset($decoded['contacts'])) {
            return $this->normalizeContactsValue($decoded['contacts']);
        }

        return array_is_list($decoded) ? $decoded : null;
    }

    /**
     * Accept valid JSON plus the app's loose object syntax like:
     * {contacts: [{name: Ahmed, phone: 010...}]}
     *
     * @return array<string, mixed>|array<int, mixed>|null
     */
    private function decodeLooseJson(string $value): array|null
    {
        $value = trim($value);

        if ($value === '') {
            return null;
        }

        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        $normalized = preg_replace("/'([^']*)'/u", '"$1"', $value) ?? $value;
        $normalized = preg_replace('/([{,]\s*)([A-Za-z_][A-Za-z0-9_]*)(\s*:)/u', '$1"$2"$3', $normalized) ?? $normalized;
        $normalized = preg_replace_callback(
            '/:\s*([^"\{\[\],][^,\}\]]*)(?=\s*[,}\]])/u',
            static function (array $matches): string {
                $raw = trim($matches[1]);

                if ($raw === '' || is_numeric($raw) || in_array(strtolower($raw), ['true', 'false', 'null'], true)) {
                    return ': '.$raw;
                }

                $escaped = addcslashes($raw, "\\\"");

                return ': "'.$escaped.'"';
            },
            $normalized
        ) ?? $normalized;

        $decoded = json_decode($normalized, true);

        return json_last_error() === JSON_ERROR_NONE && is_array($decoded) ? $decoded : null;
    }

    public function rules(): array
    {
        return [
            'contacts' => ['required', 'array', 'min:1'],
            'contacts.*.name' => ['required', 'string', 'max:255'],
            'contacts.*.phone' => ['required', 'string', 'max:32'],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(RespondActive::clientError(
            RespondActive::stringifyErrors($validator->errors())
        ));
    }
}
