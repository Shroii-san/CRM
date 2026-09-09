<div class="row">
    <div class="col-md-8">
        <div class="mb-4">
            <label class="form-label text-muted small">NAMA</label>
            <h4 class="fw-bold mb-0">{{ $pipeline['nama'] }}</h4>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <label class="form-label text-muted small">EMAIL</label>
                <p class="mb-0"><a href="mailto:{{ $pipeline['email'] }}">{{ $pipeline['email'] }}</a></p>
            </div>
            <div class="col-md-6">
                <label class="form-label text-muted small">TELEPON</label>
                <p class="mb-0"><a href="tel:{{ $pipeline['phone'] }}">{{ $pipeline['phone'] }}</a></p>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label text-muted small">NILAI</label>
            <h5 class="fw-bold mb-0" style="color: #10b981;">Rp {{ number_format($pipeline['value'], 0, ',', '.') }}</h5>
        </div>

        <div class="mb-4">
            <label class="form-label text-muted small">CATATAN</label>
            <p class="mb-0">{{ $pipeline['notes'] }}</p>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card" style="background-color: #f9fafb; border: 1px solid #e5e7eb;">
            <div class="card-body">
                <h6 class="card-title mb-3"><i class="fas fa-info-circle"></i> Informasi</h6>
                <small class="d-block mb-2">
                    <strong>ID:</strong> {{ $pipeline['id'] }}
                </small>
                <small class="d-block">
                    <strong>Tanggal:</strong> {{ $pipeline['date'] }}
                </small>
            </div>
        </div>
    </div>
</div>