<?php

namespace Core\Validators\Concerns;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Unique;

trait BaseValidatesAttributes
{
    protected $_customMessages = [
        'date_format_multiple' => 'The :attribute field does not match the format datetime.',
    ];

    /**
     * validate date format multiple
     * ex: date_format_multiple:"Ymd","YmdHis","Y-m-d","Y-m-d H:i:s"
     *
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    public function validateDateFormatMultiple($attribute, $value, $parameters): bool
    {
        if (!$value) {
            return true;
        }

        foreach ($parameters as $parameter) {
            $parsed = date_parse_from_format($parameter, $value);
            if ($parsed['error_count'] === 0 && $parsed['warning_count'] === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return false|\Illuminate\Database\Query\Builder
     */
    public function validateCustomExists($attribute, $value, $parameters)
    {
        if (empty($parameters) || !is_array($parameters)) {
            return false;
        }

        $exists = DB::table($parameters[0])->where($parameters[1], '=', $value);
        $deletedFlag = getConfig('model_field.deleted.flag');
        if (!empty($deletedFlag)) {
            $exists->where($deletedFlag, '=', getConfig('deleted_flag.off'));
        }

        return $exists->first();
    }

    /**
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    public function validateCustomUnique($attribute, $value, $parameters)
    {
        if (empty($parameters) || !is_array($parameters)) {
            return false;
        }

        $table = data_get($parameters, 0, '');
        $field = data_get($parameters, 1, '');
        $ignore = data_get($parameters, 2, '');
        $deletedFlag = getConfig('model_field.deleted.flag');

        if (empty($field)) {
            $field = $attribute;
        }

        $unique = DB::table($table)->where($field, '=', $value);

        if (!empty($ignore)) {
            $unique->where('id', '<>', $ignore);
        }

        if (!empty($deletedFlag)) {
            $unique->where($deletedFlag, '=', getConfig('deleted_flag.off'));
        }

        $unique = $unique->first();
        return empty($unique);
    }
}
