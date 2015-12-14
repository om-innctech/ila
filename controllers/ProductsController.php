<?php

class ProductsController extends \BaseController {

      
         public function index() {
            
            $query = Product::query();
            
            if (Input::has('category') && Input::has('attribute'))
			{
                    $iCategory= Input::get('category');
                    $category_array = explode(',', $iCategory);
                    $categories  = array();
                    $categories = DB::table('bn_products_to_categories')->whereIn('categories_id', $category_array)->get();
                    
                    $product_id_array_cat =array();

                    foreach($categories as $cat)
                    {
                        $product_id_array_cat[] = $cat->products_id;	
                    }

                    $iattribute= Input::get('attribute');
                    $attribute_array = explode(',', $iattribute);
                    $attributes  = array();
                    $attributes = DB::table('bn_products_attributes')->whereIn('options_values_id', $attribute_array)->get();
                    $product_id_array_att =array();
                    
                    foreach($attributes as $attr)
                    {
                        $product_id_array_att[] = $attr->products_id;	
                    }	

                    $common_product_array = array_intersect($product_id_array_cat,$product_id_array_att);	
                    
                    if(!empty($common_product_array))
                        $query->whereIn('products_id', $common_product_array);
                    else
                        return $this->response(array('statusCode'=>400, 'statusDescription'=>'Not Found'));
			 
		}
  		elseif (Input::has('category'))
		{   
                    $iCategory= Input::get('category');
                    $category_array = explode(',', $iCategory);
                    $categories  = array();
                    $categories = DB::table('bn_products_to_categories')->whereIn('categories_id', $category_array)->get();
                    $product_id_array =array();
                    foreach($categories as $cat)
                    {
                        $product_id_array[] = $cat->products_id;	
                    }
                    if(!empty($product_id_array))
                        $query->whereIn('products_id', $product_id_array);
                    else
                        return $this->response(array('statusCode'=>400, 'statusDescription'=>'Not Found'));
                }
		elseif (Input::has('attribute'))
		{  
                    $iattribute= Input::get('attribute');
                    $attribute_array = explode(',', $iattribute);
                    $attributes  = array();
                    $attributes = DB::table('bn_products_attributes')->whereIn('options_values_id', $attribute_array)->get();
                    $product_id_array =array();
                    foreach($attributes as $attr)
                    {
                        $product_id_array[] = $attr->products_id;	
                    }
                    
                    if(!empty($product_id_array))
                        $query->whereIn('products_id', $product_id_array);
                    else
                        return $this->response(array('statusCode'=>400, 'statusDescription'=>'Not Found'));
		}	
        
			
                // When we get price 
                $iPrice = Input::get('price');
                if($iPrice) $query->where('products_price', '<=', $iPrice);

                // When we get Condition 
                $condition = Input::get('condition');
                if($condition) $query->where('condition',  $condition);
				
				
				// When we get brand 
                $brand = Input::get('brand');
                if($brand) $query->where('brand',  $brand);
				
				
				// When we get gender 
                $gender = Input::get('gender');
                if($gender) $query->where('gender',  $gender);
				
				// When we get Age Range 
                $age_range = Input::get('age_range');
                if($age_range) $query->where('age_range',  $age_range);
				
				// When we get Freshly saved 
                $freshly_faved = Input::get('freshly_faved');
                if($freshly_faved) $query->where('freshly_faved',  $freshly_faved);

                // When we get order by 
                $order_by = Input::get('order');
                if($order_by) { $query->orderBy('created_at', $order_by); }
              //  else {	$query->orderBy('created_at', 'desc'); }
				
				$price_sort = Input::get('price_sort');
                if($price_sort) { $query->orderBy('products_price', $price_sort); }


                $products = $query->where('products_status','1')->paginate(30);//->get();
                
                if($products)
                {
                    $products = $this->product_add_detail($products);
                    return $this->response(array('statusCode'=>100, 'statusDescription'=>'Success', 
                                                'products' => json_decode ($products->toJson(),true)));
                }
                else
                    return $this->response(array('statusCode'=>400, 'statusDescription'=>'Not Found'));
        }
		
		
    
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
			
            $rules = array(
                'category'   => 'required|Integer',
				'condition'   => 'required|Integer',
                'name'          => 'required',
                'description'   => 'required',
                'donor'         => 'required|Integer',
                'orignal_price' => 'required|Numeric',
                'price'         => 'required|Numeric',
                'image'         => 'required',
                'attributes'    => 'required'
            );
            
            $validator = Validator::make(Input::all(), $rules);
            
            if ($validator->fails()) 
            {
                return $this->response(array(
                    'statusCode' => 400,
                    'statusDescription' => 'Bad Request',
                    'errors' =>$validator->messages()->toJson())
                );
            }
            else
            {
              
			  
                $iGetInstitute = Donor::where('user_id' , Input::get('donor'))->first();


                $product = new Product(
                array(  
                    'products_price'        =>  Input::get('price'), 
                    'products_orginal_price'=>  Input::get('orignal_price'), 
                    'brand'                 =>  Input::get('brand',NULL),
                    'products_status'       =>  Input::get('products_status',0),
                    'products_donar'        =>  Input::get('donor'),
                    'condition'             =>  Input::get('condition'),
                    'products_image'        =>  Input::get('image'),
                    'gender'                =>  Input::get('gender'),
                    'age_range'             =>  Input::get('age_range'),
                    'freshly_faved'         =>  Input::get('freshly_faved'),
                    'collections_id'         =>  Input::get('collections_id'),
					'seasons_id'         	=>  Input::get('seasons_id'),
					'types_id'       	    =>  Input::get('types_id'),
                     'institution_id'        =>  $iGetInstitute->institution_id
                ));
                
                $product->save();
                
                $category = Categories::find(Input::get('category'));
                
                $product->productCategory()->save($category);
                
                if(Input::has('tags'))
                {
                    $inptags    = Input::get("tags");
                    $tags       = explode(",", $inptags);
                    
                    foreach ($tags as $key => $value) {
                        
                        $ctag = Tags::find($value);
                        if($ctag)
                            $product->productTags()->save($ctag);
                    }
                }        
                
                
                $attributes = json_decode(Input::get("attributes"),TRUE);
                
                //$attribs = array();
              
                foreach ($attributes as $key => $value) {
                    $opt_values = explode(",", $value);
                    foreach($opt_values as $optval)
                    {
                        //echo $key." ".$optval."<br/>";
                        $attribute = new ProductsAttributes(array('options_id' => $key, 'options_values_id' => $optval ));  
                        $product->productsAttributes()->save($attribute);
                    }
                }
                
                $description = new ProductsDescription(array("products_name" => Input::get("name"), "products_description" => Input::get("description")));
                
                $product->productsDescription()->save($description);
                
                return $this->response(array('statusCode'=>100, 'statusDescription'=>'Success'));
            }
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
            $product = Product::find($id);
            if($product)
            {
                $attributes     = $product->productsAttributes;
                $description    = $product->productsDescription;
                $donar          = $product->donar; 
                $category       = $product->productCategory;
                $tags 			= $product->productTags;
				$brand 			= $product->productBrand;
				$condition 			= $product->productCondition;
                
                if(!empty($attributes))
                foreach($attributes as $attribute)
                {
                    if($attribute->options_id)
                        $attribute->option_name         = ProductsOptions::find($attribute->options_id)->products_options_name;
                    
                    if($attribute->options_values_id)
                        $attribute->option_value_name   = ProductsOptionsValues::find($attribute->options_values_id)->products_options_values_name;
                }
                
                return $this->response(array('statusCode'=>100, 'statusDescription'=>'Success', 
                                            'product' => json_decode ($product->toJson(),true), 'school'=>$school));
            }
            else
                return $this->response(array('statusCode'=>400, 'statusDescription'=>'Not Found'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		
		    $rules = array(
                'category'   => 'required|Integer',
				'condition'   => 'required|Integer',
                'name'          => 'required',
                'description'   => 'required',
                'donor'         => 'required|Integer',
                'orignal_price' => 'required|Numeric',
                'price'         => 'required|Numeric',
                'image'         => 'required',
                'attributes'    => 'required'
            );
            
            $validator = Validator::make(Input::all(), $rules);
            
            if ($validator->fails()) 
            {
                return $this->response(array(
                    'statusCode' => 400,
                    'statusDescription' => 'Bad Request',
                    'errors' =>$validator->messages()->toJson())
                );
            }
            else
            {
              
			  
                $iGetInstitute = Donor::where('user_id' , Input::get('donor'))->first();


                $product = Product::find($id)->update(
                array(  
                    'products_price'        =>  Input::get('price'), 
                    'products_orginal_price'=>  Input::get('orignal_price'), 
                    'brand'                 =>  Input::get('brand',NULL),
                    'products_status'       =>  Input::get('products_status',1),
                    'products_donar'        =>  Input::get('donor'),
                    'condition'             =>  Input::get('condition'),
                    'products_image'        =>  Input::get('image'),
                    'gender'                =>  Input::get('gender'),
                    'age_range'             =>  Input::get('age_range'),
					 'collections_id'         =>  Input::get('collections_id'),
					'seasons_id'         	=>  Input::get('seasons_id'),
					'types_id'       	    =>  Input::get('types_id'),
                    'freshly_faved'         =>  Input::get('freshly_faved'),
                    'institution_id'        =>  $iGetInstitute->institution_id
                ));
                
//                $product->save();
                
                $category = Categories::find(Input::get('category'));
                
				$product = Product::find($id);
                //$product->productCategory()->save($category);
               Product::find($id)->productCategory()->detach();//updateExistingPivot($category->categories_id, array("categories_id"=>$category->categories_id),false);
			   $product->productCategory()->save($category);
			    
                if(Input::has('tags'))
                {
                    Product::find($id)->productTags()->detach();
					$product = Product::find($id);
					
                    $inptags    = Input::get("tags");
                    $tags       = explode(",", $inptags);
                    
                    foreach ($tags as $key => $value) {
                        
                        $ctag = Tags::find($value);
                        if($ctag)
                          {
							    $product->productTags()->save($ctag);
						  }
                    }
                }        
                
                
                
                $attributes = json_decode(Input::get("attributes"),TRUE);
                
                //$attribs = array();
                ProductsAttributes::where("products_id",$id)->delete();
                foreach ($attributes as $key => $value) {
                    $opt_values = explode(",", $value);
                    foreach($opt_values as $optval)
                    {
                        //echo $key." ".$optval."<br/>";
                        $attribute = new ProductsAttributes(array('options_id' => $key, 'options_values_id' => $optval ));  
                        $product->productsAttributes()->save($attribute);
                    }
                }
                
                $description = ProductsDescription::find($id)->update(array("products_name" => Input::get("name"), "products_description" => Input::get("description")));
                
                //$product->productsDescription()->save($description);
                
                return $this->response(array('statusCode'=>100, 'statusDescription'=>'Success'));
            }
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
	
		Product::where('products_id', '=', $id)->delete();
		ProductsAttributes::where('products_id', '=', $id)->delete();
                ProductsDescription::where('products_id', '=', $id)->delete();
                
											
		return $this->response(array(
                    'statusCode' => 100,
                    'statusDescription' => 'Success',
                    'message' =>"Product Deleted Successfully")
                );
	
	}
        
	public function product_add_detail($products)
        {
            foreach($products as $product)
            {
                $attributes     = $product->productsAttributes;
                $description    = $product->productsDescription;
                $donar          = $product->donar;
                $category       = $product->productCategory;
                $brand 			= $product->productBrand;
                $tags 			= $product->productTags;
                
                if(!empty($attributes))
                foreach($attributes as $attribute)
                {
                    if($attribute->options_id)
                        $attribute->option_name         = ProductsOptions::find($attribute->options_id)->products_options_name;
                    
                    if($attribute->options_values_id)
                        $attribute->option_value_name   = ProductsOptionsValues::find($attribute->options_values_id)->products_options_values_name;
                }
                
            }
            return $products;
        }	
		
 	
		
	public function searching($term)
        {
            $condition      = array("New" => 1, "Perfect" => 2, "Good"=> 3, "Blemist" => 4 );
            $cond_prod_ids  = array();
            $product_ids    = array();
            $pro_ids        = array();
            $brand_prod_ids  = array();
            $cat_prod_ids  = array();
            $tag_prod_ids  = array();
            //Search by product condition
            if(in_array($term, $condition))
            {
                $cond_prod_ids = Product::where("condition", "=", $condition[$term])->lists('products_id');
            }
            
            
            //Search by product attribute name
            $product_values_ids = ProductsOptionsValues::where('products_options_values_name', 'LIKE', '%'.$term.'%')->lists('products_options_values_id');
            
            if($product_values_ids)
                $product_ids = DB::table('bn_products_attributes')->whereIn('options_values_id', $product_values_ids)->lists('products_id');
            
            
            //Search by product name or description
            $pro_ids = ProductsDescription::where('products_name', 'LIKE', '%'.$term.'%')
                                            ->orWhere('products_description', 'LIKE', '%'.$term.'%')
                                            ->lists('products_id');
            
            
            //Search by product brand
            $brand_ids = Brands::where('brand_name', 'LIKE', '%'.$term.'%')->lists('brands_id'); 
                    
            if($brand_ids)
            {
                $brand_prod_ids = Product::whereIn("brand", $brand_ids)->lists('products_id');
            }
            
            
            //Search product by category
            $cat_ids = Categories::where('title', 'LIKE', '%'.$term.'%')->lists('categories_id');
            if($cat_ids)
                $cat_prod_ids = DB::table('bn_products_to_categories')->whereIn('categories_id', $cat_ids)->lists('products_id');
            
            
            //Search product by tags 
            $tag_ids = Tags::where('title', 'LIKE', '%'.$term.'%')->lists('tags_id');
            if($tag_ids)
                $tag_prod_ids = DB::table('bn_products_to_tags')->whereIn('tags_id', $tag_ids)->lists('products_id');
            
            $result_prod_ids = array_merge($product_ids, $cond_prod_ids, $pro_ids, $brand_prod_ids, $cat_prod_ids, $tag_prod_ids);
            
            $products = Product::whereIn('products_id',$result_prod_ids)->where('products_status','1')->paginate(15);//->get();
                
                if($products)
                {
                    $products = $this->product_add_detail($products);
                    return $this->response(array('statusCode'=>100, 'statusDescription'=>'Success', 
                                                'products' => json_decode ($products->toJson(),true)));
                }
                else
                    return $this->response(array('statusCode'=>400, 'statusDescription'=>'Not Found'));
        }
}