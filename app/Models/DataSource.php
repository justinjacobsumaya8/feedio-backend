<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataSource extends Model
{
    use HasFactory;

    protected $guarded = [];

    const THE_GUARDIAN_ID = 1;
    const NEWS_API_ID = 2;
    const THE_NEW_YORK_TIMES_ID = 3;
}
