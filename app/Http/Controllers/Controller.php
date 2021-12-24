<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    //karena var data digunakan di seluruh controller jadi di buat variabel global
    protected $data=[];

    //definisi variable penyimpanan menu active
    public function __construct()
    {
        //Method baru untuk menu admin
        $this->initAdminMenu();
    }

    private function initAdminMenu() {
        $this->data['currentAdminMenu'] = 'dashboard';//default tampilan
        $this->data['currentAdminSubMenu'] = '';//untuk sub menu (meng-override )
    }

    //protected agar bisa diakses di controller dibawahnya
    protected function load_theme($view, $data = [])
    {
        return view('themes/'. env('APP_THEME') .'/'. $view, $data); //mengambil dari env
    }
}
