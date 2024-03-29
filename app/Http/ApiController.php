<?php

namespace App\Http;

use App\Http\Controllers\Controller;
use App\Services\Api\ApiResponse;
use Illuminate\Validation\Validator;

class ApiController extends Controller
{
    public function __construct()
    {
        $this->guard = 'api';

        parent::__construct();
    }

    protected function validation_error_response(Validator $validator)
    {
        $_errors = $validator->errors()->messages();

        $first_error = array_values($_errors);

        if (!empty($first_error) && isset($first_error[0])) {
            if (isset($first_error[0][0])) {
                $first_error = $first_error[0][0];
            }
        }

        if (empty($first_error)) {
            $first_error = __('Invalid data');
        }

        return ApiResponse::validation(
            $first_error
        );
    }

    protected function formatValidationErrors(array $validationError): array
    {
        $formattedErrors = [];

        foreach ($validationError as $key => $value) {
            $formattedErrors[$key] = $value[0];
        }

        return $formattedErrors;
    }
}
