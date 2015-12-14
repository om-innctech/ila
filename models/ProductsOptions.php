<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ProductsOptions extends Eloquent {

    protected $table = 'bn_products_options';
    
    protected $primaryKey = 'products_options_id';
    //protected $guarded = array('products_id');

    protected $fillable = array('products_options_id', 'products_options_name');
    
    public $timestamps = false;
    
    public function productsOptionsValues()
    {
        return $this->belongsToMany('ProductsOptionsValues', 'bn_products_options_values_to_products_options', 'products_options_id', 'products_options_values_id');
    }
    
    /*public function ProductsAttributes()
    {
        return $this->belongsTo("ProductsAttributes");
    }*/
}