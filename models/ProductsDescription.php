<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ProductsDescription extends Eloquent {

    protected $table = 'bn_products_description';
    
    protected $touches = array('product');
    
    //protected $guarded = array('id');
    protected $primaryKey = 'products_id';
    
    public $timestamps = false;
    
    protected $fillable = array('products_id', 'products_name', 'products_description', 'products_url', 'products_viewed');
    
    public function product()
    {
        return $this->belongsTo("Product");
    }
    
}