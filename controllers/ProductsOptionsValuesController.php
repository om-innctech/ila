<?php

class ProductsOptionsValuesController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
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
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
            $optionvalue = ProductsOptionsValues::find($id);
            if($optionvalue)
                return $this->response(array('statusCode'=>100, 'statusDescription'=>'Success', 'option' => json_decode ($optionvalue->toJson(),true)));
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
                'products_options_values_name'   => 'required'
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
                $option = ProductsOptionsValues::find($id);
                
                if($option)
                {
                    $option->products_options_values_name = Input::get('products_options_values_name');

                    $option->save();
                
                    return $this->response(array('statusCode'=>100, 'statusDescription'=>'Success'));
                }
                else {
                    return $this->response(array('statusCode'=>400, 'statusDescription'=>'Not Found'));
                }

                
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
		
		ProductsOptionsValues::where('products_options_values_id', '=', $id)->delete();
											
 		return $this->response(array(
                    'statusCode' => 100,
                    'statusDescription' => 'Success',
                    'message' =>"Product Option Value Deleted Successfully")
                );
		
		
		//
	}

}