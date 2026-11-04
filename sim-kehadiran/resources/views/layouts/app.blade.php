<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>SIM Kehadiran SDIT Daarul Fataa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>

<body>

<div class="wrapper">

    {{-- ================= SIDEBAR ================= --}}
    <div class="sidebar d-flex flex-column">

    {{-- ================= PROFILE ================= --}}
    <div class="profile text-center p-3 border-bottom">

        @if(auth()->user()->foto)

            <img
                src="{{ asset('storage/' . auth()->user()->foto) }}"
                class="profile-photo rounded-circle">

        @else

            <div class="profile-photo bg-success rounded-circle d-flex align-items-center justify-content-center mx-auto">

                <strong>{{ strtoupper(substr(auth()->user()->nama,0,1)) }}</strong>

            </div>

        @endif

        <!-- profile info -->
        <div class="profile-info mt-3">

            <div class="fw-semibold">

                {{ auth()->user()->nama }}

            </div>

            <small>

                @switch(auth()->user()->role_id)

                    @case(1)
                        Guru / Tendik
                        @break

                    @case(2)
                        Staf Kedisiplinan
                        @break

                    @case(3)
                        Kepala Sekolah
                        @break

                @endswitch

            </small>

        </div>

        

    </div>

    {{-- ================= MENU ================= --}}
    <div class="flex-grow-1">

        @php
            $role = auth()->user()->role_id;
        @endphp

        @if($role==1)
            @include('guru-tendik.sidebar')
        @elseif($role==2)
            @include('staf-kedisiplinan.sidebar')
        @elseif($role==3)
            @include('kepala-sekolah.sidebar')
        @endif

    </div>

    {{-- ================= FOOTER ================= --}}
    <div class="sidebar-footer">

        <div class="footer-title">
            SIM Kehadiran
        </div>

        <div class="footer-version">
            Version 1.0
        </div>

        <div class="footer-copy">
            © {{ date('Y') }}
        </div>

    </div>

</div>

    {{-- ================= MAIN ================= --}}
    <div class="main">

        {{-- Header --}}
        <div class="header">

            <div class="d-flex align-items-center gap-3">

                <button
                    id="toggleSidebar"
                    class="btn btn-light border">

                    <i class="ph ph-list"></i>

                </button>

                <img
                    src="{{ asset('images/logo.png') }}"
                    height="38">

                <div>

                    <div class="fw-bold">
                        SIM Kehadiran
                    </div>

                    <small class="text-muted">
                        SDIT Daarul Fataa
                    </small>

                </div>

            </div>

            <form method="POST" action="{{ route('logout') }}">

                @csrf

                <button
                    class="btn btn-outline-danger"
                    title="Logout">

                    <i class="ph ph-sign-out"></i>

                </button>

            </form>

        </div>

        {{-- Content --}}
        <div class="content">

            @yield('content')

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

    @if(session('success'))

        Swal.fire({
            icon:'success',
            title:'Berhasil',
            text:'{{ session('success') }}',
            confirmButtonColor:'#198754'
        });

    @endif

    @if(session('error'))

        Swal.fire({
            icon:'error',
            title:'Gagal',
            text:'{{ session('error') }}',
            confirmButtonColor:'#dc3545'
        });

    @endif

    const body = document.body;

    const toggle = document.getElementById("toggleSidebar");

    if(localStorage.getItem("sidebar") === "collapse"){

        body.classList.add("sidebar-collapse");

    }

    toggle.addEventListener("click",()=>{

        body.classList.toggle("sidebar-collapse");

        if(body.classList.contains("sidebar-collapse")){

            localStorage.setItem("sidebar","collapse");

        }else{

            localStorage.setItem("sidebar","expand");

        }

    });

</script>
@stack('scripts')
</body>
</html>