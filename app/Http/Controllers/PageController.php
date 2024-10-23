<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\User;
use Illuminate\Support\Facades\Auth;
use PDF;

use App\Imports\PacientesImport;
use Maatwebsite\Excel\Facades\Excel;

class PageController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function stream($folder, $filename){
        return Response::stream(function () {
              $file = public_path('public') + $folder + '/' + $filename;
              readfile($file);
        }, 200, ['content-type' => 'image/jpeg']);
    }

    /**
     * Display all the static pages when authenticated
     *
     * @param string $page
     * @return \Illuminate\View\View
     */
    public function index(string $page)
    {
        if (view()->exists("pages.{$page}")) {
            return view("pages.{$page}");
        }

        return abort(404);
    }


}
