<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Brands extends Eloquent {

    protected $table = 'bn_brands';
    
    //protected $guarded = array('products_id');
    protected $primaryKey = 'brands_id';
    
    protected $fillable = array('brand_name', 'description', 'brand_image', 'brand_logo', 'status');
    
    
}