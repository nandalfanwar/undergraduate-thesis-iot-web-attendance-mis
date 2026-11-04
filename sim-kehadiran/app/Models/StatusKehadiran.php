<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusKehadiran extends Model
{
    use HasFactory;

    protected $table = 'status_kehadiran';

    protected $fillable = [
        'status_kehadiran_label'
    ];

    public function dataKehadiran()
    {
        return $this->hasMany(DataKehadiran::class, 'status_kehadiran');
    }
}
