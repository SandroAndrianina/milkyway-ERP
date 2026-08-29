<?php

namespace App\Controllers;

class Recapitulation extends BaseController
{
    public function index()
    {
        return view('ecoulement/recapitulation');
    }
}