@extends('layouts.admin.master')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Riwayat Pesanan SMM</h1>
    
    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-4" role="alert">
        {{ session('success') }}
        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card mb-4 mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-table me-1"></i>
                Daftar Pesanan
            </div>
        </div>
        <div class="card-body">
            <table id="datatablesSimple">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>User</th>
                        <th>Layanan</th>
                        <th>Jumlah</th>
                        <th>Harga API (Rp)</th>
                        <th>Harga Jual (Rp)</th>
                        <th>Harga Reseller (Rp)</th>
                        <th>Provider (SID)</th>
                        <th>Status</th>
                        <th>Refill</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Invoice</th>
                        <th>User</th>
                        <th>Layanan</th>
                        <th>Jumlah</th>
                        <th>Harga API (Rp)</th>
                        <th>Harga Jual (Rp)</th>
                        <th>Harga Reseller (Rp)</th>
                        <th>Provider (SID)</th>
                        <th>Status</th>
                        <th>Refill</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </tfoot>
                <tbody>
                    @foreach($orders ?? [] as $order)
                    <tr>
                        <td>
                            <span class="fw-bold">{{ $order->invoice }}</span>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span>{{ $order->user->full_name ?? '-' }}</span>
                                <small class="text-muted">{{ $order->user->username ?? '-' }}</small>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span>{{ $order->service->name_service ?? 'Layanan Terhapus' }}</span>
                                <small class="text-muted">ID: {{ $order->id_service_smm ?? '-' }}</small>
                            </div>
                        </td>
                        <td>{{ number_format($order->amount) }}</td>
                        <td>
                            <div class="text-end">
                                <span class="text-warning">Rp {{ number_format($order->price_api, 2, ',', '.') }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="text-end">
                                <span class="text-success">Rp {{ number_format($order->price_sale, 2, ',', '.') }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="text-end">
                                <span class="text-info">Rp {{ number_format($order->price_reseller, 2, ',', '.') }}</span>
                            </div>
                        </td>
                        <td>
                            @if($order->api)
                                <div class="d-flex flex-column">
                                    <span class="badge bg-light text-dark border">{{ $order->api->name }}</span>
                                    <small class="text-muted">SID: {{ $order->sid ?? '-' }}</small>
                                </div>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($order->status_order == 'Pending')
                                <span class="badge bg-warning text-dark">{{ $order->status_order }}</span>
                            @elseif($order->status_order == 'In Progres')
                                <span class="badge bg-info">{{ $order->status_order }}</span>
                            @elseif($order->status_order == 'Partial')
                                <span class="badge bg-primary">{{ $order->status_order }}</span>
                            @elseif($order->status_order == 'Cancel')
                                <span class="badge bg-danger">{{ $order->status_order }}</span>
                            @elseif($order->status_order == 'Error')
                                <span class="badge bg-danger">{{ $order->status_order }}</span>
                            @elseif($order->status_order == 'Success')
                                <span class="badge bg-success">{{ $order->status_order }}</span>
                            @elseif($order->status_order == 'Finish')
                                <span class="badge bg-success">{{ $order->status_order }}</span>
                            @else
                                <span class="badge bg-secondary">{{ $order->status_order }}</span>
                            @endif
                        </td>
                        <td>
                            @if($order->refill)
                                <span class="badge bg-primary"><i class="fas fa-sync-alt"></i> Aktif</span>
                            @else
                                <span class="badge bg-light text-dark border">Tidak</span>
                            @endif
                        </td>
                        <td>
                            <span>{{ $order->create_at ? \Carbon\Carbon::parse($order->create_at)->format('d/m/Y H:i') : '-' }}</span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                @if($order->api && $order->sid)
                                    <form method="POST" action="{{ route('admin.smm.order.sync', $order->id) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-info btn-sm px-2 py-1 text-white" title="Sync Status"><i class="fas fa-sync-alt"></i></button>
                                    </form>
                                @endif
                                <button class="btn btn-warning btn-sm px-2 py-1" title="Edit Detail" data-bs-toggle="modal" data-bs-target="#editModal{{ $order->id }}"><i class="fas fa-pencil-alt"></i></button>
                                <form action="#" method="POST" onsubmit="return confirm('Yakin ingin menghapus order ini?');" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm px-2 py-1" title="Hapus"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>

                            <!-- Modal Edit Status / Detail -->
                            <div class="modal fade" id="editModal{{ $order->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $order->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editModalLabel{{ $order->id }}">Edit Status Pesanan #{{ $order->invoice }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="#" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Status Pesanan</label>
                                                    <select class="form-select" name="status_order">
                                                        <option value="Pending" {{ $order->status_order == 'Pending' ? 'selected' : '' }}>Pending</option>
                                                        <option value="In Progres" {{ $order->status_order == 'In Progres' ? 'selected' : '' }}>In Progres</option>
                                                        <option value="Partial" {{ $order->status_order == 'Partial' ? 'selected' : '' }}>Partial</option>
                                                        <option value="Cancel" {{ $order->status_order == 'Cancel' ? 'selected' : '' }}>Cancel</option>
                                                        <option value="Error" {{ $order->status_order == 'Error' ? 'selected' : '' }}>Error</option>
                                                        <option value="Success" {{ $order->status_order == 'Success' ? 'selected' : '' }}>Success</option>
                                                        <option value="Finish" {{ $order->status_order == 'Finish' ? 'selected' : '' }}>Finish</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Start Count</label>
                                                    <input type="number" class="form-control" name="start_count" value="{{ $order->start_count }}">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Remains</label>
                                                    <input type="number" class="form-control" name="remains" value="{{ $order->remains }}">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Simpan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
