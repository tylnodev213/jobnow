<?php

namespace App\Validators;

use App\Models\AdminUser;

class AdminUserValidator extends CustomValidator
{
    protected $_model = AdminUser::class;

    /**
     * @param $params
     * @return bool
     */
    public function validateLogin($params)
    {
        $rules = [
            'email' => 'required|email',
            'password' => 'required'
        ];

        return $this->_addRulesMessages($rules, [], false)->with($params)->passes();
    }
}
