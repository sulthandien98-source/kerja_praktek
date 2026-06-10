<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'customer_name', 'phone', 'address', 'total_price', 'status',
        'payment_proof', 'payment_status', 'payment_note', 'payment_uploaded_at',
    ];

    protected $casts = [
        'payment_uploaded_at' => 'datetime',
        'total_price'         => 'integer',
    ];

    // ── Order statuses ────────────────────────────────────────

    const STATUS_PENDING  = 'pending';
    const STATUS_WAITING  = 'menunggu_konfirmasi';
    const STATUS_DIPROSES = 'diproses';
    const STATUS_SELESAI  = 'selesai';
    const STATUS_DITOLAK  = 'ditolak';

    const STATUSES = [
        self::STATUS_PENDING  => ['label' => 'Pending',             'color' => 'yellow'],
        self::STATUS_WAITING  => ['label' => 'Menunggu Konfirmasi', 'color' => 'orange'],
        self::STATUS_DIPROSES => ['label' => 'Diproses',            'color' => 'blue'],
        self::STATUS_SELESAI  => ['label' => 'Selesai',             'color' => 'green'],
        self::STATUS_DITOLAK  => ['label' => 'Ditolak',             'color' => 'red'],
    ];

    // ── Payment statuses ──────────────────────────────────────

    const PAYMENT_UNPAID   = 'unpaid';
    const PAYMENT_UPLOADED = 'uploaded';
    const PAYMENT_APPROVED = 'approved';
    const PAYMENT_REJECTED = 'rejected';

    const PAYMENT_STATUSES = [
        self::PAYMENT_UNPAID   => ['label' => 'Belum Bayar',         'color' => 'gray'],
        self::PAYMENT_UPLOADED => ['label' => 'Menunggu Verifikasi',  'color' => 'yellow'],
        self::PAYMENT_APPROVED => ['label' => 'Terverifikasi',        'color' => 'green'],
        self::PAYMENT_REJECTED => ['label' => 'Ditolak',              'color' => 'red'],
    ];

    // ── Relationships ─────────────────────────────────────────

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Order status accessors ────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status]['label'] ?? ucfirst($this->status);
    }

    public function getStatusColorAttribute(): string
    {
        return self::STATUSES[$this->status]['color'] ?? 'gray';
    }

    // ── Payment status accessors ──────────────────────────────

    public function getPaymentStatusLabelAttribute(): string
    {
        return self::PAYMENT_STATUSES[$this->payment_status]['label'] ?? ucfirst($this->payment_status ?? 'unpaid');
    }

    public function getPaymentStatusColorAttribute(): string
    {
        return self::PAYMENT_STATUSES[$this->payment_status]['color'] ?? 'gray';
    }

    public function getPaymentProofUrlAttribute(): ?string
    {
        return $this->payment_proof
            ? asset('storage/' . $this->payment_proof)
            : null;
    }

    // ── Helpers ───────────────────────────────────────────────

    public function canUploadPayment(): bool
    {
        return in_array($this->payment_status ?? self::PAYMENT_UNPAID, [
            self::PAYMENT_UNPAID,
            self::PAYMENT_REJECTED,
        ]);
    }
}
