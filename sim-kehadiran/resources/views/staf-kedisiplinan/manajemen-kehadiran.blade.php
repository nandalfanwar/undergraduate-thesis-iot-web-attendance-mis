@extends('layouts.app')

@section('content')

@if(session('success'))

    <div class="alert alert-success alert-dismissible fade show">

    {{ session('success') }}

    <button
    class="btn-close"
    data-bs-dismiss="alert">
    </button>

    </div>

@endif

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">
        Manajemen Kehadiran
    </h3>

    <button
        class="btn btn-success"
        data-bs-toggle="modal"
        data-bs-target="#tambahModal">

        <i class="ph ph-plus"></i>
        Tambah Kehadiran

    </button>

</div>

<!-- filter -->
<div class="card mb-3">
    <div class="card-body">

        <form method="GET">

            <div class="row g-3 align-items-end">

                <div class="col-md-4">
                    <label class="form-label">Cari Nama</label>

                    <input
                        type="text"
                        class="form-control"
                        name="nama"
                        value="{{ request('nama') }}"
                        placeholder="Masukkan nama guru/tendik">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Status</label>

                    <select
                        name="status"
                        class="form-select">

                        <option value="">
                            Semua Status
                        </option>

                        @foreach($statusList as $item)

                            <option
                                value="{{ $item->id }}"
                                {{ request('status') == $item->id ? 'selected' : '' }}>

                                {{ $item->status_kehadiran_label }}

                            </option>

                        @endforeach

                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Tanggal</label>

                    <input
                        type="date"
                        class="form-control"
                        name="tanggal"
                        value="{{ request('tanggal') }}">
                </div>

                <div class="col-md-2 d-flex gap-2">

                    <button class="btn btn-success flex-fill">

                        Filter

                    </button>

                    <a
                        href="{{ route('staf-kedisiplinan.manajemen-kehadiran') }}"
                        class="btn btn-secondary">

                        Reset

                    </a>

                </div>

            </div>

        </form>

    </div>
</div>

<!-- tabel -->
<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nama</th>
                        <th>Tanggal</th>
                        <th>Masuk</th>
                        <th>Keluar</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($kehadiranTampil as $data)
                    <tr>
                        <td>{{ $data->nama }}</td>
                        <td>{{ \Carbon\Carbon::parse($data->tanggal)->translatedFormat('d F Y') }}</td>
                        <td>{{ $data->waktu_masuk }}</td>
                        <td>{{ $data->waktu_keluar }}</td>
                        <td>
                            <span class="badge
                                @if($data->status=='Hadir') bg-success
                                @elseif($data->status=='Alpa') bg-danger
                                @elseif($data->status=='Sakit') bg-warning text-dark
                                @elseif($data->status=='Izin') bg-primary
                                @elseif($data->status=='Cuti') bg-info text-dark
                                @elseif($data->status=='Tugas') bg-secondary
                                @elseif($data->status=='Terlambat') bg-warning text-white
                                @else bg-secondary @endif">
                                {{ $data->status }}
                            </span>
                        </td>
                        <td>{{ $data->keterangan }}</td>
                        <td>
                            <button
                                type="button"
                                class="btn btn-warning btn-sm btn-edit"
                                data-bs-toggle="modal"
                                data-bs-target="#editModal"
                                data-id="{{ $data->id ?? '' }}"
                                data-user-id="{{ $data->user_id }}"
                                data-nama="{{ $data->nama }}"
                                data-nuptk="{{ $data->nuptk }}"
                                data-tanggal="{{ $data->tanggal }}"
                                data-status="{{ $data->status_kehadiran_id ?? '' }}"
                                data-masuk="{{ $data->waktu_masuk != '-' ? $data->waktu_masuk : '' }}"
                                data-keluar="{{ $data->waktu_keluar != '-' ? $data->waktu_keluar : '' }}"
                                data-keterangan="{{ $data->keterangan != '-' ? $data->keterangan : '' }}"
                                data-bukti="{{ $data->bukti ?? '' }}"
                                data-tgl-upload-bukti="{{ $data->tgl_upload_bukti ? \Carbon\Carbon::parse($data->tgl_upload_bukti)->translatedFormat('d F Y - H:i') : '' }}">
                                
                                <i class="ph ph-pencil"></i>
                            </button>

                            @if(!$data->is_virtual && $data->id)
                                <form
                                    action="{{ route('staf-kedisiplinan.manajemen-kehadiran.destroy', $data->id) }}"
                                    method="POST"
                                    class="d-inline form-delete">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="ph ph-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">Belum ada data guru atau tendik.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        </div>
</div>

<!-- modal tambah -->
<div class="modal fade" id="tambahModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('staf-kedisiplinan.manajemen-kehadiran.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="modal-header">
                    <div>
                        <h5 class="modal-title mb-1">Tambah Manajemen Kehadiran</h5>
                        <small class="text-muted">Tambahkan data kehadiran guru atau tenaga kependidikan</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3"><i class="ph ph-user me-2"></i> Data Pegawai</h6>
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Guru / Tendik</label>
                                <select class="form-select" name="user_id" id="userSelect" required>
                                    <option value="">Pilih Guru / Tendik</option>
                                    @foreach($guru as $item)
                                        <option value="{{ $item->id }}" data-nuptk="{{ $item->nuptk }}">
                                            {{ $item->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">NUPTK</label>
                                <input type="text" id="nuptk" class="form-control" readonly>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h6 class="fw-bold mb-3"><i class="ph ph-calendar-check me-2"></i> Data Kehadiran</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status Kehadiran</label>
                                <select name="status_kehadiran_id" id="statusSelect" class="form-select" required>
                                    @foreach($statusList as $item)
                                        <option value="{{ $item->id }}">
                                            {{ $item->status_kehadiran_label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jam Masuk</label>
                                <input type="time" class="form-control" name="waktu_masuk" id="waktu_masuk">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jam Keluar</label>
                                <input type="time" class="form-control" name="waktu_keluar" id="waktu_keluar">
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h6 class="fw-bold mb-3"><i class="ph ph-file-arrow-up me-2"></i> Bukti Kehadiran (Opsional)</h6>
                        <input type="file" class="form-control" name="bukti" accept="image/*,.pdf">
                        <small class="text-muted">Format: JPG, PNG, PDF (Maks 2MB).</small>
                    </div>

                    <hr>

                    <div>
                        <h6 class="fw-bold mb-3"><i class="ph ph-note-pencil me-2"></i> Keterangan</h6>
                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Tambahkan keterangan jika diperlukan..."></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan Kehadiran</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="formEdit" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <input type="hidden" name="_method" id="formMethod" value="PUT">
                <input type="hidden" name="user_id" id="edit_user_id">

                <!-- HEADER -->
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title mb-1">Edit Data Kehadiran</h5>
                        <small class="text-muted">Perbarui informasi kehadiran</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">
                    <!-- DATA PEGAWAI -->
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3">
                            <i class="ph ph-user me-2"></i>Data Pegawai
                        </h6>
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Nama</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ph ph-user"></i></span>
                                    <input type="text" class="form-control" id="edit_nama" readonly>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">NUPTK</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ph ph-identification-card"></i></span>
                                    <input type="text" class="form-control" id="edit_nuptk" readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- DATA KEHADIRAN -->
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3">
                            <i class="ph ph-calendar-check me-2"></i>Data Kehadiran
                        </h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal</label>
                                <input type="date" class="form-control" name="tanggal" id="edit_tanggal" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status Kehadiran</label>
                                <select class="form-select" name="status_kehadiran_id" id="edit_status" required>
                                    @foreach($statusList as $item)
                                        <option value="{{ $item->id }}">{{ $item->status_kehadiran_label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row" id="editJamSection">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jam Masuk</label>
                                <input type="time" class="form-control" name="waktu_masuk" id="edit_waktu_masuk">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jam Keluar</label>
                                <input type="time" class="form-control" name="waktu_keluar" id="edit_waktu_keluar">
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h6 class="fw-bold mb-3">
                            <i class="ph ph-file-arrow-up me-2"></i>Bukti Kehadiran
                        </h6>
                        
                        <!-- Alert jika bukti sudah terisi (terkunci) -->
                        <div id="alert_bukti_locked" class="alert alert-warning d-none align-items-center mb-3" role="alert">
                            <i class="ph ph-warning-circle fs-5 me-2"></i>
                            <div>Bukti sudah diunggah dan tidak dapat diubah kembali.</div>
                        </div>

                        <!-- Container Preview Bukti jika Sudah Diupload -->
                        <div id="preview_bukti_container" class="mb-3 d-none">
                            <div class="card bg-light border-0 p-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <span class="badge bg-success mb-1">
                                            <i class="ph ph-check-circle me-1"></i>Bukti Tersimpan
                                        </span>
                                        <small class="text-muted d-block" id="label_tgl_upload">
                                            <!-- Tanggal upload dari JS -->
                                        </small>
                                    </div>
                                    <a href="#" id="link_preview_bukti" target="_blank" class="btn btn-sm btn-primary">
                                        <i class="ph ph-eye me-1"></i> Lihat / Download Bukti
                                    </a>
                                </div>
                                <!-- Elemen ini opsional jika berupa file gambar -->
                                <div id="image_preview_box" class="mt-2 text-center d-none">
                                    <img id="img_bukti_preview" src="" class="img-fluid rounded border shadow-sm" style="max-height: 200px;">
                                </div>
                            </div>
                        </div>

                        <!-- Input File (Di-hide / Disabled via JS jika bukti sudah ada) -->
                        <div id="input_bukti_container">
                            <label class="form-label">Upload Bukti File / Foto</label>
                            <input type="file" class="form-control" name="bukti" id="edit_bukti" accept="image/*,.pdf">
                            <small class="text-muted">Format: JPG, PNG, PDF (Maks 2MB). Hanya dapat diunggah 1 kali.</small>
                        </div>
                    </div>

                    <hr>

                    <!-- KETERANGAN -->
                    <div>
                        <h6 class="fw-bold mb-3">
                            <i class="ph ph-note-pencil me-2"></i>Keterangan
                        </h6>
                        <textarea class="form-control" rows="3" name="keterangan" id="edit_keterangan" placeholder=""></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ph ph-x-circle me-1"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="ph ph-floppy-disk me-1"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // 1. SweetAlert Hapus Data
    document.querySelectorAll('.form-delete').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Hapus Data?',
                text: 'Data yang dihapus tidak dapat dikembalikan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // 2. Auto-fill NUPTK saat Pilih Guru (Modal Tambah)
    const userSelect = document.getElementById('userSelect');
    if (userSelect) {
        userSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const nuptk = selectedOption.getAttribute('data-nuptk') || '-';
            const nuptkInput = document.getElementById('nuptk');
            if (nuptkInput) nuptkInput.value = nuptk;
        });
    }

    // 3. Helper Function: Toggle Input Waktu berdasarkan Status Kehadiran
    function toggleWaktuInput(statusSelectId, masukId, keluarId) {
        const statusSelect = document.getElementById(statusSelectId);
        const inputMasuk = document.getElementById(masukId);
        const inputKeluar = document.getElementById(keluarId);

        if (!statusSelect || !inputMasuk || !inputKeluar) return;

        function updateState() {
            const selectedText = statusSelect.options[statusSelect.selectedIndex]?.text.toLowerCase() || '';
            const selectedValue = statusSelect.value;

            // Nonaktifkan jam jika status bukan "Hadir" (berdasarkan kata atau ID)
            const isHadir = selectedText.includes('hadir') || selectedValue == '1';

            if (isHadir) {
                inputMasuk.disabled = false;
                inputKeluar.disabled = false;
            } else {
                inputMasuk.value = '';
                inputKeluar.value = '';
                inputMasuk.disabled = true;
                inputKeluar.disabled = true;
            }
        }

        statusSelect.addEventListener('change', updateState);
        updateState(); // Jalankan sekali saat inisialisasi
    }

    // Inisialisasi Toggle Waktu untuk Modal Tambah & Edit
    toggleWaktuInput('statusSelect', 'waktu_masuk', 'waktu_keluar');
    toggleWaktuInput('edit_status', 'edit_waktu_masuk', 'edit_waktu_keluar');

    // 4. Populasi Data pada Modal Edit
    // document.querySelectorAll('.btn-edit').forEach(button => {
    //     button.addEventListener('click', function () {
    //         const data = this.dataset;

    //         // Action Form
    //         const formEdit = document.getElementById('formEdit');
    //         if (formEdit) formEdit.action = '/staf-kedisiplinan/manajemen-kehadiran/' + data.id;

    //         // Set Input Hidden User ID (jika ada di HTML)
    //         const editUserId = document.getElementById('edit_user_id');
    //         if (editUserId) editUserId.value = data.userId || '';

    //         // Set Data Readonly
    //         const editNama = document.getElementById('edit_nama');
    //         const editNuptk = document.getElementById('edit_nuptk');
    //         if (editNama) editNama.value = data.nama || '-';
    //         if (editNuptk) editNuptk.value = data.nuptk || '-';

    //         // Set Data Form
    //         const editTanggal = document.getElementById('edit_tanggal');
    //         const editStatus = document.getElementById('edit_status');
    //         const editMasuk = document.getElementById('edit_waktu_masuk');
    //         const editKeluar = document.getElementById('edit_waktu_keluar');
    //         const editKeterangan = document.getElementById('edit_keterangan');

    //         if (editTanggal) editTanggal.value = data.tanggal || '';
    //         if (editKeterangan) editKeterangan.value = data.keterangan || '';

    //         if (editStatus) {
    //             editStatus.value = data.status || '';
    //             // Trigger event change agar logika toggle waktu otomatis menyesuaikan
    //             editStatus.dispatchEvent(new Event('change'));
    //         }

    //         // Masukkan nilai jam setelah event status dijalankan
    //         if (editMasuk && !editMasuk.disabled) editMasuk.value = data.masuk || '';
    //         if (editKeluar && !editKeluar.disabled) editKeluar.value = data.keluar || '';

    //         // 5. Penanganan Field Bukti & Lock System
    //         const inputBukti = document.getElementById('edit_bukti');
    //         const inputBuktiContainer = document.getElementById('input_bukti_container');
    //         const labelTglUpload = document.getElementById('label_tgl_upload');
    //         const alertLocked = document.getElementById('alert_bukti_locked');
    //         const previewContainer = document.getElementById('preview_bukti_container');
    //         const linkPreview = document.getElementById('link_preview_bukti');

    //         if (inputBukti) {
    //             // Reset kondisi awal
    //             inputBukti.value = '';
    //             inputBukti.disabled = false;
    //             if (inputBuktiContainer) inputBuktiContainer.classList.remove('d-none');
                
    //             if (alertLocked) {
    //                 alertLocked.classList.add('d-none');
    //                 alertLocked.classList.remove('d-flex');
    //             }
    //             if (previewContainer) previewContainer.classList.add('d-none');
    //             if (labelTglUpload) labelTglUpload.innerHTML = 'Diupload pada: -';

    //             // Jika bukti sudah pernah diupload di DB
    //             if (data.bukti && data.bukti.trim() !== '') {
    //                 // Lock & sembunyikan input upload
    //                 inputBukti.disabled = true;
    //                 if (inputBuktiContainer) inputBuktiContainer.classList.add('d-none');

    //                 // Tampilkan alert peringatan
    //                 if (alertLocked) {
    //                     alertLocked.classList.remove('d-none');
    //                     alertLocked.classList.add('d-flex');
    //                 }

    //                 // Tampilkan tanggal upload
    //                 if (labelTglUpload) {
    //                     const tglUpload = data.tglUploadBukti || '-';
    //                     labelTglUpload.innerHTML = `Diupload pada: <strong>${tglUpload}</strong>`;
    //                 }

    //                 // Tampilkan tombol/link preview
    //                 if (previewContainer && linkPreview) {
    //                     previewContainer.classList.remove('d-none');
    //                     linkPreview.href = `/storage/${data.bukti}`;
    //                 }
    //             }
    //         }
    //     });
    // });

    document.querySelectorAll('.btn-edit').forEach(button => {
    button.addEventListener('click', function () {
        const data = this.dataset;

        // // 1. Set Action Form

        const formEdit = document.getElementById('formEdit');
        const methodInput = document.getElementById('formMethod'); // Ambil input hidden method
        const editUserId = document.getElementById('edit_user_id'); // Ambil input hidden user_id
        // 1. Set Action Form
        if (formEdit) {
            // Set User ID tersembunyi
            if (editUserId) editUserId.value = data.userId;

            // Jika data->id kosong (virtual / belum ada di database), arahkan ke STORE (POST)
            if (data.id && data.id.trim() !== '') {
                formEdit.action = '/staf-kedisiplinan/manajemen-kehadiran/' + data.id;
                if(methodInput) methodInput.value = 'PUT'; // Mode Update
            } else {
                formEdit.action = '/staf-kedisiplinan/manajemen-kehadiran'; 
                if(methodInput) methodInput.value = 'POST'; // Mode Insert Baru
            }
        }

        // 2. Set Data Form Dasar
        const editNama = document.getElementById('edit_nama');
        const editNuptk = document.getElementById('edit_nuptk');
        const editTanggal = document.getElementById('edit_tanggal');
        const editStatus = document.getElementById('edit_status');
        const editMasuk = document.getElementById('edit_waktu_masuk');
        const editKeluar = document.getElementById('edit_waktu_keluar');
        const editKeterangan = document.getElementById('edit_keterangan');

        if (editNama) editNama.value = data.nama || '-';
        if (editNuptk) editNuptk.value = data.nuptk || '-';
        if (editTanggal) editTanggal.value = data.tanggal || '';
        if (editKeterangan) editKeterangan.value = data.keterangan || '';

        if (editStatus) {
            editStatus.value = data.status || '';
            editStatus.dispatchEvent(new Event('change'));
        }

        if (editMasuk && !editMasuk.disabled) editMasuk.value = data.masuk || '';
        if (editKeluar && !editKeluar.disabled) editKeluar.value = data.keluar || '';

        // 3. Logika Menampilkan Bukti & Tanggal Upload
        const alertLocked = document.getElementById('alert_bukti_locked');
        const previewContainer = document.getElementById('preview_bukti_container');
        const labelTglUpload = document.getElementById('label_tgl_upload');
        const linkPreview = document.getElementById('link_preview_bukti');
        const imagePreviewBox = document.getElementById('image_preview_box');
        const imgBuktiPreview = document.getElementById('img_bukti_preview');
        const inputBuktiContainer = document.getElementById('input_bukti_container');
        const inputBukti = document.getElementById('edit_bukti');

        // Reset Tampilan Awal (Kondisi belum upload)
        if (inputBukti) {
            inputBukti.value = '';
            inputBukti.disabled = false;
        }
        if (inputBuktiContainer) inputBuktiContainer.classList.remove('d-none');
        if (alertLocked) alertLocked.classList.add('d-none');
        if (previewContainer) previewContainer.classList.add('d-none');
        if (imagePreviewBox) imagePreviewBox.classList.add('d-none');

        // Cek jika kolom bukti di DB tidak kosong
        if (data.bukti && data.bukti.trim() !== '') {
            // Sembunyikan dan kunci input file
            if (inputBuktiContainer) inputBuktiContainer.classList.add('d-none');
            if (inputBukti) inputBukti.disabled = true;

            // Tampilkan alert bahwa data dikunci
            if (alertLocked) {
                alertLocked.classList.remove('d-none');
                alertLocked.classList.add('d-flex');
            }

            // Set Tanggal Upload
            if (labelTglUpload) {
                const tglFormatted = data.tglUploadBukti || 'Tanggal tidak dicatat';
                labelTglUpload.innerHTML = `Diupload pada: <strong>${tglFormatted}</strong>`;
            }

            // Set URL File
            const fileUrl = `/storage/${data.bukti}`;
            if (linkPreview) linkPreview.href = fileUrl;

            // Jika file berupa Gambar (JPG, PNG, GIF, WEBP), tampilkan gambarnya langsung
            const ext = data.bukti.split('.').pop().toLowerCase();
            if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
                if (imgBuktiPreview) imgBuktiPreview.src = fileUrl;
                if (imagePreviewBox) imagePreviewBox.classList.remove('d-none');
            }

            // Tampilkan container preview bukti
            if (previewContainer) previewContainer.classList.remove('d-none');
        }
    });
});
});
</script>

@endsection 