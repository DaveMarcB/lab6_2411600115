<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'type',
        'quantity',
        'reference_number',
        'notes',
    ];

    // A transaction belongs to a product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // A transaction belongs to a user (who acted)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Filter for stock in transactions
    public function scopeStockIn($query)
    {
        return $query->where('type', 'stock_in');
    }

    // Filter for stock out transactions
    public function scopeStockOut($query)
    {
        return $query->where('type', 'stock_out');
    }

    // Order by newest first
    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}