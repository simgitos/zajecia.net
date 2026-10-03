<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\View\View;

class SchoolController extends Controller
{
    /**
     * Lista szkół / placówek.
     */
    public function index(): View
    {
        $schools = School::latest()->get();
        return view('front.schools.index', compact('schools'));
    }

    /**
     * Landing page konkretnej szkoły.
     */
    public function show(School $school): View
    {
        return view('front.schools.show', compact('school'));
    }
}
