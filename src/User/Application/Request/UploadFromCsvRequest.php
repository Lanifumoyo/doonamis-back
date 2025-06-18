<?php

namespace Doonamis\User\Application\Request;

use Illuminate\Foundation\Http\FormRequest;

class UploadFromCsvRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => 'required|file|mimes:csv,txt',
        ];
    }
}