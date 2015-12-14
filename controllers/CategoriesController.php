<?php

class CategoriesController  extends \BaseController {

        public function __construct()
        {
            $this->beforeFilter('auth.token', array('except' => array('index', 'show','show_subcategories')));//, array('except' => 'getLogin')

        }
    
    
 	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
            $Category = Categories::all()->toJson();
                
                return $this->response(array(
                                    'statusCode' => 100,
                                    'statusDescription' => "Success",
                                    'Categorys' => json_decode($Category,true)));
            
	}


 	public function store()
	{
		//
            
            $rules = array(
                'title' => 'sometimes|required|min:2|max:30',
              );

            $messages = array(
                'title.required' => 'Title is required.',
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
				$title = Input::get('title');
				$parent_id = Input::get('parent_id');
				$status = Input::get('status');
				$is_featured = Input::get('is_featured');
                
                Categories::insert(
                    array('title' => $title,'parent_id' => $parent_id,'status' => $status, 'is_featured' => $is_featured,'created_at' => date("Y-m-d H:i:s"), 'updated_at' => date("Y-m-d H:i:s"))
                );
				
				 
				
				return $this->response(array(
                    'statusCode' => 100,
                    'statusDescription' => 'Success',
                    'message' =>"Category Added Successfully")
                );
				
				 
            }
	}	
	
	
	public function show($id)
	{
		
		$Category= Categories::find($id)->toJson();
											 
		  return $this->response(array(
                                        'statusCode' => 100,
                                        'statusDescription' => "Success",
                                          'Categorys' =>  json_decode($Category,true)));
		
	}
	
	public function edit($id)
	{
		//
	}
	
	public function update($id)
	{
		 $rules = array(
                'title' => 'sometimes|required|min:2|max:30',
              );

            $messages = array(
                'title.required' => 'Title is required.',
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
				$title = Input::get('title');
				$parent_id = Input::get('parent_id');
				$status = Input::get('status');
				$is_featured = Input::get('is_featured');
								
								
				Categories::find($id)
				->update(array('title' => $title,'parent_id' => $parent_id, 'is_featured' => $is_featured,  'status' => $status, 'updated_at' => date("Y-m-d H:i:s")));
			 
 				return $this->response(array(
                    'statusCode' => 100,
                    'statusDescription' => 'Success',
                    'message' =>"Category Added Successfully")
                );
			 }
	}
	
	
	public function destroy($id)
	{ 
	
		Categories::where('categories_id', '=', $id)->delete();
											
											
		return $this->response(array(
                    'statusCode' => 100,
                    'statusDescription' => 'Success',
                    'message' =>"Category Deleted Successfully")
                );
	
	
	
		}
	
	
	
	public function postImage()
	{
		$file = Input::file('file'); // your file upload input field in the form should be named 'file'
		echo $file;
		print_r($file);
                
		$destinationPath = 'uploads/'.str_random(8);
		$filename = $file->getClientOriginalName();
		$extension =$file->getClientOriginalExtension(); //if you need extension of the file
		$uploadSuccess = Input::file('file')->move($destinationPath, $filename);
		 
		if( $uploadSuccess ) {
		   return Response::json('path', $destinationPath); // or do a redirect with some message that file was uploaded
		} else {
		   return Response::json('error', 400);
		}
	}

	public function show_subcategories($id) {
		$Category = Categories::where('parent_id', '=', $id)->get();
		
		return $this->response(array(
									'statusCode' => 100,
									'statusDescription' => "Success",
									'categories' =>  json_decode($Category,true)));
	}

}
