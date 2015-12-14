<?php

class BrandsController extends \BaseController {

 
	 
    public function index() {
           
		   
 		$count = Brands::where('status', '=', '1')
                                     ->count();
        $Brand = array();
            
            
        $Brand= Brands::where('status', '=', '1')->get()->toJson();
           
            


              return $this->response(array(
                                    'statusCode' => 100,
                                    'statusDescription' => "Success",
                                    'count' => $count,
                                     'Brands' => json_decode($Brand,true)));
	
		   
		    
            
        }
		
		
		
	public function create()
	{	}

	 
	public function store()
	{ 
	
		
		  $rules = array(
                'brand_name' => 'sometimes|required|min:2|max:30',
				'description' => 'sometimes|required|min:2|max:30',
				'brand_image' => 'sometimes|required|min:2|max:70',
				'brand_logo' => 'sometimes|required|min:2|max:70',
  				
              );

            $messages = array(
                'brand_name.required' => 'Brand Name is required.',
				'description.required' => 'Description is required.',
				'brand_image.required' => 'Brand Image is required.',
				'brand_logo.required' => 'Brand Logo is required.',
 				
             );

            $validator = Validator::make(Input::all(), $rules, $messages);
            
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
                $brand_name = Input::get('brand_name');
                $description = Input::get('description');
                $brand_image = Input::get('brand_image');
				$brand_logo = Input::get('brand_logo');
  
                 Brands::insert(
                    array('brand_name' => $brand_name, 'description' => $description,  'brand_image' => $brand_image,  'brand_logo' => $brand_logo)
                );
				
 		return $this->response(array(
                                'statusCode' => 100,
                                'statusDescription' => 'Success',
                                'message' =>"Brand Added Successfully")
                            );
				
            }
            
	
	
	
	}

	 
	public function show($id)
	{
		
				   
 		$count = Brands::where('brands_id', '=',$id)
                                     ->count();
        $Brand = array();
            
            
        $Brand= Brands::where('brands_id', '=', $id)->get()->toJson();
           
            


              return $this->response(array(
                                    'statusCode' => 100,
                                    'statusDescription' => "Success",
                                    'count' => $count,
                                     'Brands' => json_decode($Brand,true)));
		
		
		 }

	 
	public function edit($id)
	{ }

	 
	public function updatebrands($id)
	{   
	
		$rules = array(
                'brand_name' => 'sometimes|required|min:2|max:30',
				'description' => 'sometimes|required|min:2|max:30',
				'brand_image' => 'sometimes|required|min:2|max:30',
				'brand_logo' => 'sometimes|required|min:2|max:30',
               );

            $messages = array(
                'brand_name.required' => 'Brand Name is required.',
				'description.required' => 'Description is required.',
				'brand_image.required' => 'Brand Image is required.',
				'brand_logo.required' => 'Brand Logo is required.',
 				
             );

            $validator = Validator::make(Input::all(), $rules, $messages);
            
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
				$brand_name = Input::get('brand_name');
                $description = Input::get('description');
                $brand_image = Input::get('brand_image');
				$brand_logo = Input::get('brand_logo');
                
				 Brands::find($id)
				->update(array('brand_name' => $brand_name,'description' => $description,'brand_image' => $brand_image,'brand_logo' => $brand_logo));
			 
 				return $this->response(array(
                    'statusCode' => 100,
                    'statusDescription' => 'Success',
                    'message' =>"Brand Update Successfully")
                );
			 }
	
	
	
	}

	 
	public function destroy($id)
	{ 
	
	Brands::where('brands_id', '=', $id)->delete();
											
											
		return $this->response(array(
                    'statusCode' => 100,
                    'statusDescription' => 'Success',
                    'message' =>"Brands Deleted Successfully")
                );
	
	
	
		}
        
		
		
 	 
}