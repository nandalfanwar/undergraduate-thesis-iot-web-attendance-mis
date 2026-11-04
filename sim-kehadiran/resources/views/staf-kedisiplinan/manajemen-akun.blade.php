@extends('layouts.app')

@section('content')

<!-- Alerts -->
@if(session()->has('success'))
<script>
Swal.fire({
    icon: 'success',
    title: 'Berhasil',
    text: @json(session('success'))
});
</script>
@endif

@if($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

@if($errors->any())

<div class="alert alert-danger">
    <ul class="mb-0">

        @foreach($errors->all() as $error)

            <li>{{ $error }}</li>

        @endforeach

    </ul>
</div>

@endif
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Manajemen Akun</h3>
        <p class="text-muted mb-0">Kelola akun pengguna sistem di sini.</p>
    </div>
    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#tambahAkun">
        <i class="ph ph-user-plus me-1"></i> Tambah Akun
    </button>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-4 ms-auto">
                <input type="text" class="form-control" id="searchInput" placeholder="Cari nama atau username...">
            </div>
        </div>
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th class="text-center" width="50">No</th>
                    <th>Nama Pengguna</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>RFID</th>
                    <th width="220">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            @if($item->foto)
                                <img src="{{ asset('storage/' . $item->foto) }}" class="rounded-circle me-3" width="42" height="42" style="object-fit: cover;">
                            @else
                                <div class="avatar-placeholder me-3">
                                    {{ strtoupper(substr($item->nama ?? '-', 0, 1)) }}
                                </div>
                            @endif
                            <span>{{ $item->nama ?? '-' }}</span>
                        </div>
                    </td>
                    <td>{{ $item->username }}</td>
                    <td>
                        @if(optional($item->role)->nama_role == 'Guru/Tendik')
                            <span class="badge bg-success">Guru / Tendik</span>
                        @elseif(optional($item->role)->nama_role == 'Staf Kedisiplinan')
                            <span class="badge bg-warning text-dark">Staf</span>
                        @else
                            <span class="badge bg-primary">Kepala Sekolah</span>
                        @endif
                    </td>
                    <td>
                        @if($item->uid_rfid)
                            <span class="badge bg-success-subtle text-success border">
                                <i class="ph ph-check-circle me-1"></i> Terdaftar
                            </span>
                        @else
                            <span class="badge bg-light text-secondary border">
                                <i class="ph ph-clock me-1"></i> Belum
                            </span>
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editAkun{{ $item->id }}" title="Edit Akun">
                            <i class="ph ph-pencil"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#hapusAkun{{ $item->id }}" title="Hapus Akun">
                            <i class="ph ph-trash"></i>
                        </button>
                        <a href="{{ route('staf-kedisiplinan.manajemen-akun.rfid', $item->id) }}" class="btn btn-info btn-sm" title="Kelola RFID">
                            <i class="ph ph-credit-card"></i>
                        </a>
                    </td>
                </tr>

                {{-- Modal Edit --}}

                <!-- <div
                    class="modal fade"
                    id="editAkun{{ $item->id }}"
                    tabindex="-1">

                    <div class="modal-dialog">

                        <div class="modal-content">

                            <form
                                action="{{ route('staf-kedisiplinan.manajemen-akun.update',$item->id) }}"
                                method="POST">

                                @csrf
                                @method('PUT')

                                <div class="modal-header">

                                    <h5 class="modal-title">

                                        Edit Akun

                                    </h5>

                                    <button
                                        class="btn-close"
                                        data-bs-dismiss="modal">
                                    </button>

                                </div>

                                <div class="modal-body">

                                    <div class="mb-3">

                                        <label class="form-label">

                                            Nama

                                        </label>

                                        <input
                                            type="text"
                                            name="nama"
                                            class="form-control"
                                            value="{{ $item->nama }}"
                                            placeholder="Masukkan nama lengkap"
                                            required>

                                    </div>

                                    <div class="mb-3">

                                        <label class="form-label">

                                            Email

                                        </label>

                                        <input
                                            type="email"
                                            name="email"
                                            class="form-control"
                                            value="{{ $item->email }}"
                                            placeholder="nama@email.com"
                                            required>

                                    </div>

                                    <div class="mb-3">

                                        <label class="form-label">

                                            Username

                                        </label>

                                        <input
                                            type="text"
                                            name="username"
                                            class="form-control"
                                            value="{{ $item->username }}"
                                            placeholder="Masukkan username"
                                            required>

                                    </div>

                                    <div class="mb-3">

                                        <label class="form-label">

                                            Password Baru

                                        </label>

                                        <input
                                            type="password"
                                            name="password"
                                            class="form-control">

                                        <small class="text-muted">

                                            Kosongkan jika tidak ingin mengganti password.

                                        </small>

                                    </div>

                                    <div class="mb-3">

                                        <label class="form-label">

                                            Role

                                        </label>

                                        <select
                                            name="role_id"
                                            class="form-select">

                                            @foreach($roles as $role)

                                            <option
                                                value="{{ $role->id }}"
                                                {{ $item->role_id == $role->id ? 'selected' : '' }}>

                                                {{ $role->nama_role }}

                                            </option>

                                            @endforeach

                                        </select>

                                    </div>

                                </div>

                                <div class="modal-footer">

                                    <button
                                        type="button"
                                        class="btn btn-secondary"
                                        data-bs-dismiss="modal">

                                        Batal

                                    </button>

                                    <button
                                        class="btn btn-success">

                                        Simpan

                                    </button>

                                </div>

                            </form>

                        </div>

                    </div>

                </div> -->

                <div class="modal fade" id="editAkun{{ $item->id }}" tabindex="-1">

                    <div class="modal-dialog modal-lg modal-dialog-centered">

                        <div class="modal-content border-0 shadow">

                            <form
                                action="{{ route('staf-kedisiplinan.manajemen-akun.update', $item->id) }}"
                                method="POST"
                                enctype="multipart/form-data">

                                @csrf
                                @method('PUT')

                                <!-- HEADER -->
                                <div class="modal-header">

                                    <div>
                                        <h5 class="modal-title mb-1">
                                            Ubah Akun
                                        </h5>

                                        <small>
                                            Perbarui informasi akun pengguna.
                                        </small>
                                    </div>

                                    <button
                                        type="button"
                                        class="btn-close"
                                        data-bs-dismiss="modal">
                                    </button>

                                </div>

                                <div class="modal-body p-4">

                                    <!-- DATA PEGAWAI -->
                                    <div class="mb-4">

                                        <h6 class="fw-bold mb-3">
                                            <i class="bi bi-person-vcard me-2"></i>
                                            Data Pegawai
                                        </h6>

                                        <div class="row">

                                            <div class="col-md-6 mb-3">

                                                <label class="form-label">
                                                    NUPTK
                                                </label>

                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    value="{{ $item->nuptk }}"
                                                    readonly>

                                            </div>

                                            <div class="col-md-6 mb-3">

                                                <label class="form-label">
                                                    Nama Lengkap
                                                </label>

                                                <input
                                                    type="text"
                                                    name="nama"
                                                    class="form-control"
                                                    value="{{ $item->nama }}"
                                                    required>

                                            </div>

                                            <div class="col-md-6 mb-3">

                                                <label class="form-label">
                                                    Role
                                                </label>

                                                <select
                                                    name="role_id"
                                                    class="form-select"
                                                    required>

                                                    @foreach($roles as $role)

                                                    <option
                                                        value="{{ $role->id }}"
                                                        {{ $item->role_id == $role->id ? 'selected' : '' }}>

                                                        {{ $role->nama_role }}

                                                    </option>

                                                    @endforeach

                                                </select>

                                            </div>

                                            <div class="col-md-6 mb-3">

                                                <label class="form-label">
                                                    UID RFID
                                                </label>

                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    value="{{ $item->uid_rfid ?? 'Belum terpasang' }}"
                                                    readonly>

                                            </div>

                                        </div>

                                    </div>

                                    <hr>

                                    <!-- DATA LOGIN -->
                                    <div>

                                        <h6 class="fw-bold mb-3">
                                            <i class="bi bi-shield-lock me-2"></i>
                                            Data Login
                                        </h6>

                                        <div class="row">

                                            <div class="col-md-6 mb-3">

                                                <label class="form-label">
                                                    Email
                                                </label>

                                                <input
                                                    type="email"
                                                    name="email"
                                                    class="form-control"
                                                    value="{{ $item->email }}"
                                                    required>

                                            </div>

                                            <div class="col-md-6 mb-3">

                                                <label class="form-label">
                                                    Username
                                                </label>

                                                <input
                                                    type="text"
                                                    name="username"
                                                    class="form-control"
                                                    value="{{ $item->username }}"
                                                    required>

                                            </div>

                                            <div class="col-md-6 mb-3">

                                                <label class="form-label">
                                                    Password Baru
                                                </label>

                                                <div class="input-group">

                                                    <span class="input-group-text">
                                                        <i class="bi bi-key"></i>
                                                    </span>

                                                    <input
                                                        type="password"
                                                        name="password"
                                                        id="passwordEdit{{ $item->id }}"
                                                        class="form-control"
                                                        placeholder="Kosongkan jika tidak diubah">

                                                    <button
                                                        type="button"
                                                        class="btn btn-outline-secondary"
                                                        onclick="togglePassword('passwordEdit{{ $item->id }}', this)">

                                                        <i class="bi bi-eye"></i>

                                                    </button>

                                                </div>

                                                <small class="text-muted">
                                                    Kosongkan jika tidak ingin mengganti password.
                                                </small>

                                            </div>

                                            <div class="col-md-6 mb-3">

                                                <label class="form-label">
                                                    Foto Profil
                                                </label>

                                                <input
                                                    type="file"
                                                    name="foto"
                                                    class="form-control"
                                                    accept="image/*">

                                                @if($item->foto)

                                                    <img
                                                        src="{{ asset('storage/'.$item->foto) }}"
                                                        class="img-thumbnail mt-2"
                                                        width="90">

                                                @endif

                                            </div>

                                        </div>

                                    </div>

                                </div>

                                <!-- FOOTER -->
                                <div class="modal-footer">

                                    <button
                                        type="button"
                                        class="btn btn-secondary"
                                        data-bs-dismiss="modal">

                                        <i class="bi bi-x-circle me-1"></i>
                                        Batal

                                    </button>

                                    <button
                                        type="submit"
                                        class="btn btn-success">

                                        <i class="bi bi-save me-1"></i>
                                        Simpan Perubahan

                                    </button>

                                </div>

                            </form>

                        </div>

                    </div>

                </div>

                {{-- Modal Hapus Akun --}}
                <div
                    class="modal fade"
                    id="hapusAkun{{ $item->id }}"
                    tabindex="-1">

                    <div class="modal-dialog">

                        <div class="modal-content">

                            <form
                                action="{{ route('staf-kedisiplinan.manajemen-akun.destroy',$item->id) }}"
                                method="POST">

                                @csrf
                                @method('DELETE')

                                <div class="modal-header">

                                    <h5 class="modal-title">

                                        Hapus Akun

                                    </h5>

                                    <button
                                        class="btn-close"
                                        data-bs-dismiss="modal">
                                    </button>

                                </div>

                                <div class="modal-body">

                                    Apakah yakin ingin menghapus akun

                                    <strong>

                                        {{ $item->nama }}

                                    </strong>?

                                </div>

                                <div class="modal-footer">

                                    <button
                                        type="button"
                                        class="btn btn-secondary"
                                        data-bs-dismiss="modal">

                                        Batal

                                    </button>

                                    <button
                                        class="btn btn-danger">

                                        Hapus

                                    </button>

                                </div>

                            </form>

                        </div>

                    </div>

                </div>

                @empty

                <tr>

                    <td
                        colspan="6"
                        class="text-center">

                        Tidak ada data.

                    </td>

                </tr>
                @endforelse
            </tbody>
        </table>
        {{ $users->links() }}
    </div>
</div>

{{-- Modal Tambah Akun --}}


<div class="modal fade" id="tambahAkun" tabindex="-1">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content border-0 shadow">


            <form 
                action="{{ route('staf-kedisiplinan.manajemen-akun.store') }}"
                method="POST"
                enctype="multipart/form-data">

                @csrf


                <!-- HEADER -->
                <div class="modal-header">

                    <div>
                        <h5 class="modal-title mb-1">
                            Tambah Akun
                        </h5>

                        <small>
                            Tambahkan akun guru atau tenaga kependidikan
                        </small>
                    </div>


                    <button 
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>

                </div>

                <div class="modal-body p-4">
                    <!-- DATA PEGAWAI -->
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3">
                            <i class="bi bi-person-vcard me-2"></i>
                            Data Pegawai
                        </h6>
                        <div class="row">


                            <!-- NUPTK -->
                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    NUPTK
                                </label>


                                <div class="input-group">

                                    <span class="input-group-text">
                                        <i class="bi bi-card-text"></i>
                                    </span>


                                    <input
                                        type="text"
                                        name="nuptk"
                                        class="form-control"
                                        placeholder="NUPTK berisi 16 digit angka"
                                        maxlength="16"
                                        inputmode="numeric"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                        required>

                                </div>

                            </div>

                            <!-- Nama -->
                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Nama Lengkap
                                </label>


                                <div class="input-group">

                                    <span class="input-group-text">
                                        <i class="bi bi-person"></i>
                                    </span>


                                    <input
                                        type="text"
                                        name="nama"
                                        class="form-control"
                                        placeholder="Masukkan nama lengkap guru/tendik"
                                        required>

                                </div>

                            </div>

                            <!-- Role -->
                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Role
                                </label>


                                <div class="input-group">

                                    <span class="input-group-text">
                                        <i class="bi bi-person-badge"></i>
                                    </span>


                                    <select
                                        name="role_id"
                                        class="form-select"
                                        required>


                                        <option value="">
                                            Pilih Role
                                        </option>


                                        @foreach($roles as $role)

                                            <option value="{{ $role->id }}">
                                                {{ $role->nama_role }}
                                            </option>

                                        @endforeach


                                    </select>

                                </div>


                            </div>

                            <!-- RFID -->
                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    UID RFID
                                </label>


                                <div class="input-group">


                                    <span class="input-group-text">
                                        <i class="bi bi-credit-card"></i>
                                    </span>


                                    <input
                                        type="text"
                                        name="uid_rfid"
                                        id="uid_rfid"
                                        class="form-control"
                                        placeholder="Belum terpasang"
                                        readonly>

                                </div>


                            </div>


                        </div>


                    </div>

                    <hr>

                    <!-- DATA LOGIN -->
                    <div class="mb-4">


                        <h6 class="fw-bold mb-3">

                            <i class="bi bi-shield-lock me-2"></i>

                            Data Login

                        </h6>

                        <div class="row">


                            <!-- Email -->
                            <div class="col-md-6 mb-3">


                                <label class="form-label">
                                    Email
                                </label>


                                <div class="input-group">

                                    <span class="input-group-text">
                                        <i class="bi bi-envelope"></i>
                                    </span>


                                    <input
                                        type="email"
                                        name="email"
                                        class="form-control"
                                        placeholder="email@contoh.com"
                                        required>

                                </div>


                            </div>


                            <!-- Username -->
                            <div class="col-md-6 mb-3">


                                <label class="form-label">
                                    Username
                                </label>


                                <div class="input-group">

                                    <span class="input-group-text">
                                        <i class="bi bi-person-circle"></i>
                                    </span>


                                    <input
                                        type="text"
                                        name="username"
                                        class="form-control"
                                        placeholder="username"
                                        required>

                                </div>


                            </div>





                            <!-- Password -->
                            <div class="col-md-6 mb-3">


                                <label class="form-label">
                                    Password
                                </label>


                                <div class="input-group">

                                    <span class="input-group-text">
                                        <i class="bi bi-key"></i>
                                    </span>


                                    <input
                                        type="password"
                                        name="password"
                                        class="form-control"
                                        placeholder="Password"
                                        required>

                                </div>


                            </div>

                            <!-- Foto -->
                            <div class="mb-3">

                                <label class="form-label">
                                    Foto Profil
                                </label>

                                <div class="input-group">

                                    <span class="input-group-text">
                                        <i class="bi bi-image"></i>
                                    </span>

                                    <input
                                        type="file"
                                        name="foto"
                                        class="form-control"
                                        accept="image/*">

                                </div>

                            </div>

                        </div>

                    </div>

                </div>
                
                <div class="modal-footer">


                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">

                        <i class="bi bi-x-circle me-1"></i>
                        Batal

                    </button>

                    <button
                        type="submit"
                        class="btn btn-success">

                        <i class="bi bi-save me-1"></i>
                        Simpan Akun

                    </button>


                </div>

            </form>


        </div>

    </div>

</div>



<script>

document.getElementById("searchInput").addEventListener("keyup", function(){

    let value = this.value.toLowerCase();

    let rows = document.querySelectorAll("tbody tr");

    rows.forEach(row => {

        row.style.display = row.innerText.toLowerCase().includes(value)
            ? ""
            : "none";

    });

});

// Toggle Password Visibility
function togglePassword(id, button) {

    const input = document.getElementById(id);
    const icon = button.querySelector("i");

    if (input.type === "password") {

        input.type = "text";
        icon.classList.remove("bi-eye");
        icon.classList.add("bi-eye-slash");

    } else {

        input.type = "password";
        icon.classList.remove("bi-eye-slash");
        icon.classList.add("bi-eye");

    }
}

</script>

@endsection