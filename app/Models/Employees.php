<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employees extends Model
{
  protected $fillable = [
      'id', 'name', 'email','mobile_no','department','status'
  ];
}
