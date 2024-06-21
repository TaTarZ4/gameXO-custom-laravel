<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Round extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'qtyX',
        'qtyY',
        'qtyWin',
        'type',
        'winner'
    ];

    public function action()
    {
        return $this->hasMany(Round::class , 'round_id' , 'id');
    }
}
