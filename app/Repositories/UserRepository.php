<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserRepository extends CustomRepository
{
    protected $model = User::class;

    public function __construct()
    {
        parent::__construct();
    }

    protected function _buildSelectForIndex($fields)
    {
        $result = [
            'id',
            DB::raw("CONCAT(last_name, ' ', first_name) as name"),
            'email',
            'avatar',
            'created_at',
            'updated_at',
        ];
        return $this->selectRaw(implode(',', $result));
    }

    protected function _buildConditionForIndex(&$builder, $params = [])
    {
        $email = data_get($params, 'email');
        $name = data_get($params, 'name');

        if (!empty($email)) {
            $builder->where('email', 'LIKE', '%' . escape_like($email) . '%');
        }
        if (!empty($name)) {
            $builder->whereRaw("CONCAT(last_name, ' ', first_name) LIKE '%" . escape_like($name) . "%'");
        }
    }

    protected function _buildSelectForExport($fields)
    {
        return $this->_buildSelectForIndex($fields);
    }

    protected function _buildConditionForExport(&$builder, $params = [])
    {
        $this->_buildConditionForIndex($builder, $params);
    }
}
