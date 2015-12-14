<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ProductsOptionsValues extends Eloquent {

    protected $table = 'bn_products_options_values';
    
    protected $primaryKey = 'products_options_values_id';
    //protected $guarded = array('products_id');
    
    public $timestamps = false;
    
    protected $fillable = array('products_options_values_id', 'products_options_values_name');
    
    /*public function ProductsAttributes()
    {
        return $this->belongsTo("ProductsAttributes");
    }*/
    
}