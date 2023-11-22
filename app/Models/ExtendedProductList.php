<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExtendedProductList extends Model
{
    protected $table = 'extended_product_list_tables';

    // Assuming you want to allow mass assignment for these fields
    protected $fillable = [
        'Código_Produto', 'Classificação', 'Subproduto', 'Local', 'Fetch_Status',
        'bolsa', 'roda', 'fonte', 'tav', 'subtav', 'decimais', 'correlatos',
        'empresa', 'contrato', 'subproduto_id', 'entcode', 'nome', 'longo', 'descr',
        'codf', 'bd', 'palavras', 'habilitado', 'lote', 'rep', 'vln', 'dia',
        'freq', 'dex', 'inserido', 'alterado'
    ];

    // If you don't use the created_at and updated_at timestamps, set this to false
    public $timestamps = true;

    // Define the relationship with DataSeries
    public function dataSeries()
    {
        return $this->hasOne(DataSeries::class, 'extended_product_list_id');
    }
}

