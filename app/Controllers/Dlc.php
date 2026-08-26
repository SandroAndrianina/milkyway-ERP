<?php

namespace App\Controllers;

class Dlc extends BaseController
{
    public function catalogue()
    {
        return view('dlc/catalogue');
    }

    public function calculateur()
    {
        return view('dlc/calculateur');
    }
}