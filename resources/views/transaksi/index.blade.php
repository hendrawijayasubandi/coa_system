@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Transaksi') }}</div>

                    <div class="card-body">
                        <!-- Alert -->
                        <div id="alertContainer" class="mb-3"></div>

                        <!-- Modal -->
                        <div class="modal fade" id="transaksiModal" tabindex="-1" aria-labelledby="transaksiModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="transaksiModalLabel">Tambah Transaksi</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="transaksiForm">
                                            @csrf
                                            <input type="hidden" name="id" id="id">
                                            <div class="mb-3">
                                                <label for="tanggal" class="form-label">Tanggal</label>
                                                <input type="text" class="form-control" id="tanggal" name="tanggal"
                                                    placeholder="-- Pilih Tanggal --" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="coa_kode" class="form-label">COA Kode</label>
                                                <select class="form-control" id="coa_kode" name="coa_kode" required>
                                                    <option value="" selected disabled>-- Pilih COA Kode --</option>
                                                    @foreach ($coa as $item)
                                                        <option value="{{ $item->kode }}">{{ $item->kode }} -
                                                            {{ $item->nama }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="desc" class="form-label">Deskripsi</label>
                                                <input type="text" class="form-control" id="desc" name="desc"
                                                    required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="nominal" class="form-label">Nominal</label>
                                                <input type="text" class="form-control" id="nominal" name="nominal"
                                                    oninput="formatRupiah(this)" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="status_transaksi" class="form-label">Status Transaksi</label>
                                                <select class="form-control" id="status_transaksi" name="status_transaksi"
                                                    required>
                                                    <option value="" selected disabled>-- Pilih Status Transaksi --
                                                    </option>
                                                    <option value="debit">Debit</option>
                                                    <option value="credit">Credit</option>
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
                                        Apakah Anda yakin ingin menghapus transaksi ini?
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
                            data-bs-target="#transaksiModal">
                            Tambah Transaksi
                        </button>

                        <!-- Tabel Transaksi -->
                        <table class="table table-striped table-bordered table-responsive" id="transaksiTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>COA Kode</th>
                                    <th>COA Nama</th>
                                    <th>Deskripsi</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
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

    <!-- CSS Bootstrap Datepicker -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css"
        rel="stylesheet">

    <!-- JS Bootstrap Datepicker -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script>
        $('#tanggal').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
            orientation: 'bottom'
        });

        $('#nominal').on('input', function() {
            this.value = formatRupiah(this.value);
        });

        function formatRupiah(input) {
            let angka = input.value.replace(/[^,\d]/g, '');
            if (!angka) {
                input.value = '';
                return;
            }

            let ribuan = angka.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            input.value = ribuan;
        }

        function toNumber(rupiah) {
            return rupiah.replace(/[^0-9]/g, '');
        }

        function showAlert(message, type) {
            $('#alertContainer').html(`
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        }

        $(document).ready(function() {
            var table = $('#transaksiTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('transaksi.index') }}",
                columns: [{
                        data: null,
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        data: 'tanggal',
                        render: function(data) {
                            return formatTanggal(data);
                        }
                    },
                    {
                        data: 'coa_kode'
                    },
                    {
                        data: 'coa_nama'
                    },
                    {
                        data: 'desc'
                    },
                    {
                        data: 'debit',
                        render: function(data) {
                            return formatRupiah(data ? data.toString() : '0');
                        }
                    },
                    {
                        data: 'credit',
                        render: function(data) {
                            return formatRupiah(data ? data.toString() : '0');
                        }
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
            });

            function formatTanggal(dateString) {
                const date = new Date(dateString);
                const day = date.getDate();
                const month = ('0' + (date.getMonth() + 1)).slice(-2);
                const year = date.getFullYear();
                return `${day} ${month} ${year}`;
            }

            function formatRupiah(angka) {
                let number = parseInt(angka, 10);
                return "Rp " + number.toLocaleString('id-ID');
            }

            $('#saveBtn').click(function() {
                let formData = $('#transaksiForm').serializeArray();
                let id = $('#id').val();
                let url = id ? "{{ route('transaksi.update', '') }}/" + id :
                    "{{ route('transaksi.store') }}";
                let method = id ? "PUT" : "POST";

                formData.forEach(function(field) {
                    if (field.name === 'nominal') {
                        field.value = toNumber(field.value);
                    }
                });

                $('#saveBtn').html('<span class="spinner-border spinner-border-sm"></span> Menyimpan...')
                    .prop('disabled', true);

                $.ajax({
                    url: url,
                    type: method,
                    data: formData,
                    success: function(response) {
                        $('#transaksiModal').modal('hide');
                        setTimeout(() => {
                            $('.modal-backdrop').remove();
                            $('body').removeClass('modal-open').css('overflow', 'auto');
                        }, 300);
                        $('#transaksiForm')[0].reset();
                        $('#transaksiTable').DataTable().ajax.reload(null, false);
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
            $('#transaksiTable').on('click', '.edit', function() {
                let id = $(this).data('id');

                $.get("{{ url('transaksi') }}/" + id + "/edit", function(data) {
                    $('#id').val(data.id);
                    $('#tanggal').val(data.tanggal);
                    $('#coa_kode').val(data.coa_kode);
                    $('#desc').val(data.desc);
                    $('#nominal').val(formatNominal(data.nominal.toString()));
                    $('#status_transaksi').val(data.status_transaksi);

                    $('#transaksiModalLabel').text('Edit Transaksi');
                    $('#transaksiModal').modal('show');
                }).fail(function(xhr) {
                    showAlert('Gagal mengambil data. Error: ' + xhr.status, 'danger');
                });
            });

            function formatNominal(angka) {
                angka = angka ? angka.toString().replace(/\D/g, '') : '0';
                return new Intl.NumberFormat('id-ID').format(angka);
            }

            $('#transaksiTable').on('click', '.delete', function() {
                let id = $(this).data('id');
                let desc = $(this).closest('tr').find('td:eq(4)').text().trim();

                $('#confirmDeleteModal .modal-body').text(
                    `Apakah Anda yakin ingin menghapus transaksi "${desc}"?`);
                $('#confirmDeleteBtn').data('id', id);
                $('#confirmDeleteModal').modal('show');
            });

            $('#confirmDeleteBtn').off('click').on('click', function() {
                let id = $(this).data('id');

                $(this).html('<span class="spinner-border spinner-border-sm"></span> Menghapus...')
                    .prop('disabled', true);

                $.ajax({
                    url: "{{ url('transaksi') }}/" + id,
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        _method: "DELETE"
                    },
                    success: function(response) {
                        $('#confirmDeleteModal').modal('hide');
                        $('#transaksiTable').DataTable().ajax.reload(null, false);
                        showAlert(response.success, 'success');
                    },
                    error: function(xhr) {
                        showAlert('Gagal menghapus transaksi. Error: ' + xhr.status, 'danger');
                    },
                    complete: function() {
                        $('#confirmDeleteBtn').html('Hapus').prop('disabled', false);
                    }
                });
            });

            $('#transaksiModal').on('hidden.bs.modal', function() {
                $('#transaksiForm')[0].reset();
                $('#id').val('');
                $('#transaksiModalLabel').text('Tambah Transaksi');
                $('#coa_kode').prop('selectedIndex', 0);
                $('#status_transaksi').prop('selectedIndex', 0);
                $('#nominal').val('');

                setTimeout(() => {
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open').css('overflow', 'auto');
                }, 300);
            });
        });
    </script>
@endsection
