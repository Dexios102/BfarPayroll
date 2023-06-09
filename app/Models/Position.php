<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Position extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "position";
    protected $fillable = [
        'code',
        'description',
        'name',
        
    ];

    public function division_info(){
        return $this->belongsTo(Department::class,'division');
    }

}
