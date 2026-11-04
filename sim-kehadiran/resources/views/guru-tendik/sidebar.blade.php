<a href="{{ route('guru-tendik.dashboard') }}"
   class="{{ request()->routeIs('guru-tendik.dashboard') ? 'active' : '' }}">
    <i class="ph ph-house"></i>
    <span>Dashboard</span>
</a>

<a href="{{ route('guru-tendik.laporan-rekapitulasi') }}"
   class="{{ request()->routeIs('guru-tendik.laporan-rekapitulasi*') ? 'active' : '' }}">
    <i class="ph ph-chart-bar"></i>
    <span>Laporan & Rekapitulasi</span>
</a>