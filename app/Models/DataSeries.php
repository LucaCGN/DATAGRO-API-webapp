<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataSeries extends Model
{
    protected $table = 'data_series_tables';

    protected $fillable = [
        'extended_product_list_id', 'cod', 'data', 'ult', 'mini', 'maxi',
        'abe', 'volumes', 'cab', 'med', 'aju'
    ];

    public $timestamps = true;

    // Define the relationship with ExtendedProductList
    public function extendedProductList()
    {
        return $this->belongsTo(ExtendedProductList::class, 'extended_product_list_id');
    }
}
