<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Product extends Eloquent {

    protected $table = 'bn_products';
    
    //protected $guarded = array('products_id');
    protected $primaryKey = 'products_id';
    
    protected $fillable = array('products_quantity', 'products_model', 'products_image', 'products_price', 'products_orginal_price', 'brand', 'products_donar', 'created_by', 'products_status','condition','gender','age_range','freshly_faved','seasons_id','types_id','collections_id');
    
    public function productCategory()
    {
        return $this->belongsToMany('Categories', 'bn_products_to_categories', 'products_id', 'categories_id');
    }
    
    public function productsAttributes()
    { 
        return $this->hasMany("ProductsAttributes","products_id");
    }
    
    public function productsDescription()
    { 
        return $this->hasOne("ProductsDescription","products_id");
    }
    
    public function donar()
    {
        return $this->belongsTo('User', 'products_donar', 'id')->select('first','username','email');
    }
	
	 public function productBrand()
    {
        return $this->belongsTo('Brands', 'brand', 'brands_id')->select('brand_name','brand_image','brand_logo');
    }
	
	public function productCondition()
    {
        return $this->belongsTo('Conditions', 'condition', 'id')->select('name');
    }
    
    public function productTags()
    {
        return $this->belongsToMany('Tags', 'bn_products_to_tags', 'products_id', 'tags_id');
    }
    
    public function ordersProducts() {
        return $this->hasOne("OrdersProducts", "products_id", "products_id");
    }
}