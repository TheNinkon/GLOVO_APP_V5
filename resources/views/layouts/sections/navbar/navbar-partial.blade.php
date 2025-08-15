@php
  use Illuminate\Support\Facades\Auth;

  $user = Auth::guard('web')->user() ?? Auth::guard('rider')->user();
  $userGuard = Auth::guard('web')->check() ? 'web' : 'rider';
@endphp

{{-- Puedes mantener o eliminar las notificaciones y otros iconos de la demo si quieres --}}
<div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
  <ul class="navbar-nav flex-row align-items-center ms-auto">

    <li class="nav-item navbar-dropdown dropdown-user dropdown">
      <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
        <div class="avatar avatar-online">
          <img src="{{ asset('assets/img/avatars/1.png') }}" alt class="h-auto rounded-circle">
        </div>
      </a>
      <ul class="dropdown-menu dropdown-menu-end">
        <li>
          <a class="dropdown-item" href="#">
            <div class="d-flex">
              <div class="flex-shrink-0 me-3">
                <div class="avatar avatar-online">
                  <img src="{{ asset('assets/img/avatars/1.png') }}" alt class="h-auto rounded-circle">
                </div>
              </div>
              <div class="flex-grow-1">
                <span class="fw-medium d-block">{{ $user->name ?? $user->full_name }}</span>
                <small class="text-muted">{{ ucfirst($userGuard) }}</small>
              </div>
            </div>
          </a>
        </li>
        <li>
          <div class="dropdown-divider"></div>
        </li>
        <li>
          <a class="dropdown-item" href="#">
            {{-- ICONO CORREGIDO --}}
            <i class="ti tabler-user-check me-2 ti-sm"></i>
            <span class="align-middle">My Profile</span>
          </a>
        </li>
        <li>
          <a class="dropdown-item" href="#">
            {{-- ICONO CORREGIDO --}}
            <i class="ti tabler-settings me-2 ti-sm"></i>
            <span class="align-middle">Settings</span>
          </a>
        </li>
        <li>
          <div class="dropdown-divider"></div>
        </li>
        <li>
          {{-- Lógica de Logout con icono corregido --}}
          @if ($userGuard == 'web')
            <a class="dropdown-item" href="{{ route('admin.logout') }}"
              onclick="event.preventDefault(); document.getElementById('logout-form-admin').submit();">
              <i class='ti tabler-logout me-2 ti-sm'></i>
              <span class="align-middle">Logout</span>
            </a>
            <form method="POST" id="logout-form-admin" action="{{ route('admin.logout') }}">
              @csrf
            </form>
          @elseif ($userGuard == 'rider')
            <a class="dropdown-item" href="{{ route('rider.logout') }}"
              onclick="event.preventDefault(); document.getElementById('logout-form-rider').submit();">
              <i class='ti tabler-logout me-2 ti-sm'></i>
              <span class="align-middle">Logout</span>
            </a>
            <form method="POST" id="logout-form-rider" action="{{ route('rider.logout') }}">
              @csrf
            </form>
          @endif
        </li>
      </ul>
    </li>
  </ul>
</div>
