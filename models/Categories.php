<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Categories extends Eloquent {

    protected $table = 'bn_categories';
    
    protected $primaryKey = 'categories_id';
    
    protected $fillable = array('title', 'parent_id', 'status', 'image', 'sort_order', 'created_at', 'updated_at', 'is_bucknation','is_featured');
    
    public function Parent()
    {
        return $this->hasOne('Categories','categories_id','parent_id');
    }

    public function Children()
    {
        return $this->hasMany('Categories','parent_id');   // user category_id for parent category  
    }
    
    public function treeBuild($cate,$level=0){   // this is recursive function

    global $tree;   // need global variable

     foreach ($cate as $list ) {
         
            if ($list->parent_id!=0) {    // this is look ugly  for  reset level when root category found ( top category_id must =   0 )
            
                $level++;  
    
            }else{

                $level = 0;   // reset level
            }
              $dat = str_repeat("- ", $level); // add  " -  " each level depth 
            $tree[$list->categories_id] =  $dat.$list->title;
               
            if (!is_null($list->children)) {   // check if this category has children

                $level++;

                foreach ($list->children as $lists ) {
                      $dat = str_repeat("- ", $level);    // add  " -  " each level depth  you can change to whatever u want
                     $tree[$lists->categories_id] = $dat.$lists->title;

                    $this->treeBuild($lists->children,$level);   // recursive
                
                }
            }
        }
        return  $tree;
     

    }

    static  public function Tree($level){    // call this function    with level depth u want
    
    $cate = Categories::with(implode('.', array_fill(0, $level, 'children')))->whereparent_id(0)->get();    
       
    $new = new Categories;
    return  $new->treeBuild($cate);      
    

       
    }
  

    public function subcats($cate,$level = 0)
    {
        global $subtree;   // need global variable

     foreach ($cate as $list ) {
         
            if ($list->parent_id!=0) {    // this is look ugly  for  reset level when root category found ( top category_id must =   0 )
            
                $level++;  
    
            }else{

                $level = 0;   // reset level
            }
            
            $subtree[$list->categories_id] =  $list->title;
               
            if (!is_null($list->children)) {   // check if this category has children

                $level++;

                foreach ($list->children as $lists ) {
                    
                     $subtree[$lists->categories_id] = $lists->title;

                    $this->subcats($lists->children,$level);   // recursive
                
                }
            }
        }
        return  $subtree;
    }


}