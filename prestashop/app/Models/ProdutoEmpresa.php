<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProdutoEmpresa extends Model
{
    protected $table = 'produto_empresa';
    protected $primaryKey = 'proId';
    protected $guarded = [];
}
