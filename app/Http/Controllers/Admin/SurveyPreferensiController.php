<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SurveyPreferensi;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SurveyPreferensiController extends Controller
{
    public function index(Request $request): View
    {
        $survey = SurveyPreferensi::with(['guestVisitor', 'wisata.kategoriWisata'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = '%'.$request->string('search').'%';
                $query->whereHas('guestVisitor', fn ($guest) => $guest->where('kode_guest', 'like', $search))
                    ->orWhereHas('wisata', fn ($wisata) => $wisata->where('nama_wisata', 'like', $search));
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.survey.index', compact('survey'));
    }
}
