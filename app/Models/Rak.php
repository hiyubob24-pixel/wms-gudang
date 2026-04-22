<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rak extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'location', 'nomor_rak', 'tingkat', 'bagian', 'capacity'];

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function stockIns()
    {
        return $this->hasMany(StockIn::class);
    }

    public function stockOuts()
    {
        return $this->hasMany(StockOut::class);
    }

    // Menghitung total isi rak saat ini
    public function getCurrentOccupancyAttribute()
    {
        // Menggunakan relasi yang sudah di-load agar tidak N+1 query
        return $this->stocks->sum('quantity');
    }

    // Menghitung sisa ruang tersedia
    public function getAvailableSpaceAttribute()
    {
        return $this->capacity - $this->current_occupancy;
    }

    public function getDisplayNameAttribute()
    {
        $identitas = array_filter([$this->nomor_rak, $this->tingkat, $this->bagian]);
        $namaDetail = empty($identitas) ? $this->name : implode(' - ', $identitas);

        return "{$namaDetail} (Sisa: {$this->available_space})";
    }
}
