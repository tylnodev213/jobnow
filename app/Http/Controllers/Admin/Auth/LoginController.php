<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Admin\AdminController;
use App\Validators\AdminUserValidator;
use Illuminate\Support\MessageBag;

class LoginController extends AdminController
{
    /** @var AdminUserValidator $adminUserValidator */
    protected $adminUserValidator;

    public function __construct()
    {
        parent::__construct();
        $this->middleware('guest')->except('logout');
        $this->setTitle(__('messages.page_title.admin.login'));
        $this->adminUserValidator = app(AdminUserValidator::class);
    }

    public function showLoginForm()
    {
        return $this->render('auth.login');
    }

    public function login()
    {
        if (!$this->adminUserValidator->validateLogin(request()->all())) {
            return redirect()->back()
                ->withErrors($this->adminUserValidator->errorsBag())
                ->withInput(request()->except('password'));
        }

        $userData = [
            'email' => request()->get('email'),
            'password' => request()->get('password'),
        ];

        if (getGuard()->attempt($userData)) {
            return redirect(getRoute('home'));
        }

        $errors = new MessageBag(['email' => [__('messages.email_password_valid')]]);

        return redirect()->back()
            ->withErrors($errors)
            ->withInput(request()->except('password'));
    }

    public function logout()
    {
        getGuard()->logout();
        return redirect(getRoute('login'));
    }
}
