<?php

class ProductsOptionsController extends \BaseController {

        
        public function __construct()
        {
            $this->beforeFilter('auth.token', array('except' => array('index', 'show', 'show_values')));//, array('except' => 'getLogin')

        }
    
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
            return $this->response(array('statusCode'=>100, 'statusDescription'=>'Success', 'options'=>  json_decode(ProductsOptions::all()->toJson(),true)));
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
                'option_name'   => 'required|unique:bn_products_options,products_options_name'
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
                ProductsOptions::create(array('products_options_name' => Input::get('option_name')));
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
            $option = ProductsOptions::find($id);
            if($option)
                return $this->response(array('statusCode'=>100, 'statusDescription'=>'Success', 'option' => json_decode ($option->toJson(),true)));
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
                'option_name'   => 'required|unique:bn_products_options,products_options_name,{{$id}},products_options_id'
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
                $option = ProductsOptions::find($id);
                
                if($option)
                {
                    $option->products_options_name = Input::get('option_name');

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
		 ProductsOptions::where('products_options_id', '=', $id)->delete();
											
											
		return $this->response(array(
                    'statusCode' => 100,
                    'statusDescription' => 'Success',
                    'message' =>"Product Option Deleted Successfully")
                );
	}

        
        public function show_values($id) {
            
            $option = ProductsOptions::find($id);
            
            //print_r($option);
            if($option)
            {
                $op_vals = array();
                foreach($option->productsOptionsValues as $value){
                     //print '<li>' . $value->products_options_values_id . ' ' . $value->products_options_values_name;//print_r($value);//$op_vals[]= ;// $drink->name . ' ' . $drink->pivot->customer_got_drink;
                    //echo("<br/>");
                    $op_vals[$value->products_options_values_id] = $value->products_options_values_name;
                }
                
                return $this->response(array('statusCode'=>100, 'statusDescription'=>'Success', 'option_values' => $op_vals));
            }
            return $this->response(array('statusCode'=>400, 'statusDescription'=>'Not Found'));
        }
        
        public function store_values()
        {
            $rules = array(
                'option_id'     => 'required|Integer',
                'option_value'  => 'required'
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
                //ProductsOptions::create(array('products_options_name' => Input::get('option_name')));
                
                $option_value = new ProductsOptionsValues(array('products_options_values_name' => Input::get('option_value')));
                $option_value->save();
                $option = ProductsOptions::find(Input::get('option_id'));
                
                if($option)
                {
                    //$option->productsOptionsValues()->attach($option_value); //this executes the insert-query
                    ProductsOptions::find(Input::get('option_id'))->productsOptionsValues()->save($option_value);
                    return $this->response(array('statusCode'=>100, 'statusDescription'=>'Success'));
                }
                else
                {
                    return $this->response(array('statusCode'=>400, 'statusDescription'=>'Not Found'));
                }
            }
            //$option_id 
            //$option_value
        }
}