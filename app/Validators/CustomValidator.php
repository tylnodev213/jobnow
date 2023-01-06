<?php

namespace App\Validators;

use Core\Validators\BaseValidator;

class CustomValidator extends BaseValidator
{
    /**
     * Validate data for action store
     *
     * @param $data
     * @return bool
     */
    public function validateCreate($data)
    {
        return $this->_addRulesMessages()->with($data)->passes();
    }

    /**
     * Validate data for action update
     *
     * @param $data
     * @return bool
     */
    public function validateUpdate($data)
    {
        return $this->_addRulesMessages()->with($data)->passes();
    }

    /**
     * Validate detail
     *
     * @param $id
     * @return bool
     */
    public function validateShow($id)
    {
        $modelName = app($this->_model)->getModel()->getTable();
        $data = ['id' => $id];
        $rules = ['id' => 'required|integer|custom_exists:' . $modelName . ',id'];

        return $this->_addRulesMessages($rules, [], false)->with($data)->passes();
    }
}
