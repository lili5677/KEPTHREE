<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProtocolReviewer extends Model
{
    protected $table = 'protocol_reviewers';

    protected $fillable = [
        'protocol_id',
        'reviewer_id',
        'deadline',
        'catatan',
        'status',
    ];

    public function protocol()
    {
        return $this->belongsTo(Protocol::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}