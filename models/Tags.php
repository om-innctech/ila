<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Tags extends Eloquent {

    protected $table = 'bn_tags';
    
    //protected $guarded = array('products_id');
    protected $primaryKey = 'tags_id';
    
    protected $fillable = array('title','status');
    
    
}