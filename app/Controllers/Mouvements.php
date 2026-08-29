<?php

namespace App\Controllers;

class Mouvements extends BaseController
{
    public function index()
    {
        return view('ecoulement/mouvements');
    }
}