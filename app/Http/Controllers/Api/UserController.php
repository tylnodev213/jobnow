<?php

namespace App\Http\Controllers\Api;

use App\Repositories\UserRepository;
use App\Validators\UserValidator;

class UserController extends ApiController
{
    /** @var UserRepository $userRepository */
    protected $userRepository;
    /** @var UserValidator $userValidator */
    protected $userValidator;

    public function __construct()
    {
        parent::__construct();
        $this->userRepository = app(UserRepository::class);
        $this->userValidator = app(UserValidator::class);
    }

    public function index()
    {
        $users = $this->userRepository->paginate(getConfig('page_number'))->toArray();
        $this->setData(['users' => data_get($users, 'data', [])]);
        $this->appendResponse(['meta' => ['current_page' => data_get($users, 'current_page', 1), 'total_page' => data_get($users, 'to', 1)]]);
        return $this->response();
    }

    public function create()
    {
        try {
            $validator = $this->userValidator->validateCreate($this->getParams());
            if (!$validator) {
                $this->setStatus(false);
                $this->setMessage($this->userValidator->customErrorsBag());
                return $this->response();
            }

            $password = $this->getParams('password');
            $password = !empty($password) ? bcrypt($password) : null;
            $users = [
                'email' => $this->getParams('email'),
                'password' => $password,
                'last_name' => $this->getParams('last_name'),
                'first_name' => $this->getParams('first_name'),
            ];
            $user = $this->userRepository->create($users);

            $this->setMessage(__('messages.create_success'));
            $this->setData($user->toArray());
        } catch (\Exception $exception) {
            logInfo('Params: ' . $this->jsonEncode($this->getParams()));
            logError($exception->getMessage() . PHP_EOL . $exception->getTraceAsString());
            $this->setStatus(false);
            $this->setCode(getConstant('HTTP_CODE.SERVER_ERROR'));
            return $this->response();
        }

        return $this->response();
    }

    public function detail($id)
    {
        $validate = $this->userValidator->validateShow($id);
        if (!$validate) {
            $this->setStatus(false);
            $this->setMessage(__('messages.no_result_found'));
            return $this->response();
        }

        $user = $this->userRepository->where('id', $id)->first();
        $user = !empty($user) ? $user->toArray() : [];
        $this->setData($user);
        return $this->response();
    }

    public function edit($id)
    {
        try {
            $validate = $this->userValidator->validateShow($id);
            if (!$validate) {
                $this->setStatus(false);
                $this->setMessage(__('messages.no_result_found'));
                return $this->response();
            }

            $this->appendParams(['id' => $id]);
            $validateEdit = $this->userValidator->validateUpdate($this->getParams());
            if (!$validateEdit) {
                $this->setStatus(false);
                $this->setMessage($this->userValidator->customErrorsBag());
                return $this->response();
            }

            $dataUpdate = [];
            if (!is_null($this->getParams('email'))) {
                $dataUpdate['email'] = $this->getParams('email');
            }
            if (!is_null($this->getParams('password'))) {
                $dataUpdate['password'] = bcrypt($this->getParams('password'));
            }
            if (!is_null($this->getParams('last_name'))) {
                $dataUpdate['last_name'] = $this->getParams('last_name');
            }
            if (!is_null($this->getParams('first_name'))) {
                $dataUpdate['first_name'] = $this->getParams('first_name');
            }
            if (!empty($dataUpdate)) {
                $this->userRepository->update($id, $dataUpdate);
            }
        } catch (\Exception $exception) {
            logInfo('Params: ' . $this->jsonEncode($this->getParams()));
            logError($exception->getMessage() . PHP_EOL . $exception->getTraceAsString());
            $this->setStatus(false);
            $this->setCode(getConstant('HTTP_CODE.SERVER_ERROR'));
            return $this->response();
        }

        return $this->response();
    }

    public function delete($id)
    {
        try {
            $validate = $this->userValidator->validateShow($id);
            if (!$validate) {
                $this->setStatus(false);
                $this->setMessage(__('messages.no_result_found'));
                return $this->response();
            }

            $this->userRepository->delete($id);
        } catch (\Exception $exception) {
            logInfo('Params: ' . $this->jsonEncode($this->getParams()));
            logError($exception->getMessage() . PHP_EOL . $exception->getTraceAsString());
            $this->setStatus(false);
            $this->setCode(getConstant('HTTP_CODE.SERVER_ERROR'));
            return $this->response();
        }

        return $this->response();
    }
}
