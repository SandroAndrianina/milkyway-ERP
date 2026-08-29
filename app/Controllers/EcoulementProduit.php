<?php

namespace App\Controllers;

class EcoulementProduit extends BaseController
{
    public function index()
    {
        return view('ecoulement/produits');
    }
}