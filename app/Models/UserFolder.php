<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFolder extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function userFolderSubscriptions()
    {
        return $this->hasMany(UserFolderSubscription::class);
    }
}
