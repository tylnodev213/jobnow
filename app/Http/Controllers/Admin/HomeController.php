<?php

namespace App\Http\Controllers\Admin;

class HomeController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->setTitle(__('messages.page_title.admin.home'));
    }

    public function index()
    {
        return $this->render();
    }
}
