<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderList extends Model
{
    use HasFactory;
    protected $table = 'provider_lists';


    public function Transaction()
    {
        return $this->belongsTo(GamesHistory::class, 'ProviderCode', 'ProductID')->orderBy('created_at','desc');
    }
}
