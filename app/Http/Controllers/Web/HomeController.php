<?php

namespace App\Http\Controllers\Web;

class HomeController extends WebController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        return $this->render();
    }
}
