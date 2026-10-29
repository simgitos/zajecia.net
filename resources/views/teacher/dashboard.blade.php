<x-app-layout>
    
    <x-user-header 
        title="Witaj, {{ explode(' ',Auth::user()->name)[0] }} 👋" 
        description="Zarządzaj swoimi grupami, realizuj zajęcia i dbaj o ewidencję obecności." 
    />

    <div class="page-body">
        <div class="container-xl">

            {{-- ===================== 3 KAFELKI STATYSTYK ===================== --}}
            <div class="row g-3 mb-4">


                {{-- Kafelek: Moje zajęcia --}}
                
                <x-stats icon="ti ti-books" color="success" title="Moje zajęcia" value="{{ $teacherCourses->count() }}" route="{{ route('teacher.courses.index') }}" description="Zarządzaj swoimi zajęciami" ></x-stats>
                

                {{-- Kafelek: Uczestnicy --}}
                <x-stats icon="ti ti-users" color="warning" title="Uczestnicy" value="{{ $assignedChildrenCount }}" route="{{ route('teacher.children.index') }}" description="Zarządzaj uczestnikami" ></x-stats>
                

                {{-- Kafelek: Zrealizowane --}}
                <x-stats icon="ti ti-calendar-check" color="primary" title="Zrealizowane" value="{{ $currentMonthLessonsCount }}" route="{{ route('teacher.lessons.index') }}" description="Zrealizowane zajęcia" ></x-stats>
                
                
            </div>


        </div>
    </div>
</x-app-layout>