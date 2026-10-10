<x-app-layout>
    <div class="page-header d-print-none mb-4">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="mb-1">
                        <a href="{{ route('teacher.courses.index') }}" class="text-secondary">
                            <i class="ti ti-arrow-left me-1"></i> Powrót do moich zajęć
                        </a>
                    </div>
                    <h2 class="page-title text-primary fw-bold">
                        <i class="ti ti-school me-2"></i> Zajęcia: {{ $course->title }}
                    </h2>
                </div>
                <div class="col-auto ms-auto d-print-none d-flex gap-2">
                    <a href="{{ route('teacher.lessons.create', $course->id) }}" class="btn btn-success">
                        <i class="ti ti-checkup-list me-1"></i> Przeprowadź zajęcia
                    </a>
                    <button type="button" onclick="window.print();" class="btn btn-outline-secondary">
                        <i class="ti ti-printer me-1"></i> Drukuj listę
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm mb-3" role="alert">
                    <i class="ti ti-check me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Toast / Powiadomienie o zmianie kolejności Drag & Drop -->
            <div id="reorder-toast" class="alert alert-success shadow-sm mb-3 d-none align-items-center" role="alert">
                <i class="ti ti-arrows-sort fs-2 me-2"></i>
                <div class="flex-grow-1">
                    <strong>Zapisano kolejność!</strong> Kolejność uczestników na zajęciach została zaktualizowana.
                </div>
                <button type="button" class="btn-close ms-2" onclick="document.getElementById('reorder-toast').classList.add('d-none')"></button>
            </div>

            <div class="row row-cards mb-4">
                <!-- Informacje o zajęciach dla prowadzącego -->
                <div class="col-md-4 d-print-none">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-surface border-bottom">
                            <h3 class="card-title fw-bold"><i class="ti ti-info-circle me-2 text-primary"></i> Informacje o zajęciach</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <span class="text-secondary d-block fs-5">Liczba zapisanych uczestników</span>
                                <strong class="fs-2 text-dark">{{ $course->children_count }} / {{ $course->max_participants }}</strong>
                            </div>

                            <div class="mb-3">
                                <span class="text-secondary d-block fs-5">Zrealizowane terminy</span>
                                <strong class="fs-2 text-primary">{{ $course->lessons->count() }}</strong>
                            </div>

                            <div class="mb-3">
                                <span class="text-secondary d-block fs-5">Sala</span>
                                @if($course->room)
                                    <span class="badge bg-blue-lt fs-4"><i class="ti ti-door me-1"></i>{{ $course->room->name }}</span>
                                @else
                                    <span class="text-muted">Nie określono</span>
                                @endif
                            </div>

                            <div class="mb-3">
                                <span class="text-secondary d-block fs-5">Typ zajęć</span>
                                <span class="badge {{ $course->type === 'group' ? 'bg-purple-lt' : 'bg-azure-lt' }} fs-4">
                                    {{ $course->type === 'group' ? 'Grupowe' : 'Indywidualne (1 os./sesja)' }}
                                </span>
                            </div>

                            @if($course->description)
                                <div>
                                    <span class="text-secondary d-block fs-5">Opis / Uwagi do programu</span>
                                    <p class="text-dark mb-0 fs-4">{{ $course->description }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Tabela zapisanych uczestników z Drag & Drop -->
                <div class="col-md-8 col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-surface border-bottom d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="card-title fw-bold mb-0">
                                    <i class="ti ti-users me-2 text-primary"></i> 
                                    @if($course->isIndividual())
                                        Kolejka uczestników zajęć indywidualnych ({{ $course->children->count() }})
                                    @else
                                        Lista zapisanych uczestników ({{ $course->children->count() }})
                                    @endif
                                </h3>
                                @if($course->children->count() > 1)
                                    <small class="text-muted">
                                        <i class="ti ti-arrows-sort me-1"></i> Przeciągnij wiersze, aby zmienić kolejność uczestników.
                                    </small>
                                @endif
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modal-enroll-children">
                                <i class="ti ti-user-plus me-1"></i> Dodaj uczestników
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table table-hover">
                                <thead>
                                    <tr>
                                        <th class="w-1"></th>
                                        <th class="w-1 text-center">Kolejność</th>
                                        <th>Imię i nazwisko</th>
                                        <th>Wiek / Kontakt</th>
                                        <th>Uwagi</th>
                                        <th class="w-1 text-end">Akcje</th>
                                    </tr>
                                </thead>
                                <tbody id="sortable-children">
                                    @forelse($course->children as $index => $child)
                                        <tr data-id="{{ $child->id }}" class="align-middle">
                                            <td class="cursor-grab text-muted drag-handle px-2" title="Chwyć i przeciągnij, aby zmienić kolejność">
                                                <i class="ti ti-grip-vertical fs-3"></i>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-secondary-lt fw-bold row-index fs-4">
                                                    {{ $index + 1 }}
                                                </span>
                                            </td>
                                            <td>
                                                <strong class="text-dark fs-3 d-block">{{ $child->name }}</strong>
                                                <small class="text-muted">{{ $child->birth_date ? $child->birth_date->format('d.m.Y') : '-' }} ({{ $child->age }} lat)</small>
                                            </td>
                                            <td>
                                                @if($child->parent)
                                                    <div class="fw-bold">{{ $child->parent->name }}</div>
                                                    <small class="text-muted"><i class="ti ti-mail me-1"></i>{{ $child->parent->email }}</small>
                                                @else
                                                    <span class="text-muted">Brak</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($child->parent_comment)
                                                    <span class="badge bg-warning-lt text-wrap" style="max-width: 180px;">
                                                        <i class="ti ti-alert-triangle me-1"></i> {{ $child->parent_comment }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-end text-nowrap">
                                                @if($course->isIndividual())
                                                    <a href="{{ route('teacher.lessons.create', [$course->id, 'child_id' => $child->id]) }}" class="btn btn-sm btn-success me-1" title="Przeprowadź lekcję indywidualną z tym uczestnikiem">
                                                        <i class="ti ti-checkup-list me-1"></i> Przeprowadź
                                                    </a>
                                                @endif
                                                <form action="{{ route('teacher.courses.unenroll-child', ['course' => $course->id, 'child' => $child->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Czy na pewno chcesz wypisać tego uczestnika z zajęć?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-ghost-danger btn-icon" title="Wypisz z zajęć">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-5">
                                                <i class="ti ti-user-off fs-1 d-block mb-2 text-secondary"></i>
                                                Brak zapisanych uczestników na te zajęcia.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabela zrealizowanych terminów -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-surface border-bottom d-flex justify-content-between align-items-center">
                    <h3 class="card-title fw-bold">
                        <i class="ti ti-history me-2 text-primary"></i> Lista zrealizowanych terminów ({{ $course->lessons->count() }})
                    </h3>
                    <a href="{{ route('teacher.lessons.create', $course->id) }}" class="btn btn-success btn-sm">
                        <i class="ti ti-plus me-1"></i> Przeprowadź nowy termin
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table table-hover">
                        <thead>
                            <tr>
                                <th class="w-1">Lp.</th>
                                <th>Data zajęć</th>
                                <th>Temat zajęć</th>
                                <th>
                                    @if($course->isIndividual())
                                        Uczestnik / Frekwencja
                                    @else
                                        Frekwencja (Obecni)
                                    @endif
                                </th>
                                <th>Notatka</th>
                                <th class="w-1">Akcje</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($course->lessons->sortByDesc('realized_at') as $index => $lesson)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <strong class="text-dark"><i class="ti ti-calendar me-1 text-primary"></i>{{ $lesson->realized_at->format('d.m.Y') }}</strong>
                                    </td>
                                    <td>
                                        <span class="fw-bold">{{ $lesson->topic ?: 'Brak tematu' }}</span>
                                    </td>
                                    <td>
                                        @if($course->isIndividual())
                                            @php
                                                $firstAtt = $lesson->attendances->first();
                                            @endphp
                                            @if($firstAtt && $firstAtt->child)
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="fw-bold text-dark fs-4">{{ $firstAtt->child->name }}</span>
                                                    @switch($firstAtt->status)
                                                        @case('present')
                                                            <span class="badge bg-success"><i class="ti ti-check me-1"></i>Obecny</span>
                                                            @break
                                                        @case('absent')
                                                            <span class="badge bg-danger"><i class="ti ti-x me-1"></i>Nieobecny</span>
                                                            @break
                                                        @case('excused')
                                                            <span class="badge bg-warning"><i class="ti ti-clock me-1"></i>Usprawiedliwiony</span>
                                                            @break
                                                    @endswitch
                                                </div>
                                            @else
                                                <span class="text-muted">Brak danych uczestnika</span>
                                            @endif
                                        @else
                                            @php
                                                $present = $lesson->attendances->where('status', 'present')->count();
                                                $total = $lesson->attendances->count();
                                            @endphp
                                            <span class="badge bg-success-lt fw-bold fs-4">
                                                <i class="ti ti-users me-1"></i> {{ $present }} / {{ $total }} obecnych
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ Str::limit($lesson->notes, 60) ?: '-' }}</small>
                                    </td>
                                    <td>
                                        <a href="{{ route('teacher.lessons.show', $lesson->id) }}" class="btn btn-outline-info btn-sm">
                                            <i class="ti ti-eye me-1"></i> Szczegóły
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
                                        <i class="ti ti-calendar-off fs-1 d-block mb-2 text-secondary"></i>
                                        Nie przeprowadzono jeszcze żadnych terminów dla tych zajęć.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal dodawania uczestników do zajęć -->
    <div class="modal modal-blur fade" id="modal-enroll-children" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <form action="{{ route('teacher.courses.enroll-children', $course) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">
                            <i class="ti ti-user-plus me-1 text-primary"></i> Dodaj uczestników do zajęć: {{ $course->title }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-secondary mb-3">
                            Zaznacz jednego lub wielu uczestników, których chcesz dopisać do grafiku tych zajęć.
                        </p>

                        @if(empty($availableChildren) || $availableChildren->isEmpty())
                            <div class="alert alert-info mb-0">
                                <i class="ti ti-info-circle me-1"></i> Wszyscy zarejestrowani uczestnicy w placówce są już zapisani na te zajęcia lub brak innych uczestników.
                            </div>
                        @else
                            <div class="mb-3">
                                <input type="text" id="filter-modal-children" class="form-control" placeholder="Filtruj uczestników po nazwisku...">
                            </div>

                            <div class="table-responsive" style="max-height: 320px; overflow-y: auto;">
                                <table class="table table-vcenter table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th class="w-1">
                                                <input type="checkbox" class="form-check-input" id="check-all-children" onclick="toggleAllModalChildren(this)">
                                            </th>
                                            <th>Uczestnik</th>
                                            <th>Wiek</th>
                                            <th>Opiekun</th>
                                        </tr>
                                    </thead>
                                    <tbody id="modal-children-list">
                                        @foreach($availableChildren as $avChild)
                                            <tr class="modal-child-row">
                                                <td>
                                                    <input type="checkbox" name="child_ids[]" value="{{ $avChild->id }}" class="form-check-input modal-child-checkbox">
                                                </td>
                                                <td>
                                                    <strong class="modal-child-name text-dark">{{ $avChild->name }}</strong>
                                                </td>
                                                <td>
                                                    <small class="text-muted">{{ $avChild->age }} lat</small>
                                                </td>
                                                <td>
                                                    <small class="text-muted">{{ $avChild->parent?->name ?? 'Brak' }}</small>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Anuluj</button>
                        <button type="submit" class="btn btn-primary" @if(empty($availableChildren) || $availableChildren->isEmpty()) disabled @endif>
                            <i class="ti ti-plus me-1"></i> Zapisz wybranych uczestników
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Skrypt SortableJS dla Drag & Drop -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const el = document.getElementById('sortable-children');
            if (el) {
                new Sortable(el, {
                    handle: '.drag-handle',
                    animation: 150,
                    ghostClass: 'table-active',
                    onEnd: function () {
                        // Aktualizacja numerków Lp. w tabeli
                        const rows = el.querySelectorAll('tr[data-id]');
                        const order = [];
                        rows.forEach((row, index) => {
                            order.push(row.getAttribute('data-id'));
                            const badge = row.querySelector('.row-index');
                            if (badge) {
                                badge.textContent = index + 1;
                            }
                        });

                        // Wysłanie zapisu AJAX do kontrolera
                        fetch('{{ route('teacher.courses.reorder-children', $course) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ order: order })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                const toast = document.getElementById('reorder-toast');
                                if (toast) {
                                    toast.classList.remove('d-none');
                                    setTimeout(() => {
                                        toast.classList.add('d-none');
                                    }, 4000);
                                }
                            }
                        })
                        .catch(err => {
                            console.error('Błąd zapisu kolejności:', err);
                            alert('Wystąpił błąd podczas zapisywania kolejności.');
                        });
                    }
                });
            }

            // Filtr uczestników w modalu
            const filterInput = document.getElementById('filter-modal-children');
            if (filterInput) {
                filterInput.addEventListener('input', function() {
                    const q = this.value.toLowerCase();
                    document.querySelectorAll('.modal-child-row').forEach(row => {
                        const name = row.querySelector('.modal-child-name')?.textContent.toLowerCase() || '';
                        row.style.display = name.includes(q) ? '' : 'none';
                    });
                });
            }
        });

        function toggleAllModalChildren(source) {
            document.querySelectorAll('.modal-child-checkbox').forEach(cb => {
                if (cb.closest('tr').style.display !== 'none') {
                    cb.checked = source.checked;
                }
            });
        }
    </script>
    <style>
        .cursor-grab { cursor: grab; }
        .cursor-grab:active { cursor: grabbing; }
    </style>
</x-app-layout>
