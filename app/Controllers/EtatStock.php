<?php

namespace App\Controllers;

class EtatStock extends BaseController
{
    public function index()
    {
        return view('ecoulement/etat-stock');
    }
}