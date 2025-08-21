{{-- resources/views/admin/users/manage.blade.php --}}
@extends('layouts.admin')
@section('title', 'Kullanıcılar')

@section('content')
    <div class="container-fluid py-3">

        {{-- Başlık + Hızlı butonlar --}}
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-3">
            <h2 class="mb-0">Kullanıcı Yönetimi</h2>
            <div class="d-flex gap-2">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                    <i class="bi bi-plus-lg"></i> Yeni Kullanıcı
                </button>
            </div>
        </div>

        {{-- Flash --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        {{-- Liste --}}
        <div class="card">
            <div class="card-body table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Ad</th>
                            <th>E‑posta</th>
                            <th>Rol</th>
                            <th>Durum</th>
                            <th class="text-end">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td class="fw-medium">{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="badge bg-{{ $user->role === 'admin' ? 'warning text-dark' : 'secondary' }}">
                                        {{ strtoupper($user->role ?? 'user') }}
                                    </span>
                                </td>
                                <td>
                                    @if($user->email_verified_at)
                                        <span class="badge bg-success">Doğrulanmış</span>
                                    @else
                                        <span class="badge bg-secondary">Doğrulanmamış</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#editUserModal" data-id="{{ $user->id }}"
                                            data-name="{{ $user->name }}" data-email="{{ $user->email }}"
                                            data-role="{{ $user->role }}"
                                            data-verified="{{ $user->email_verified_at ? 1 : 0 }}">
                                            Düzenle
                                        </button>

                                        <form class="ms-1" method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                            onsubmit="return confirm('Silinsin mi?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">Sil</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Kayıt yok</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-3">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Yeni Kullanıcı Modal --}}
    <div class="modal fade" id="createUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Yeni Kullanıcı</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Ad Soyad</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">E‑posta</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Şifre</label>
                        <input type="password" name="password" class="form-control" minlength="6" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rol</label>
                        <select name="role" class="form-select">
                            <option value="user" selected>Kullanıcı</option>
                            <option value="waiter">Garson</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary">Kaydet</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Düzenle Modal --}}
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="editUserForm" class="modal-content" method="POST">
                @csrf @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Kullanıcıyı Düzenle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editUserId">
                    <div class="mb-3">
                        <label class="form-label">Ad Soyad</label>
                        <input type="text" name="name" id="editName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">E‑posta</label>
                        <input type="email" name="email" id="editEmail" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Yeni Şifre (opsiyonel)</label>
                        <input type="password" name="password" class="form-control" minlength="6"
                            placeholder="Değiştirmek istemiyorsan boş bırak">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rol</label>
                        <select name="role" id="editRole" class="form-select">
                            <option value="user">Kullanıcı</option>
                            <option value="waiter">Garson</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="editVerified" name="verified">
                        <label class="form-check-label" for="editVerified">E‑posta Doğrulanmış</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary">Güncelle</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal JS: verileri forma bas + action ayarla --}}
    @push('scripts')
        <script>
            document.getElementById('editUserModal').addEventListener('show.bs.modal', function (ev) {
                const btn = ev.relatedTarget;
                const id = btn.getAttribute('data-id');
                const name = btn.getAttribute('data-name');
                const email = btn.getAttribute('data-email');
                const role = btn.getAttribute('data-role');
                const verified = btn.getAttribute('data-verified') === '1';

                document.getElementById('editUserId').value = id;
                document.getElementById('editName').value = name;
                document.getElementById('editEmail').value = email;
                document.getElementById('editRole').value = role;
                document.getElementById('editVerified').checked = verified;

                const form = document.getElementById('editUserForm');
                form.action = "{{ url('/admin/users') }}/" + id; // PUT /admin/users/{id}
            });
        </script>
    @endpush

@endsection