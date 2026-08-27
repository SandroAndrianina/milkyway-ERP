<?php

namespace App\Controllers;

class Clients extends BaseController
{
    public function index()
    {
        return view('ecoulement/clients');
    }
}