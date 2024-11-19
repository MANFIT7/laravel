<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Manajemen Penjualan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Manajemen Penjualan</h2>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#saleModal">
                Tambah Penjualan Baru
            </button>
        </div>

        <div class="card">
            <div class="card-body">
                <table id="salesTable" class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nomor Faktur</th>
                            <th>Tanggal</th>
                            <th>Pelanggan</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal untuk Tambah/Edit Penjualan -->
    <div class="modal fade" id="saleModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Penjualan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="saleForm">
                        <input type="hidden" id="saleId">
                        <div class="mb-3">
                            <label class="form-label">Nomor Faktur</label>
                            <input type="text" class="form-control" id="invoice_number" name="invoice_number" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tanggal Penjualan</label>
                            <input type="date" class="form-control" id="sale_date" name="sale_date" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Pelanggan</label>
                            <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Total</label>
                            <input type="number" step="1" class="form-control" id="total_amount" name="total_amount" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea class="form-control" id="notes" name="notes"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="pending">Menunggu</option>
                                <option value="completed">Selesai</option>
                                <option value="cancelled">Dibatalkan</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" id="saveSale">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let table = $('#salesTable').DataTable({
            ajax: {
                url: '{{ route("sales.list") }}',
                dataSrc: 'data'
            },
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            },
            columns: [
                { data: 'invoice_number' },
                { data: 'sale_date' },
                { data: 'customer_name' },
                { 
                    data: 'total_amount',
                    render: function(data) {
                        return new Intl.NumberFormat('id-ID', {
                            style: 'currency',
                            currency: 'IDR',
                            minimumFractionDigits: 0,
                            maximumFractionDigits: 0
                        }).format(data);
                    }
                },
                { 
                    data: 'status',
                    render: function(data) {
                        const badges = {
                            pending: 'bg-warning',
                            completed: 'bg-success',
                            cancelled: 'bg-danger'
                        };
                        const status = {
                            pending: 'Menunggu',
                            completed: 'Selesai',
                            cancelled: 'Dibatalkan'
                        };
                        return `<span class="badge ${badges[data]}">${status[data]}</span>`;
                    }
                },
                {
                    data: null,
                    render: function(data) {
                        return `
                            <button class="btn btn-sm btn-info edit-sale" data-id="${data.id}">Edit</button>
                            <button class="btn btn-sm btn-danger delete-sale" data-id="${data.id}">Hapus</button>
                        `;
                    }
                }
            ]
        });

        $('#saleModal').on('hidden.bs.modal', function() {
            $('#saleForm')[0].reset();
            $('#saleId').val('');
        });

        $('#saveSale').click(function() {
            let formData = {
                invoice_number: $('#invoice_number').val(),
                sale_date: $('#sale_date').val(),
                customer_name: $('#customer_name').val(),
                total_amount: $('#total_amount').val(),
                notes: $('#notes').val(),
                status: $('#status').val()
            };

            let id = $('#saleId').val();
            let url = id ? `/sales/${id}` : '/sales';
            let method = id ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                method: method,
                data: formData,
                success: function(response) {
                    $('#saleModal').modal('hide');
                    table.ajax.reload();
                    alert(response.message);
                },
                error: function(xhr) {
                    let errors = xhr.responseJSON.errors;
                    let errorMessage = Object.values(errors).flat().join('\n');
                    alert('Error: ' + errorMessage);
                }
            });
        });

        $('#salesTable').on('click', '.edit-sale', function() {
            let id = $(this).data('id');
            $.get(`/sales/${id}`, function(response) {
                let sale = response.data;
                $('#saleId').val(sale.id);
                $('#invoice_number').val(sale.invoice_number);
                $('#sale_date').val(sale.sale_date);
                $('#customer_name').val(sale.customer_name);
                $('#total_amount').val(sale.total_amount);
                $('#notes').val(sale.notes);
                $('#status').val(sale.status);
                $('#saleModal').modal('show');
            });
        });

        $('#salesTable').on('click', '.delete-sale', function() {
            if (confirm('Apakah Anda yakin ingin menghapus penjualan ini?')) {
                let id = $(this).data('id');
                $.ajax({
                    url: `/sales/${id}`,
                    method: 'DELETE',
                    success: function(response) {
                        table.ajax.reload();
                        alert(response.message);
                    }
                });
            }
        });
    </script>
</body>
</html>
