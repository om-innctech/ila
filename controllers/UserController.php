<?php

class UserController extends \BaseController {

    private $ROLE_ADMIN;
	public function __construct()
	{
            $this->ROLE_ADMIN = Config::get('constants.ROLE_ADMIN');
            $this->beforeFilter('auth.token', array('except' => array('store', 'login','verifyEmail', 'forgotPassword')));//, array('except' => 'getLogin')
	}
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		try
		{
			$user = Auth::user();
			if($user->role ==  $this->ROLE_ADMIN)
			{
				$data = User::all();
			}
			else
			{
				$data = Auth::user();		
			}
			if($data)
			{
				return $this->response(array('statusCode'=>100, 'statusDescription'=>'Success', 'data' => $data));
			}
			else
			{
				return $this->response(array('statusCode'=>400, 'statusDescription'=>"Bad Request", "errors" => "No record found."));						
			}
		}
		catch(Exception $e)
		{
			return $this->response(array('statusCode'=>401, 'statusDescription'=>"Bad Request", "errors" => $e->getMessage()));
		}
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
		try
		{
			$rules = array(
				'email'=>'required|email|max:50|unique:users,email',
				'mobile_no'=>'required|numeric',				
				'password'=>'required|alpha_num|between:6,12'	
             );
            $messages = array(
                'mobile_no.required' => 'Mobile no is required.',
				'email.required' => 'Email address is required.',
                'password.required' => 'Password is required.',			
            );
            $validator = Validator::make(Input::all(), $rules, $messages);
            if ($validator->fails()) 
            {              
                $ers = json_decode($validator->messages()->toJson());
				$ermsg = "";
                foreach ($ers as $errs ) {
                    foreach ($errs as $key => $value) {
                        $ermsg .= $value."<br/>";
                    }
                }
                return $this->response(array(
                    'statusCode' => 400,
                    'statusDescription' => 'Bad Request',
                    'errors' => $ermsg )
                );
            }
            else
            {
				$token = hash('sha256',Str::random(10),false);				
				
				$user = new User;
				$user->email = Input::get('email');
				$user->mobile_no = Input::get('mobile_no');
				$user->password = Hash::make(Input::get('password'));  
				$user->api_token = $token;
				$user->expires_at = date('Y-m-d H:i:s', strtotime('+1 week'));              
                if($user->save())
                {   
					$data_arr = array(
							'email' => Input::get("email"),						
							'fname' => "",						
							'password' => Input::get("password")				
							 );							 
					Mail::send('emails.registration_mail_client', $data_arr, function($message)
					{
					  $mail_sub = Config::get('constants.CV_SITE_SHORT_NAME')." Account Details";
					  $message->from(Config::get('constants.CV_EMAIL_FROM'), Config::get('constants.CV_SITE_SHORT_NAME'));
					  $message->to(Input::get('email'), "")->subject($mail_sub);
					});  
				  	$data_arr = array(
								'email' => Input::get("email"),						
								'fname' => "",						
								'password' => Input::get("password")	
								 );	
					Mail::send('emails.registration_mail_admin', $data_arr, function($message)
					{
					  $mail_sub = 'A new persona is Registered ('.Input::get("email").' )';	
					  $message->from(Config::get('constants.CV_EMAIL_FROM'), Config::get('constants.CV_SITE_SHORT_NAME'));
					  $message->to(Config::get('constants.CV_EMAIL_TO'), Config::get('constants.CV_SITE_SHORT_NAME'))->subject($mail_sub);
					}); 
					
			        return $this->response(array(
                        'statusCode' => 100,
						'token' => $token, 
						'user' => $user,
                        'statusDescription' => 'Success',
						'message' => 'Your account is ready, please verify email address.')
                    );
                }
            }
		}
		catch(Exception $e)
		{
			return $this->response(array('statusCode'=>401, 'statusDescription'=>"Bad Request", "errors" => $e->getMessage()));
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
		//
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
		try
		{
			$rules = array(
				'first_name'=>'required|min:2|max:20',
				'last_name'=>'required|min:2|max:20',
				'city'=>'required|max:50',				
				'pincode'=>'required|between:6,10'	
             );
            $messages = array(
                'first_name.required' => 'First name is required.',
				'last_name.required' => 'Last name is required.',
                'city.required' => 'City is required.',
				'pincode.required' => 'Pincode is required.',			
            );
            $validator = Validator::make(Input::all(), $rules, $messages);
            if ($validator->fails()) 
            {              
                $ers = json_decode($validator->messages()->toJson());
				$ermsg = "";
                foreach ($ers as $errs ) {
                    foreach ($errs as $key => $value) {
                        $ermsg .= $value."<br/>";
                    }
                }
                return $this->response(array(
                    'statusCode' => 400,
                    'statusDescription' => 'Bad Request',
                    'errors' => $ermsg )
                );
            }
            else
            {
				$uid = Auth::id();
				
				if($id == $uid)
				{
					$user = Auth::user();					
					$user->fname = Input::get('first_name');
					$user->lname = Input::get('last_name');
					$user->address = Input::get('address');
					$user->colony = Input::get('colony');
					$user->area = Input::get('area');
					$user->city = Input::get('city');
					$user->pincode = Input::get('pincode');					
					$user->save();
					
					$bike = Bike::where('user_id', '=', $uid)
								->where('status', '=', 0, 'AND')
								->orderBy('id', 'desc')
								->first();
					if($bike)
					{				
						$unique_id=$user->city."-".$user->pincode."-";
						for($i=0;$i< 11;$i++)
						{
							$unique_id .= rand(0,9);
						}
						
						$bike->unique_id = $unique_id;						
						$bike->status = 1;
						$bike->qr_code = base64_encode(QrCode::format('png')->size(150)->encoding('UTF-8')->color(150,90,10)->backgroundColor(125,184,'#808040')->generate($unique_id));
						$bike->updatedon = date("Y-m-d H:i:s");	
						$bike->save();						
					}
					
					return $this->response(array('statusCode'=>100, 'statusDescription'=>'Success', 'data' => $user));
					
				}
				else
				{
					return $this->response(array('statusCode'=>400, 'statusDescription'=>"Bad Request", "errors" => "You cannot change profile of others."));
				}
			}
		}
		catch(Exception $e)
		{
			return $this->response(array('statusCode'=>401, 'statusDescription'=>"Bad Request", "errors" => $e->getMessage()));
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
		//
	}

	public function forgotPassword() 
	{		
		try
		{
			$rules = array(
				'email'=>'required|email'            
             );
            $messages = array(
                'email.required' => 'User name is required.'
            );			
			$validator = Validator::make(Input::all(), $rules, $messages);
			if($validator->passes())
			{ 
				$email = Input::get('email');
				$user_data = DB::table('users')->where('email', '=', $email)->first();
				if($user_data)
				{
					$alphanum = "1234567890ABCD123EFGHI456JKLMNOPQ789RSTUV0123WXYZ123456789";
					$password = substr(str_shuffle($alphanum), 0, 10);
					$pwd = Hash::make($password);
					User::where('email', $email)->update(array('password' => $pwd));					
					$data = array(
					  'password' => $password,
					  'fname'   =>  "" 
					);
					
					Mail::send('emails.forgot_password', $data, function($message)
					{
					  $message->from(Config::get('constants.CV_EMAIL_FROM'), Config::get('constants.CV_SITE_SHORT_NAME'));
					  $message->to(Input::get('email'), "")->subject('Password Reset');			  
					}); 
					
					return $this->response(array('statusCode'=>100, 'statusDescription'=>'Success', 'message' => "Your password have been reset. Please check your email."));
				}
				else
				{
					return $this->response(array('statusCode'=>400, 'statusDescription'=>"Bad Request", "errors" => "You have entered incorrect email address."));
				}
			}
			else
			{
				$ers = json_decode($validator->messages()->toJson());
				$ermsg = "";
                foreach ($ers as $errs ) {
                    foreach ($errs as $key => $value) {
                        $ermsg .= $value."<br/>";
                    }
                }
				return $this->response(array('statusCode'=>400, 'statusDescription'=>"Bad Request", "errors" => $ermsg));
			}	
		}
		catch(Exception $e)
		{
			return $this->response(array('statusCode'=>401, 'statusDescription'=>"Bad Request", "errors" => $e->getMessage()));
		}
	}	
	
	public function login()
	{
		try
		{
			$rules = array(
				'email'=>'required|email',
    			'password'=>'required'
             );
            $messages = array(
                'email.required' => 'User name is required.',
                'password.required' => 'Password is required.'
            );
			$validator = Validator::make(Input::all(), $rules, $messages);
			if($validator->passes())
			{
				if(Auth::attempt(array('email'=>Input::get('email'), 'password'=>Input::get('password')))) 
				{									
					//if(Auth::user()->status=='1')
					//{	
						$token = hash('sha256',Str::random(10),false);
						Auth::user()->api_token = $token;
						Auth::user()->expires_at = date('Y-m-d H:i:s', strtotime('+1 week'));		
						Auth::user()->save();
						
						return $this->response(array('statusCode'=>100, 'statusDescription'=>'Success', 'token' => $token, 'user' => Auth::user()));
					/*}
					else
					{
						return $this->response(array('statusCode'=>400, 'statusDescription'=>"Bad Request", "errors"=>"Your account is blocked or deactiveted. Please contact your administrator."));
					}	*/				
				} 
				else{
					return $this->response(array('statusCode'=>400, 'statusDescription'=>"Bad Request", "errors" => "Incorrect username/password."));					
				}	
			}
			else
			{
				$ers = json_decode($validator->messages()->toJson());
				$ermsg = "";
                foreach ($ers as $errs ) {
                    foreach ($errs as $key => $value) {
                        $ermsg .= $value."<br/>";
                    }
                }
				return $this->response(array('statusCode'=>400, 'statusDescription'=>"Bad Request", "errors" => $ermsg));
			}	
		}
		catch(Exception $e)
		{
			return $this->response(array('statusCode'=>401, 'statusDescription'=>"Bad Request", "errors" => $e->getMessage()));
		}
	}  
	
	public function logout()
	{
		try
		{
			Auth::logout();
			return $this->response(array('statusCode'=>100, 'statusDescription'=>'Success', 'message' => "Logged out successfully."));
		}
		catch(Exception $e)
		{
			return $this->response(array('statusCode'=>401, 'statusDescription'=>"Bad Request", "errors" => $e->getMessage()));
		}
	}
	
	public function changePassword() 
	{		
		try
		{
			$rules = array(
                            'new_password'=>'required|alpha_num|between:6,12'            
                        );
                        
                        $messages = array(
                            'new_password.required' => 'New password is required.',
                        );			
			$validator = Validator::make(Input::all(), $rules, $messages);
			if($validator->passes())
			{ 
				$uid = Auth::id();
				
				if(Auth::check())
				{
				
					$user_data = User::find($uid);//DB::table('users')->where('id', '=', $uid)->first();
					if($user_data)
					{
						$new_password = Hash::make(Input::get('new_password'));
						User::where('id', $uid)->update(array('password' => $new_password));					
						return $this->response(array('statusCode'=>100, 'statusDescription'=>'Success', 'message' => "Your password have been changed."));
					}
					else
					{
						return $this->response(array('statusCode'=>400, 'statusDescription'=>"Bad Request", "errors" => "Try after some time."));
					}
				}
				else
				{
					return $this->response(array('statusCode'=>400, 'statusDescription'=>"Bad Request", "errors" => "You cannot change password of others."));
				}
			}
			else
			{
				$ers = json_decode($validator->messages()->toJson());
				$ermsg = "";
                foreach ($ers as $errs ) {
                    foreach ($errs as $key => $value) {
                        $ermsg .= $value."<br/>";
                    }
                }
				return $this->response(array('statusCode'=>400, 'statusDescription'=>"Bad Request", "errors" => $ermsg));
			}	
		}
		catch(Exception $e)
		{
			return $this->response(array('statusCode'=>401, 'statusDescription'=>"Bad Request", "errors" => $e->getMessage()));
		}
	}

	public function verifyEmail($verificationCode="") 
	{		
		try
		{
			$email = base64_decode($verificationCode);
								
			if($email)
			{ 
				$user_data = DB::table('users')->where('email', '=', $email)->first();
				if($user_data)
				{
					User::where('email', $email)->update(array('status' =>1));					
					return $this->response(array('statusCode'=>100, 'statusDescription'=>'Success', 'message' => "Your account successfully activated."));
				}
				else
				{
					return $this->response(array('statusCode'=>400, 'statusDescription'=>"Bad Request", "errors" => "Your account not activated. Please try again."));
				}
			}
			else{
				return $this->response(array('statusCode'=>400, 'statusDescription'=>"Bad Request", "errors" => "Your account not activated. Please try again."));
			}	
		}
		catch(Exception $e)
		{
			return $this->response(array('statusCode'=>401, 'statusDescription'=>"Bad Request", "errors" => $e->getMessage()));
		}
	}
        
        public function search($term)
        {
            $users = User::where("fname","LIKE","%".$term."%")
                        ->orWhere("lname","LIKE","%".$term."%")
                        ->orWhere("email","LIKE","%".$term."%")
                        ->orWhere("mobile_no","LIKE","%".$term."%")
                        ->orWhere("address","LIKE","%".$term."%")
                        ->orWhere("colony","LIKE","%".$term."%")
                        ->orWhere("area","LIKE","%".$term."%")
                        ->orWhere("city","LIKE","%".$term."%")
                        ->orWhere("pincode","LIKE","%".$term."%")
                        ->get();    
                if($users)
                {
                    //$bikes = $this->add_offers_details($offers);
                    return $this->response(array('statusCode'=>100, 'statusDescription'=>'Success', 'data' => $users));
                }
                else
                    return $this->response(array('statusCode'=>400, 'statusDescription'=>'Not Found'));
        }
        
}
