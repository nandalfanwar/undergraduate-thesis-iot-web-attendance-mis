<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataKehadiran extends Model
{
    use HasFactory;

    protected $table = 'data_kehadiran';

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function statusKehadiran()
    {
        return $this->belongsTo(StatusKehadiran::class, 'status_kehadiran_id');
    }
}
