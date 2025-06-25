<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="{{ route('taxis.index') }}">Taxi Manager</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link @if(request()->routeIs('taxis.*')) active @endif" href="{{ route('taxis.index') }}">Taxis</a>
        </li>
        <li class="nav-item">
          <a class="nav-link @if(request()->routeIs('daily-records.*')) active @endif" href="{{ route('daily-records.create') }}">Daily Records</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ route('taxis.create') }}">Add Taxi</a>
        </li>
        <li class="nav-item">
         <a class="nav-link" href="{{ route('taxis.manage') }}">Taxi Manager</a>

        </li>

      </ul>

      <ul class="navbar-nav ms-auto">
        @auth
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button"
               data-bs-toggle="dropdown" aria-expanded="false">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                   class="bi bi-person-circle me-2" viewBox="0 0 16 16">
                <path d="M13.468 12.37C12.758 11.226 11.468 10.5 8 10.5s-4.758.726-5.468 1.87A6.987 6.987 0 0 0 8 15a6.987 6.987 0 0 0 5.468-2.63z"/>
                <path fill-rule="evenodd" d="M8 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                <path fill-rule="evenodd" d="M8 1a7 7 0 1 0 0 14A7 7 0 0 0 8 1z"/>
              </svg>
              {{ Auth::user()->name }}
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
              <li>
                <a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a>
              </li>
              <li><hr class="dropdown-divider"></li>
              <li>
                <form method="POST" action="{{ route('logout') }}">
                  @csrf
                  <button type="submit" class="dropdown-item text-danger">Logout</button>
                </form>
              </li>
            </ul>
          </li>
        @else
          <li class="nav-item">
            <a class="nav-link" href="{{ route('login') }}">Login</a>
          </li>
          @if (Route::has('register'))
            <li class="nav-item">
              <a class="nav-link" href="{{ route('register') }}">Register</a>
            </li>
          @endif
        @endauth
      </ul>
    </div>
  </div>
</nav>
