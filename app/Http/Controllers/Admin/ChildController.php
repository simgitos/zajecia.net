<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Child;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ChildController extends Controller
{
    /**
     * Wyświetla listę wszystkich dzieci w szkole administratora.
     */
    public function index(Request $request): View
    {
        $schoolId = Auth::user()->school_id;

        $query = Child::where('school_id', $schoolId)
            ->with(['parent', 'courses']);

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('parent', function ($pq) use ($search) {
                      $pq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $children = $query->latest()->paginate(15);

        return view('admin.children.index', compact('children'));
    }
}
