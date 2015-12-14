<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ProductsAttributes extends Eloquent {

    protected $table = 'bn_products_attributes';
    
    protected $touches = array('product');
    
    protected $guarded = array('products_attributes_id');
    
    public $timestamps = false;

    protected $primaryKey = 'products_attributes_id';
    
    protected $fillable = array('products_id', 'options_id', 'options_values_id', 'options_values_price', 'price_prefix');
    
    public function product()
    {
        return $this->belongsTo("Product");
    }
    
    /*public function ProductsOptions()
    { 
        return $this->hasOne("ProductsOptions", "options_id", "products_options_id")->select('products_options_name');;
    }
    
    public function ProductsOptionsValues()
    { 
        return $this->hasOne("ProductsOptionsValues", "options_values_id", "products_options_values_id");
    }*/
}