<?php

namespace App\Providers;

use App\Validators\Contracts\CustomValidatorContract;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class ValidationServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Validator::resolver(function ($translator, $data, $rules, $messages = [], $customAttributes = []) {
            return new CustomValidatorContract($translator, $data, $rules, $messages, $customAttributes);
        });
    }

    public function register()
    {
        //
    }
}
