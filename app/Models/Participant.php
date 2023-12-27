<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

class Participant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'cpf', 'dateBirth', 'pix'];
}
