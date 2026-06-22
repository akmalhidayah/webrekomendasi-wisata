<?php

namespace App\Http\Controllers\Wisatawan;

use App\Http\Controllers\Controller;
use App\Services\GuestVisitorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GuestVisitorController extends Controller
{
    public function store(Request $request, GuestVisitorService $service): RedirectResponse
    {
        $service->getOrCreateGuestVisitor($request);

        return redirect()->route('wisatawan.survey.index');
    }
}
