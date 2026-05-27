@extends('layouts.admin.master')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Import Layanan SMM</h1>
    
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item">SMM</li>
        <li class="breadcrumb-item active">Import</li>
    </ol>
    
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Select API Provider Card -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-plug me-1"></i>
            Pilih API Provider
        </div>
        <div class="card-body">
            <form action="{{ route('admin.smm.import.fetch') }}" method="POST" class="row align-items-end">
                @csrf
                <div class="col-md-6 mb-3">
                    <label class="form-label" for="apiProvider">API Provider SMM</label>
                    <select class="form-select" id="apiProvider" name="id_api_smm" required>
                        <option value="">-- Pilih Provider --</option>
                        @foreach($apis as $api)
                            <option value="{{ $api->id }}" {{ (isset($selectedApiId) && $selectedApiId == $api->id) ? 'selected' : '' }}>
                                {{ $api->name }} ({{ $api->url }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-sync-alt me-1"></i> Tarik Layanan</button>
                </div>
            </form>
        </div>
    </div>

    @if(isset($fetchedServices) && count($fetchedServices) > 0)
    <!-- Imported Services List Card -->
    <form action="{{ route('admin.smm.import.store') }}" method="POST">
        @csrf
        <input type="hidden" name="id_api_smm" value="{{ $selectedApiId }}">

        <!-- Markups Card -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-cog me-1"></i>
                Konfigurasi Keuntungan (Markup)
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="markupInput">Markup Harga Jual Member (%)</label>
                        <input class="form-control" id="markupInput" name="markup" type="number" step="0.01" value="10" placeholder="Contoh: 10" required>
                        <small class="text-muted">Persentase keuntungan untuk harga jual normal ke member.</small>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="resellerMarkupInput">Markup Harga Reseller (%)</label>
                        <input class="form-control" id="resellerMarkupInput" name="reseller_markup" type="number" step="0.01" value="5" placeholder="Contoh: 5" required>
                        <small class="text-muted">Persentase keuntungan untuk harga reseller.</small>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="localCategorySelect">Pilih Kategori untuk Menyimpan Layanan (Lokal)</label>
                        <select class="form-select" id="localCategorySelect" name="id_category_smm">
                            <option value="">-- Gunakan Kategori Bawaan API Provider --</option>
                            @foreach($categories ?? [] as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Jika dikosongkan/memilih opsi bawaan, kategori akan otomatis dicari atau dibuat baru berdasarkan kategori dari API Provider.</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-table me-1"></i>
                    Layanan Ditemukan ({{ count($fetchedServices) }} Layanan)
                </div>
                <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-download me-1"></i> Impor Layanan Terpilih</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="datatablesSimple">
                        <thead>
                            <tr>
                                <th width="50" class="text-center">
                                    <div class="form-check d-inline-block">
                                        <input class="form-check-input" id="checkAll" type="checkbox">
                                    </div>
                                </th>
                                <th>PID</th>
                                <th>Nama Layanan</th>
                                <th>Kategori</th>
                                <th>Tipe</th>
                                <th>Min - Max</th>
                                <th>Harga Provider (per 1000)</th>
                                <th>Harga Jual (Est)</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th width="50" class="text-center">#</th>
                                <th>PID</th>
                                <th>Nama Layanan</th>
                                <th>Kategori</th>
                                <th>Tipe</th>
                                <th>Min - Max</th>
                                <th>Harga Provider</th>
                                <th>Harga Jual</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            @foreach($fetchedServices as $index => $item)
                            <tr>
                                <td class="text-center">
                                    <div class="form-check d-inline-block">
                                        <input class="form-check-input select-item" id="check-{{ $index }}" type="checkbox" name="services[]" value="{{ json_encode($item) }}">
                                    </div>
                                </td>
                                <td>{{ $item['service'] ?? '-' }}</td>
                                <td>{{ $item['name'] ?? '-' }}</td>
                                <td><span class="badge bg-secondary">{{ $item['category'] ?? 'Uncategorized' }}</span></td>
                                <td>
                                    <span class="badge bg-info text-dark">{{ $item['type'] ?? 'Default' }}</span>
                                </td>
                                <td>{{ number_format($item['min'] ?? 0) }} - {{ number_format($item['max'] ?? 0) }}</td>
                                <td>$ {{ number_format($item['rate'] ?? 0, 4, '.', ',') }}</td>
                                <td class="sale-price" data-rate="{{ $item['rate'] ?? 0 }}">
                                    $ {{ number_format(($item['rate'] ?? 0) * 1.10, 4, '.', ',') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer text-end">
                <button type="submit" class="btn btn-success"><i class="fas fa-download me-1"></i> Impor Layanan Terpilih</button>
            </div>
        </div>
    </form>
    @endif
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkAll = document.getElementById('checkAll');
    const selectItems = document.querySelectorAll('.select-item');
    const markupInput = document.getElementById('markupInput');
    const salePrices = document.querySelectorAll('.sale-price');

    // Handle Check/Uncheck All
    if (checkAll) {
        checkAll.addEventListener('change', function() {
            selectItems.forEach(item => {
                item.checked = this.checked;
            });
        });
    }

    // Dynamic price calculation based on markup input
    if (markupInput) {
        markupInput.addEventListener('input', function() {
            const markup = parseFloat(this.value) || 0;
            salePrices.forEach(td => {
                const rate = parseFloat(td.getAttribute('data-rate')) || 0;
                const newPrice = rate * (1 + markup / 100);
                td.textContent = '$ ' + newPrice.toFixed(4);
            });
        });
    }
});
</script>
@endsection
