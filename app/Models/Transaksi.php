<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaksi extends Model
{
    use SoftDeletes;

    protected $table = 'transaksi';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'sales_visit_id',
        'sales_id',
        'company_id',
        'pic_id',
        'nama_sales',
        'nama_perusahaan',
        'pic_name',
        'nilai_proyek',
        'status',
        'bukti_spk',
        'bukti_dp',
        'tanggal_mulai_kerja',
        'tanggal_selesai_kerja',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_mulai_kerja' => 'date',
        'tanggal_selesai_kerja' => 'date',
        'nilai_proyek' => 'decimal:2',
    ];

    // ============= RELATIONSHIPS =============

    public function salesVisit()
    {
        return $this->belongsTo(SalesVisit::class, 'sales_visit_id', 'id');
    }

    public function sales()
    {
        return $this->belongsTo(User::class, 'sales_id', 'user_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }

    public function pic()
    {
        return $this->belongsTo(CompanyPic::class, 'pic_id', 'pic_id');
    }

    // ============= SCOPES =============

    public function scopeDeals($query)
    {
        return $query->where('status', 'Deals');
    }

    public function scopeFails($query)
    {
        return $query->where('status', 'Fails');
    }

    public function scopeBySales($query, $salesId)
    {
        return $query->where('sales_id', $salesId);
    }

    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeSearchByName($query, $search)
    {
        return $query->where('nama_sales', 'like', "%{$search}%")
                    ->orWhere('nama_perusahaan', 'like', "%{$search}%");
    }

    // ============= ACCESSORS =============

    public function getValueFormattedAttribute()
    {
        return 'Rp' . number_format($this->nilai_proyek, 0, ',', '.');
    }

    public function getStatusBadgeAttribute()
    {
        if ($this->status === 'Deals') {
            return '<span style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.25rem 0.625rem; background-color: #dcfce7; color: #166534; border-radius: 9999px; font-size: 0.7rem; font-weight: 600;"><i class="fas fa-check-circle"></i> Deals</span>';
        } else {
            return '<span style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.25rem 0.625rem; background-color: #fee2e2; color: #991b1b; border-radius: 9999px; font-size: 0.7rem; font-weight: 600;"><i class="fas fa-times-circle"></i> Fails</span>';
        }
    }
}