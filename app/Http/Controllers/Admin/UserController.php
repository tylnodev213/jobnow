<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\UserRepository;
use App\Services\UserService;
use App\Validators\UserValidator;

class UserController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->setTitle(__('messages.page_title.admin.users'));
        $this->repository = app(UserRepository::class);
        $this->validator = app(UserValidator::class);
        $this->service = app(UserService::class);
        $this->csvFilename = getConfig('csv.users.filename');
        $this->csvHeaders = getConfig('csv.users.header');
    }

    protected function _prepareBeforeStore(&$data)
    {
        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }
    }

    protected function _prepareBeforeUpdate(&$data)
    {
        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }
    }
}
