<?php

namespace App\Http\Controllers;
use App\Http\Controllers\GuruTendikController;
use App\Http\Controllers\StafKedisiplinanController;
use App\Http\Controllers\KepalaSekolahController;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $roleId = auth()->user()->role_id;
        // dd(auth()->user()->id, auth()->user()->nama, auth()->user()->role_id);
        

        if ($roleId == 1) {
            return redirect()->action([GuruTendikController::class, 'dashboard']);
        }

        if ($roleId == 2) {
            return redirect()->action([StafKedisiplinanController::class, 'dashboard']);
        }

        if ($roleId == 3) {
            return redirect()->action([KepalaSekolahController::class, 'dashboard']);
        }

        abort(403);
    }
}
