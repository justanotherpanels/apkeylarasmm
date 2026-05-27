@extends('layouts.admin.master')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Daftar Kategori SMM</h1>
    
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
                Data Kategori SMM
            </div>
            <a href="{{ route('admin.smm.category.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah Kategori</a>
        </div>
        <div class="card-body">
            <table id="datatablesSimple">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Kategori</th>
                        <th>Kode</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>ID</th>
                        <th>Nama Kategori</th>
                        <th>Kode</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </tfoot>
                <tbody>
                    @foreach($categories ?? [] as $category)
                    <tr>
                        <td>{{ $category->id }}</td>
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->code }}</td>
                        <td>
                            @if($category->status == 'Active')
                                <span class="badge bg-success">{{ $category->status }}</span>
                            @else
                                <span class="badge bg-danger">{{ $category->status }}</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.smm.category.edit', $category->id) }}" class="btn btn-warning btn-sm px-2 py-1" title="Edit"><i class="fas fa-pencil-alt"></i></a>
                            <form action="{{ route('admin.smm.category.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini? Semua platform dan layanan di bawah kategori ini juga akan terhapus!');" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm px-2 py-1" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
