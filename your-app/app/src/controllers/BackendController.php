<?php
namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class BackendController extends BaseController
{
	protected $logger;
    protected $BackendModel;
	protected $view;
    
    public function __construct($logger, $BackendModel, $view)
    {
        $this->logger = $logger;
        $this->BackendModel = $BackendModel;
		$this->view = $view;
    }

//Backend sign_up
	public function signup_proc(Request $request, Response $response, $args)
    {
		//Get the User's info in sign_up page
		$info = [];
		$info['email'] = $request->getParsedBody()['id'];
		$info['pw'] = $request->getParsedBody()['password'];
		$info['name'] = $request->getParsedBody()['name'];
		$info['gender'] = $request->getParsedBody()['gender'];		//gender:0 = male, gender:1 = femail
		$info['birth'] = $request->getParsedBody()['birth'];
		$info['emergency_call'] = $request->getParsedBody()['emergency'];

		//Set the user's init setting
		$info['sign_state'] = 1;
		$info['is_admin'] = 1;
		
		//Hashing the password
		$hash = password_hash($info['pw'], PASSWORD_DEFAULT);
		$info['pw'] = $hash;

		//Array of put the result
		$result = [];

		//Run SQL
		//$this->BackendModel->addUser($info);

		//Insert the user's info in DB and Check, is success
		if($this->BackendModel->addUser($info) == 0){
			$result['header'] = "success";
			$result['message'] = "0";
		}else{
			$result['header'] = "fail";
			$result['message'] = "1";
		}

		return $response->withStatus(200)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode($result, JSON_NUMERIC_CHECK));
	}

//Check the user are exsit
	public function check_user(Request $request, Response $response, $args)
    {
		//Get the User's email in sign_up page
		$info = [];
		$info['email'] = $request->getParsedBody()['id'];

		//Array of put the result
		$result = [];

		//Run the SQL
		//$this->BackendModel->getByEmail($info['email']);

		//Insert the user's info in DB and Check, is success
		if($this->BackendModel->getByEmail($info['email']) > 0){
			//Already exist the email, make response 1
			$result['header'] = "Already have account";
			$result['message'] = "1";
		}else{
			//Not exist the eamil, make response 0
			$result['header'] = "None account";
			$result['message'] = "0";	
		}

		return $response->withStatus(200)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode($result, JSON_NUMERIC_CHECK));
		
	}

//Certification check
	public function check_certification(Request $request, Response $response, $args)
    {
		$result['header'] = "Test";
		$result['message'] = "0";	
		

		return $response->withStatus(200)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode($result, JSON_NUMERIC_CHECK));
		
	}

//signin_proc
	public function signin_proc(Request $request, Response $response, $args)
    {
    	//Get the User's info in sign_in page(id, password)
		$info = [];
		$info['email'] = $request->getParsedBody()['id'];
		$info['pw'] = $request->getParsedBody()['pw'];

		//Array of put the result
		$result = [];
		$temp = $this->BackendModel->login($info['email']);
	
		//Insert the user's info in DB and Check, is success
		if(!$temp['hashed']){
			//Account is not exsit
			$result['header'] = "login_account is not exsit";
			$result['message'] = "2";
		}else{	
			if(password_verify($info['pw'], $temp['hashed'])){
				//Login success!!
				$result['header'] = "login_success";
				$result['message'] = "0";
			}else{
				//Login fail!!
				$result['header'] = "login_password_wrong";
				$result['message'] = "1";
			}
		}

		return $response->withStatus(200)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode($result, JSON_NUMERIC_CHECK));
	}
}
