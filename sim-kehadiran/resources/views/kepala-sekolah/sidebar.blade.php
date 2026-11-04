<a href="{{ route('kepala-sekolah.dashboard') }}"
   class="{{ request()->routeIs('kepala-sekolah.dashboard') ? 'active' : '' }}">
    <i class="ph ph-house"></i>
    <span>Dashboard</span>
</a>

<a href="{{ route('kepala-sekolah.laporan-rekapitulasi') }}"
   class="{{ request()->routeIs('kepala-sekolah.laporan-rekapitulasi*') ? 'active' : '' }}">
    <i class="ph ph-chart-bar"></i>
    <span>Laporan & Rekapitulasi</span>
</a>

