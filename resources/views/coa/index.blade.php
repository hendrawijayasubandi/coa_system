@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Chart of Account') }}</div>

                    <div class="card-body">
                        <div id="alertContainer" class="mb-3"></div>

                        <div class="modal fade" id="coaModal" tabindex="-1" aria-labelledby="coaModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="coaModalLabel">Tambah COA</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="coaForm">
                                            @csrf
                                            <input type="hidden" name="id" id="id">
                                            <div class="mb-3">
                                                <label for="kode" class="form-label">Kode</label>
                                                <input type="text" class="form-control" id="kode" name="kode"
                                                    required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="nama" class="form-label">Nama</label>
                                                <input type="text" class="form-control" id="nama" name="nama"
                                                    required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="kategori_id" class="form-label">Kategori</label>
                                                <select class="form-control" id="kategori_id" name="kategori_id" required>
                                                    <option value="" selected disabled>-- Pilih Kategori --</option>
                                                </select>
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

                        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal"
                            data-bs-target="#coaModal">
                            Tambah COA
                        </button>

                        <table class="table table-striped table-bordered table-responsive" id="coaTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode</th>
                                    <th>Nama</th>
                                    <th>Kategori</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            var table = $('#coaTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('coa.index') }}",
                columns: [{
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        data: 'kode',
                        name: 'kode'
                    },
                    {
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: 'kategori_nama',
                        name: 'kategori.nama'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            function showAlert(message, type) {
                $('#alertContainer').html(`
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `);
            }

            $('#kategori_id').val(null);

            $.ajax({
                url: "{{ route('kategori.list') }}",
                dataType: 'json',
                success: function(kategoriData) {
                    $('#kategori_id').empty().append(
                        '<option value="" selected disabled>-- Pilih Kategori --</option>');
                    kategoriData.forEach(function(k) {
                        $('#kategori_id').append(new Option(k.nama, k.id));
                    });
                }
            });

            $('#coaModal').on('hidden.bs.modal', function() {
                $('#coaForm')[0].reset();
                $('#id').val('');
                $('#coaModalLabel').text('Tambah COA');
                $('#kategori_id').val(null);
            });

            $('#saveBtn').click(function() {
                var formData = $('#coaForm').serialize();
                var id = $('#id').val();
                var url = id ? "{{ route('coa.update', '') }}/" + id : "{{ route('coa.store') }}";
                var method = id ? "PUT" : "POST";

                $('#saveBtn').html(
                        '<span class="spinner-border spinner-border-sm" aria-hidden="true"></span> Menyimpan...'
                        )
                    .prop('disabled', true);

                $.ajax({
                    url: url,
                    type: method,
                    data: formData,
                    success: function(response) {
                        $('#coaModal').modal('hide');
                        $('#coaForm')[0].reset();
                        $('#kategori_id').val(null);
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

            $('#coaTable').on('click', '.edit', function() {
                var id = $(this).data('id');
                var url = "{{ route('coa.index') }}/" + id + "/edit";

                $.ajax({
                    url: url,
                    type: "GET",
                    success: function(data) {
                        $('#id').val(data.id);
                        $('#kode').val(data.kode);
                        $('#nama').val(data.nama);
                        $('#kategori_id').val(data.kategori_id);
                        $('#coaModalLabel').text('Edit COA');
                        $('#coaModal').modal('show');
                    },
                    error: function(xhr) {
                        showAlert('Gagal mengambil data. Error: ' + xhr.status, 'danger');
                    }
                });
            });

            $('#coaTable').on('click', '.delete', function() {
                var id = $(this).data('id');
                var nama = $(this).closest('tr').find('td:eq(2)').text().trim();

                $('#confirmDeleteModal .modal-body').text(
                    `Apakah Anda yakin ingin menghapus COA "${nama}"?`);
                $('#confirmDeleteBtn').data('id', id);
                $('#confirmDeleteModal').modal('show');
            });

            $('#confirmDeleteBtn').click(function() {
                var id = $(this).data('id');

                $('#confirmDeleteBtn').html(
                        '<span class="spinner-border spinner-border-sm" aria-hidden="true"></span> Menghapus...'
                        )
                    .prop('disabled', true);

                $.ajax({
                    url: "{{ route('coa.destroy', '') }}/" + id,
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
        });
    </script>
@endsection
