@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Kategori') }}</div>

                    <div class="card-body">
                        <!-- Alert -->
                        <div id="alertContainer" class="mb-3"></div>

                        <!-- Modal -->
                        <div class="modal fade" id="kategoriModal" tabindex="-1" aria-labelledby="kategoriModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="kategoriModalLabel">Tambah Kategori</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="kategoriForm">
                                            @csrf
                                            <input type="hidden" name="id" id="id">
                                            <div class="mb-3">
                                                <label for="nama" class="form-label">Nama Kategori</label>
                                                <input type="text" class="form-control" id="nama" name="nama"
                                                    required>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Tutup</button>
                                        <button type="button" class="btn btn-primary" id="saveBtn">Simpan</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="confirmDeleteModal" tabindex="-1"
                            aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="confirmDeleteModalLabel">Konfirmasi Hapus</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Apakah Anda yakin ingin menghapus kategori ini?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Batal</button>
                                        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Hapus</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Button Tambah Data -->
                        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal"
                            data-bs-target="#kategoriModal">
                            Tambah Kategori
                        </button>

                        <!-- Tabel Kategori -->
                        <table class="table table-striped table-bordered table-responsive" id="kategoriTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery dan Datatable -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script>
        function showAlert(message, type) {
            $('#alertContainer').empty();

            var alert = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;

            $('#alertContainer').html(alert);
        }

        function showAlert(message, type) {
            $('#alertContainer').empty();

            var alert = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;

            $('#alertContainer').html(alert);
        }

        $(document).ready(function() {
            var table = $('#kategoriTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('kategori.index') }}",
                columns: [{
                        data: null,
                        name: 'index',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
            });

            function showAlert(message, type) {
                $('#alertContainer').html(`
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `);
            }

            $('#saveBtn').click(function() {
                var formData = $('#kategoriForm').serialize();
                var id = $('#id').val();
                var url = id ? "{{ route('kategori.update', '') }}/" + id : "{{ route('kategori.store') }}";
                var method = id ? "PUT" : "POST";

                $('#saveBtn').html(
                    '<span class="spinner-border spinner-border-sm" aria-hidden="true"></span> Menyimpan...'
                    ).prop('disabled', true);

                $.ajax({
                    url: url,
                    type: method,
                    data: formData,
                    success: function(response) {
                        $('#kategoriModal').modal('hide');
                        $('#kategoriForm')[0].reset();
                        table.ajax.reload();
                        showAlert(response.success, 'success');
                    },
                    error: function() {
                        showAlert('Terjadi kesalahan. Silakan coba lagi.', 'danger');
                    },
                    complete: function() {
                        $('#saveBtn').html('Simpan').prop('disabled', false);
                    }
                });
            });

            $('#kategoriTable').on('click', '.edit', function() {
                var id = $(this).data('id');
                var url = "{{ route('kategori.index') }}/" + id + "/edit";

                $.ajax({
                    url: url,
                    type: "GET",
                    success: function(data) {
                        $('#id').val(data.id);
                        $('#nama').val(data.nama);
                        $('#kategoriModalLabel').text('Edit Kategori');
                        $('#kategoriModal').modal('show');
                    },
                    error: function(xhr) {
                        showAlert('Gagal mengambil data. Error: ' + xhr.status, 'danger');
                    }
                });
            });

            $('#kategoriTable').on('click', '.delete', function() {
                var id = $(this).data('id');
                var nama = $(this).data('nama');

                if (!nama) {
                    nama = $(this).closest('tr').find('td:eq(1)').text()
                        .trim();
                }

                $('#confirmDeleteModal .modal-body').text(
                    `Apakah Anda yakin ingin menghapus kategori "${nama}"?`);
                $('#confirmDeleteBtn').data('id', id);
                $('#confirmDeleteModal').modal('show');
            });

            $('#confirmDeleteBtn').click(function() {
                var id = $(this).data('id');

                $('#confirmDeleteBtn').html(
                    '<span class="spinner-border spinner-border-sm" aria-hidden="true"></span> Menghapus...'
                    ).prop('disabled', true);

                $.ajax({
                    url: "{{ route('kategori.destroy', '') }}/" + id,
                    type: "DELETE",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        table.ajax.reload();
                        $('#confirmDeleteModal').modal('hide');
                        showAlert(response.success, 'success');
                    },
                    error: function() {
                        showAlert('Terjadi kesalahan. Silakan coba lagi.', 'danger');
                    },
                    complete: function() {
                        $('#confirmDeleteBtn').html('Hapus').prop('disabled', false);
                    }
                });
            });

            $('#kategoriModal').on('hidden.bs.modal', function() {
                $('#kategoriForm')[0].reset();
                $('#id').val('');
                $('#kategoriModalLabel').text('Tambah Kategori');
            });
        });
    </script>
@endsection
