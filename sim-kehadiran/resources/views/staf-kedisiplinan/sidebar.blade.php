<a href="{{ route('staf-kedisiplinan.dashboard') }}"
   class="{{ request()->routeIs('staf-kedisiplinan.dashboard') ? 'active' : '' }}">
    <i class="ph ph-house"></i>
    <span>Dashboard</span>
</a>

<a href="{{ route('staf-kedisiplinan.manajemen-kehadiran') }}"
   class="{{ request()->routeIs('staf-kedisiplinan.manajemen-kehadiran') ? 'active' : '' }}">
    <i class="ph ph-calendar-check"></i>
    <span>Manajemen Kehadiran</span>
</a>

<a href="{{ route('staf-kedisiplinan.laporan-rekapitulasi') }}"
   class="{{ request()->routeIs('staf-kedisiplinan.laporan-rekapitulasi*') ? 'active' : '' }}">
    <i class="ph ph-chart-bar"></i>
    <span>Laporan & Rekapitulasi</span>
</a>

<a href="{{ route('staf-kedisiplinan.manajemen-akun') }}"
   class="{{ request()->routeIs('staf-kedisiplinan.manajemen-akun*') ? 'active' : '' }}">
    <i class="ph ph-users"></i>
    <span>Manajemen Akun</span>
</a>



