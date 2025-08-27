@extends('layouts.admin')

@section('title', 'Kategori Yönetimi')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">Kategori Yönetimi</h2>
            <p class="text-muted mb-0">Kategorileri yönetin ve fotoğraflarını ekleyin</p>
        </div>
        <button class="btn btn-primary d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#categoryForm">
            <i class="bi bi-plus-circle"></i> Yeni Kategori
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Kategori Ekleme Formu --}}
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-plus-circle text-primary me-2"></i>
                Yeni Kategori Ekle
            </h5>
            <button class="btn btn-outline-secondary btn-sm d-none d-md-block" type="button" data-bs-toggle="collapse"
                data-bs-target="#categoryForm">
                <i class="bi bi-chevron-down"></i>
            </button>
        </div>
        <div class="collapse show" id="categoryForm">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data"
                    id="categoryAddForm">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Kategori Adı <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Kategori adını giriniz"
                                required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Kategori Fotoğrafı</label>
                            <input type="file" name="image" id="category_image" class="form-control" accept="image/*"
                                onchange="previewCategoryImage(this)">
                            <div class="form-text">JPG, PNG, GIF formatları desteklenir (Max: 2MB)</div>
                            <div class="mt-2" id="imagePreview" style="display: none;">
                                <img id="previewImg" src="" class="img-thumbnail"
                                    style="max-width: 150px; max-height: 150px;">
                            </div>
                        </div>

                        <div class="col-12">
                            <hr class="my-3">
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-outline-secondary" onclick="resetCategoryForm()">
                                    <i class="bi bi-arrow-clockwise me-1"></i>
                                    Temizle
                                </button>
                                <button type="submit" class="btn btn-success" id="categorySubmitBtn">
                                    <i class="bi bi-plus-circle me-1"></i>
                                    Kategori Ekle
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Kategori Listesi --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-list-ul text-primary me-2"></i>
                Kategori Listesi
            </h5>
            <button class="btn btn-outline-primary btn-sm" onclick="refreshCategories()">
                <i class="bi bi-arrow-clockwise"></i>
                <span class="d-none d-md-inline ms-1">Yenile</span>
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="60">#</th>
                            <th>Kategori</th>
                            <th class="d-none d-md-table-cell">Ürün Sayısı</th>
                            <th width="120">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $cat)
                            <tr>
                                <td class="fw-bold text-muted">{{ $cat->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if(!empty($cat->image))
                                            <img src="{{ asset('storage/' . $cat->image) }}" class="rounded me-2" width="32"
                                                height="32" style="object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded me-2 d-flex align-items-center justify-content-center"
                                                style="width: 32px; height: 32px;">
                                                <i class="bi bi-image text-muted"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <h6 class="mb-0">{{ $cat->name }}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <span class="badge bg-secondary">{{ $cat->products_count ?? $cat->products->count() }}
                                        ürün</span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary"
                                            onclick="editCategory({{ $cat->id }})" title="Düzenle">
                                            <i class="bi bi-pencil"></i>
                                        </button>

                                        <button type="button" class="btn btn-outline-danger"
                                            onclick="deleteCategory({{ $cat->id }})" title="Sil">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4">
                                    <i class="bi bi-folder2-open fs-1 text-muted"></i>
                                    <p class="text-muted mt-2 mb-0">Henüz kategori eklenmemiş</p>
                                    <small class="text-muted">Yukarıdaki formu kullanarak yeni kategori ekleyebilirsiniz</small>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Kategori Düzenleme Modal --}}
    <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCategoryModalLabel">
                        <i class="bi bi-pencil-square text-primary me-2"></i>
                        Kategori Düzenle
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editCategoryForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Kategori Adı <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="edit_category_name" class="form-control" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Kategori Fotoğrafı</label>
                                <input type="file" name="image" id="edit_category_image" class="form-control"
                                    accept="image/*" onchange="previewEditCategoryImage(this)">
                                <div class="form-text">JPG, PNG, GIF formatları desteklenir (Max: 2MB)</div>
                                <div class="mt-2" id="editCategoryImagePreview">
                                    <img id="editCategoryPreviewImg" src="" class="img-thumbnail"
                                        style="max-width: 150px; max-height: 150px; display: none;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i>
                            İptal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i>
                            Güncelle
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Form temizleme
        function resetCategoryForm() {
            const form = document.getElementById('categoryAddForm');
            form?.reset();
            const preview = document.getElementById('imagePreview');
            const img = document.getElementById('previewImg');
            if (preview && img) {
                preview.style.display = 'none';
                img.src = '';
            }
        }

        // Kategori listesini yenile
        function refreshCategories() { location.reload(); }

        // Kategori düzenle (Bootstrap Modal ile)
        function editCategory(categoryId) {
            fetch(`/admin/categories/${categoryId}`)
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP ${response.status}`);
                    return response.json();
                })
                .then(category => {
                    document.getElementById('edit_category_name').value = category.name || '';

                    const previewImg = document.getElementById('editCategoryPreviewImg');
                    if (category.image) {
                        previewImg.src = `/storage/${category.image}`;
                        previewImg.style.display = 'block';
                    } else {
                        previewImg.style.display = 'none';
                    }

                    const form = document.getElementById('editCategoryForm');
                    form.action = `/admin/categories/${categoryId}`;

                    const modal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
                    modal.show();
                })
                .catch(err => alert('Kategori bilgileri alınırken bir hata oluştu: ' + err.message));
        }

        // Kategori sil
        function deleteCategory(categoryId) {
            fetch(`/admin/categories/${categoryId}`)
                .then(r => r.json())
                .then(category => {
                    const productCount = category.products ? category.products.length : 0;
                    let msg = `Bu kategoriyi silmek istediğinizden emin misiniz?\n\n`;
                    if (productCount > 0) msg += `⚠️ DİKKAT: Bu kategoriye ait ${productCount} adet ürün de silinecek!\n\n`;
                    msg += `Bu işlem geri alınamaz.`;
                    if (!confirm(msg)) return;

                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/admin/categories/${categoryId}`;

                    form.innerHTML = `
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="DELETE">
                `;
                    document.body.appendChild(form);
                    form.submit();
                })
                .catch(() => {
                    if (confirm('Bu kategoriyi silmek istediğinizden emin misiniz?\n\nBu işlem geri alınamaz.')) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/admin/categories/${categoryId}`;
                        form.innerHTML = `
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="DELETE">
                    `;
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
        }

        // Fotoğraf önizleme (ekleme)
        function previewCategoryImage(input) {
            const file = input.files[0];
            const preview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');
            if (!file) return preview.style.display = 'none';

            if (file.size > 2 * 1024 * 1024) { alert('Dosya boyutu 2MB\'dan büyük olamaz!'); input.value = ''; return preview.style.display = 'none'; }
            if (!file.type.startsWith('image/')) { alert('Lütfen geçerli bir resim dosyası seçin!'); input.value = ''; return preview.style.display = 'none'; }

            const reader = new FileReader();
            reader.onload = e => { previewImg.src = e.target.result; preview.style.display = 'block'; };
            reader.readAsDataURL(file);
        }

        // Fotoğraf önizleme (düzenleme)
        function previewEditCategoryImage(input) {
            const file = input.files[0];
            const previewImg = document.getElementById('editCategoryPreviewImg');
            if (!file) return previewImg.style.display = 'none';

            if (file.size > 2 * 1024 * 1024) { alert('Dosya boyutu 2MB\'dan büyük olamaz!'); input.value = ''; return previewImg.style.display = 'none'; }
            if (!file.type.startsWith('image/')) { alert('Lütfen geçerli bir resim dosyası seçin!'); input.value = ''; return previewImg.style.display = 'none'; }

            const reader = new FileReader();
            reader.onload = e => { previewImg.src = e.target.result; previewImg.style.display = 'block'; };
            reader.readAsDataURL(file);
        }

        // Form validation + event binding
        document.addEventListener('DOMContentLoaded', function () {
            const addForm = document.getElementById('categoryAddForm');
            const imageInput = document.getElementById('category_image');
            const editImageInput = document.getElementById('edit_category_image');

            if (imageInput) imageInput.addEventListener('change', () => previewCategoryImage(imageInput));
            if (editImageInput) editImageInput.addEventListener('change', () => previewEditCategoryImage(editImageInput));

            if (addForm) {
                addForm.addEventListener('submit', function (e) {
                    const nameInput = this.querySelector('input[name="name"]');
                    if (!nameInput || !nameInput.value.trim()) {
                        e.preventDefault();
                        alert('Kategori adı gereklidir!');
                    }
                });
            }
        });
    </script>
@endpush