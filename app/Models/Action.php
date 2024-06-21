<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    use HasFactory;

    protected $fillable = [
        'orderNo',
        'action',
        'x',
        'y',
        'round_id'
    ];

    public function round()
    {
        return $this->hasOne(Action::class , 'id' , 'round_id');
    }
}
