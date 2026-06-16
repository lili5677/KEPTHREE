<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SekretariatDecision extends Model
{
    protected $fillable = [
        'protocol_id',
        'sekretariat_id',
        'keputusan',
        'catatan',
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