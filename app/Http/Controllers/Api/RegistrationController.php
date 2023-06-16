<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\HelperService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RegistrationController extends Controller
{
    public function register(Request $request)
    {
        $errors = (new HelperService())->validate($request, [
            "full_name" => ["required", "string", "max:255"],
            "email" => ["required", "string", "email", "max:255", "unique:users"],
            "password" => [
                "required",
                "string",
                "min:8",
                "regex:/[a-z]/",
                "regex:/[A-Z]/",
                "regex:/[0-9]/",
                "confirmed",
            ],
            "terms_and_conditions" => "required"
        ], [
            "supplier_name.required" => "The business name field is required.",
            "supplier_name.unique" => "This business name is already in use.",
            "full_name.required" => "Please enter your full name.",
            "email.unique" => "This email is already in use.",
            "password.required" => "Please enter your password.",
            "password.min" => "Password must be at least 8 characters.",
            "password.regex" => "Password must contain an upper case letter, a lower case letter and a number.",
            "password.confirmed" => "Passwords do not match.",
            "terms_and_conditions.required" => "Please read and accept the terms and conditions."
        ]);

        if (count($errors)) {
            return response([
                "message" => $errors
            ], 422);
        }

        $user = new User();
        $user->name = $request->full_name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        return response()->json([
            "data" => $user
        ]);
    }
}
