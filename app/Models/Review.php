<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $table = 'reviews';

    protected $fillable = [
        'protocol_id',
        'reviewer_id',
        'deadline',
        'submitted_at',
        'catatan',
        'keputusan',
        'created_by'
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    public function protocol()
    {
        return $this->belongsTo(Protocol::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}