<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Verification extends Model
{
    protected $table = 'verifications';

    protected $fillable = [
        'protocol_id', 
        'sekretariat_id', 
        'verified_at', 
        'notes', 
        'status', 
        'review_type'
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public function protocol()
    {
        return $this->belongsTo(Protocol::class);
    }

    public function sekretariat()
    {
        return $this->belongsTo(User::class, 'sekretariat_id');
    }
}