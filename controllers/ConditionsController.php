<?php

class ConditionsController extends \BaseController {
	 
 
	 
    public function index() {
 
   		
		$count = Conditions::count();
		$conditions = array();
		$conditions = Conditions::get();
		 
        
               return $this->response(array(
                                    'statusCode' => 100,
                                    'statusDescription' => "Success",
                                    'count' => $count,
                                    'Conditions' => json_decode($conditions,true)));
	}
		
		
		
	public function create()
	{	}

	 
	public function store()
	{ 
	
 
	$rules = array(
 				'name' 			=> 'required',
                   );
	
	$messages = array(
 				'name.required'		 => 'Name is required.',
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
 			
			$name = Input::get('name');
 			Conditions::insert(array('name' => $name));
 		
		 
			
				return $this->response(array(
                    'statusCode' => 100,
                    'statusDescription' => 'Success',
                    'message' =>"Condition Added Successfully")
                );
			}
			   
	 }

	 
	public function show($id)
	{
		
 		$count = Conditions::where('id', '=', $id)->count();
		$conditions = array();
		$conditions = Conditions::where('id', '=', $id)->get();

               return $this->response(array(
                                    'statusCode' => 100,
                                    'statusDescription' => "Success",
                                    'count' => $count,
                                    'Conditions' => json_decode($conditions,true)));
         	
	 }
	
 	 
	public function edit($id)
	{ }

	 
	public function update($id)
	{ 
	 
 	$rules = array(
 				'name' 			=> 'required',
                   );
	
	$messages = array(
 				'name.required'		 => 'Name is required.',
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
 			
			$name = Input::get('name');
 			Conditions::find($id)
				->update(array('name' => $name));
 		
		 
			
				return $this->response(array(
                    'statusCode' => 100,
                    'statusDescription' => 'Success',
                    'message' =>"Condition Updated Successfully")
                );
			}
			   
	 
	
	
	}

	 
	public function destroy($id)
	{ 
	
	  Conditions::where('id', '=', $id)->delete();
											
											
		return $this->response(array(
                    'statusCode' => 100,
                    'statusDescription' => 'Success',
                    'message' =>"Condition Deleted Successfully")
                );
	
	
	}
	
	 
}