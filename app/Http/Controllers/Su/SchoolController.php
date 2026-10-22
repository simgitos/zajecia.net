<?php

declare(strict_types=1);

namespace App\Http\Controllers\Su;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Services\TpayPaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SchoolController extends Controller
{
    /**
     * Lista wszystkich szkół w systemie.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));
        $planFilter = $request->input('plan');
        $statusFilter = $request->input('status');
        $whitelistFilter = $request->input('whitelist');

        $query = School::query()
            ->withCount(['users', 'courses', 'children'])
            ->latest('id');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('bank_account_number', 'like', "%{$search}%");
            });
        }

        if ($planFilter) {
            $query->where('subscription_plan', $planFilter);
        }

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        if ($whitelistFilter) {
            $query->where('tpay_whitelist_status', $whitelistFilter);
        }

        $schools = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => School::count(),
            'active' => School::where('status', 'active')->count(),
            'whitelisted' => School::where('tpay_whitelist_status', 'verified')->count(),
        ];

        return view('su.schools.index', compact('schools', 'search', 'planFilter', 'statusFilter', 'whitelistFilter', 'stats'));
    }

    /**
     * Formularz dodawania nowej placówki.
     */
    public function create(): View
    {
        return view('su.schools.create');
    }

    /**
     * Zapis nowej placówki.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'full_name' => ['nullable', 'string', 'max:255'],
            'nip' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'bank_account_number' => ['nullable', 'string', 'max:50'],
            'subscription_plan' => ['required', 'string', 'in:solo,start,standard,pro'],
            'status' => ['required', 'string', 'in:active,suspended,pending'],
        ]);

        $slug = Str::slug($validated['name']);
        $originalSlug = $slug;
        $counter = 1;
        while (School::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-" . (++$counter);
        }
        $validated['slug'] = $slug;

        $school = School::create($validated);

        return redirect()->route('su.schools.index')
            ->with('success', "Placówka \"{$school->name}\" została pomyślnie utworzona.");
    }

    /**
     * Formularz edycji placówki.
     */
    public function edit(School $school): View
    {
        return view('su.schools.edit', compact('school'));
    }

    /**
     * Aktualizacja danych placówki.
     */
    public function update(Request $request, School $school): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'full_name' => ['nullable', 'string', 'max:255'],
            'nip' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'bank_account_number' => ['nullable', 'string', 'max:50'],
            'subscription_plan' => ['required', 'string', 'in:solo,start,standard,pro'],
            'status' => ['required', 'string', 'in:active,suspended,pending'],
            'tpay_whitelist_status' => ['nullable', 'string', 'in:unverified,pending,verified,rejected'],
        ]);

        $school->update($validated);

        return redirect()->route('su.schools.edit', $school)
            ->with('success', "Dane placówki \"{$school->name}\" zostały zaktualizowane.");
    }

    /**
     * Zgłoszenie rachunku placówki do białej listy Tpay Collect (API POST /collect/bank-accounts).
     */
    public function tpayWhitelist(School $school, TpayPaymentService $tpayService): RedirectResponse
    {
        try {
            $result = $tpayService->addToWhitelist($school);

            return redirect()->route('su.schools.edit', $school)
                ->with('success', $result['message']);
        } catch (\Throwable $e) {
            return redirect()->route('su.schools.edit', $school)
                ->with('error', 'Błąd podczas zgłaszania rachunku do Tpay Collect: ' . $e->getMessage());
        }
    }

    /**
     * Ręczna zmiana statusu weryfikacji w białej liście (np. zatwierdzenie telefoniczne / przez panel merchanta).
     */
    public function tpayManualVerify(Request $request, School $school): RedirectResponse
    {
        $status = $request->input('status', 'verified');

        $school->update([
            'tpay_whitelist_status' => $status,
            'tpay_whitelist_at' => $status === 'verified' ? now() : null,
            'tpay_whitelist_response' => 'Ręczna weryfikacja przez SuperAdmina SU w dniu ' . now()->toDateTimeString(),
        ]);

        return redirect()->route('su.schools.edit', $school)
            ->with('success', "Status białej listy Tpay dla placówki został zmieniony na: {$status}.");
    }

    /**
     * Przełączenie kontekstu na daną szkołę.
     */
    public function switch(School $school): RedirectResponse
    {
        session(['active_school_id' => $school->id]);

        return redirect()->route('dashboard')
            ->with('success', "Przełączono aktywny kontekst na szkołę: {$school->name}");
    }

    /**
     * Usunięcie szkoły.
     */
    public function destroy(School $school): RedirectResponse
    {
        $name = $school->name;
        $school->delete();

        return redirect()->route('su.schools.index')
            ->with('success', "Placówka \"{$name}\" została usunięta.");
    }
}
