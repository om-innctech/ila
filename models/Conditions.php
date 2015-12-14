<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Conditions extends Eloquent {

    protected $table = 'bn_conditions';
    
    protected $primaryKey = 'id';
    
    protected $fillable = array('name');
 


}