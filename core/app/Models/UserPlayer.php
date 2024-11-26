<?php

namespace App\Models;

use App\Traits\GenId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPlayer extends Model
{
    use HasFactory, GenId;
    protected $table = 'user_players';

    public function Transaction()
    {
        return $this->belongsTo(GamesHistory::class, 'ext_player', 'MemberName')->orderBy('created_at','desc');
    }
}
