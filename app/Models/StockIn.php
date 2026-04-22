<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockIn extends Model
{
    use HasFactory;
    protected $fillable = ['product_id', 'rak_id', 'quantity', 'date_time', 'created_by'];

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }
    public function rak()
    {
        return $this->belongsTo(Rak::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
