<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Traits\Response;

class BaseFormRequest extends FormRequest
{
    use Response;
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->sendRes(false, 'Validation Error', null,$validator->errors(), 422));
    }
}
