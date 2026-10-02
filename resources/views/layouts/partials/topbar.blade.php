 <header class="navbar navbar-expand-md d-none d-lg-flex d-print-none">
        <div class="container-xl">
          <div class="navbar-nav ms-auto">
            <div class="nav-item dropdown">
              <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown">
                <div class="d-none d-xl-block ps-2">
                  <div>
                    @if(!Auth::check())
                      <a href="{{ route('login') }}">Zaloguj sie</a>
                      <a href="{{ route('register') }}">Zarejestruj sie</a>
                    @else
                      {{ Auth::user()->name }}
                    @endif
                  </div>
                </div>
              </a>
              <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                <!-- Wylogowanie zgodne z Laravel Auth -->
                <form method="POST" action="{{ route('logout') }}">
                  @csrf
                  <button type="submit" class="dropdown-item text-danger">Wyloguj się</button>
                </form>
                <a href="{{ route('profile.edit') }}" class="dropdown-item">Edytuj profil</a>
                <a href="{{ route('dashboard') }}" class="dropdown-item">Panel Admina</a>
              </div>
            </div>
          </div>
        </div>
      </header>