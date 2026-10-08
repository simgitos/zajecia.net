<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\BillingItem;
use App\Models\Child;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use App\Services\BillingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Główny pulpit (Dashboard) dostosowany dynamicznie do roli zalogowanego użytkownika:
     * Admin, Nauczyciel/Instruktor oraz Rodzic/User.
     */
    public function index(Request $request, BillingService $billingService): View
    {
        $user = Auth::user();

        // 1. DLA ADMINISTRATORA
        if ($user->hasRole('admin')) {
            $schoolId = $user->school_id;

            $totalCoursesCount = Course::where('school_id', $schoolId)->count();
            $totalChildrenCount = Child::where('school_id', $schoolId)->count();
            $totalParentsCount = User::where('school_id', $schoolId)
                ->whereJsonContains('roles', 'user')
                ->count();
            $totalTeachersCount = User::where('school_id', $schoolId)
                ->whereJsonContains('roles', 'teacher')
                ->count();

            // Obłożenie kursów
            $courses = Course::where('school_id', $schoolId)
                ->with(['instructor', 'room'])
                ->withCount('children')
                ->get();

            $totalCapacity = (int) $courses->sum('max_participants');
            $totalEnrolled = (int) $courses->sum('children_count');
            $occupancyPercentage = $totalCapacity > 0 ? round(($totalEnrolled / $totalCapacity) * 100) : 0;

            // Statystyki płatności
            $billingItems = BillingItem::where('school_id', $schoolId)->get();
            $unpaidAmount = (float) $billingItems->where('status', 'unpaid')->sum('amount');
            $paidAmount = (float) $billingItems->where('status', 'paid')->sum('amount');

            // Ostatnio zrealizowane lekcje
            $recentLessons = Lesson::where('school_id', $schoolId)
                ->with(['course', 'teacher'])
                ->withCount('attendances')
                ->latest('realized_at')
                ->take(5)
                ->get();

            return view('admin.dashboard', compact(
                'totalCoursesCount',
                'totalChildrenCount',
                'totalParentsCount',
                'totalTeachersCount',
                'courses',
                'totalCapacity',
                'totalEnrolled',
                'occupancyPercentage',
                'unpaidAmount',
                'paidAmount',
                'recentLessons'
            ));
        }

        // 2. DLA NAUCZYCIELA / INSTRUKTORA
        if ($user->hasRole('teacher')) {
            $schoolId = $user->school_id;

            // Kursy gdzie dany nauczyciel jest instruktorem
            $teacherCourses = Course::where('instructor_id', $user->id)
                ->where('school_id', $schoolId)
                ->with(['room'])
                ->withCount('children')
                ->get();

            $courseIds = $teacherCourses->pluck('id');

            // Liczba przydzielonych dzieci w kursach nauczyciela
            $assignedChildrenCount = Child::whereHas('courses', function ($query) use ($courseIds) {
                $query->whereIn('courses.id', $courseIds);
            })->count();

            // Przeprowadzone lekcje w tym miesiącu
            $currentMonthLessonsCount = Lesson::where('teacher_id', $user->id)
                ->whereYear('realized_at', now()->year)
                ->whereMonth('realized_at', now()->month)
                ->count();

            // Ostatnio przeprowadzone lekcje przez tego nauczyciela
            $recentLessons = Lesson::where('teacher_id', $user->id)
                ->with(['course'])
                ->withCount('attendances')
                ->latest('realized_at')
                ->take(5)
                ->get();

            return view('teacher.dashboard', compact(
                'teacherCourses',
                'assignedChildrenCount',
                'currentMonthLessonsCount',
                'recentLessons'
            ));
        }

        // 3. DLA RODZICA / UŻYTKOWNIKA
        $billingService->syncParentAll($user);

        $children = Child::where('parent_id', $user->id)
            ->where('school_id', $user->school_id)
            ->with(['courses.instructor', 'courses.room'])
            ->get();

        $unpaidItems = BillingItem::where('parent_id', $user->id)
            ->where('school_id', $user->school_id)
            ->where('status', 'unpaid')
            ->where('amount', '>', 0)
            ->get();

        $paidItems = BillingItem::where('parent_id', $user->id)
            ->where('school_id', $user->school_id)
            ->where('status', 'paid')
            ->get();

        $totalUnpaidAmount = (float) $unpaidItems->sum('amount');
        $totalPaidAmount = (float) $paidItems->sum('amount');

        return view('user.dashboard', compact(
            'children',
            'unpaidItems',
            'paidItems',
            'totalUnpaidAmount',
            'totalPaidAmount'
        ));
    }
}
