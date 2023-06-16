<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HelperService
{
    public function validate(Request $request, $rules, $messages = [])
    {
        $validator = Validator::make($request->all(), $rules, $messages);

        $errors = [];
        if ($validator->fails()) {
            if (!empty($validator->errors()->getMessages())) {
                foreach ($validator->errors()->getMessages() as $key => $itemError) {
                    foreach ($itemError as $error) {
                        $errors[] = $error;
                    }
                }
            }
        }

        return $errors;
    }
}
