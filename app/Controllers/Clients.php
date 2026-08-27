<?php

namespace App\Controllers;

class Clients extends BaseController
{
    public function index()
    {
        return view('ecoulement/clients');
    }

    public function details($id)
    {
        return view('ecoulement/client_details', ['clientId' => $id]);
    }
}