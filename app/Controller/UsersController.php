<?php
App::uses('CakeEmail', 'Network/Email', 'AppController', 'Controller');
/**
 * Users Controller
 *
 * @property User $User
 */

class UsersController extends AppController
{
    public $uses = array('User', 'Role', 'MenuEntry');
    var $components = array('Password', 'Menu');

    /**
     * index method
     *
     * @return void
     */
    public function index()
    {
        $this->User->recursive = 0;
        $this->set('users', $this->paginate());
    }

    /**
     * view method
     *
     * @param string $id
     * @return void
     */
    public function view($id = null)
    {
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        $this->set('user', $this->User->read(null, $id));
    }

    /**
     * add method
     *
     * @return void
     */
    public function register()
    {
        if ($this->request->is('post')) {
            $user = $this->request->data['User'];
            $this->User->create();
            //modify value of 'registered' attribute to current date!
            $now = date('Y-m-d H:i:s');
            $user['registered'] = $now;
            //generate confirmation token
            $token = sha1($user['username'] . rand(0, 100));
            //modify value of 'confirmation_token' attribute to generated token!
            $user['confirmation_token'] = $token;
            //set status to "new"
            $user['status'] = false;
            //set role to "registered"
            $role = $this->Role->findByName('Registered');
            $roleId = $role['Role']['id'];
            $user['role_id'] = $roleId;

            $this->request->data['User'] = $user;
            //save data to database
            if ($this->User->save($user)) {
            	//create email and set header fields and viewVars
            	$email = new CakeEmail();
            	$email->template('user_confirmation', 'email')
            	->emailFormat('html')
            	->to($user['email'])
            	->from('noreply@'.env('SERVER_NAME'))
            	->subject('Registration complete - Please confirm your account')
            	->viewVars(array(
            		'username' => $user['username'],
            		'activationUrl' => 'http://'.env('SERVER_NAME').':'.env('SERVER_PORT').$this->webroot.'users/activateUser/'.$this->User->getLastInsertID().'/'.$user['confirmation_token'],
            		'url' => env('SERVER_NAME'),
            		'confirmationToken' => $user['confirmation_token']
            	))
            	->send();
            	
            	$this->redirect(array('action' => 'index'));
            } else {
                
            }
        }
        $this->set('adminMode', false);
        $this->set('menu', $this->Menu->buildMenu($this, NULL));
        $this->set('systemPage', true);
    }
    
    public function activateUser($userId = null, $tokenIn = null){
    	$this->User->id = $userId;
    	if ($this->User->exists()){
    		$this->User->id = $userId;
    		$userDB = $this->User->findById($userId);
    		$tokenDB = $userDB['User']['confirmation_token'];
    		if ($tokenIn == $tokenDB){
    			// Update the status flag to active
    			$this->User->saveField('status', true);
    			
    			//create email and set header fields and viewVars
    			$anEmail = new CakeEmail();
    			$anEmail->template('user_activated', 'email')
    			->emailFormat('html')
    			->to($userDB['User']['email'])
    			->from('noreply@dualon.de')
    			->subject('User activated')
    			->viewVars(array(
    					'username' => $userDB['User']['username'],
    					'url' => env('SERVER_NAME')
    			))
    			->send();
    			
    			$this->redirect(array('action' => 'login'));
    		} else{
    			//token incorrect exception
    		}
    	} else{
    		//user not exists exception
    		throw new NotFoundException(__('Invalid user'));
    	}
    }

    /**
     * edit method
     *
     * @param string $id
     * @return void
     */
    public function edit($id = null)
    {
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash(__('The user has been saved'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The user could not be saved. Please, try again.'));
            }
        } else {
            $this->request->data = $this->User->read(null, $id);
        }
        $roles = $this->User->Role->find('list');
        $this->set(compact('roles'));
    }

    /**
     * delete method
     *
     * @param string $id
     * @return void
     */
    public function delete($id = null)
    {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        if ($this->User->delete()) {
            $this->Session->setFlash(__('User deleted'));
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('User was not deleted'));
        $this->redirect(array('action' => 'index'));
    }

    /**
     * login function
     *
     * @return void
     */
    function login()
    {
        if ($this->request->is('post')) {
            $userIn = $this->request->data['User'];
            $userDB = $this->User->findByUsername($userIn['username']);
            if($userDB['User']['status'] == true){
            	//if user is already logged in
            	if ($this->Session->read('Auth.User')) {
            		$this->Session->setFlash('You are already logged in!');
            	}
            	//if user isn't already logged in
            	else {
            		//if user is active
            	
            		//user is not active
            		 
            		if ($this->Auth->login()) {
            			//update "last_login"
            			$this->User->id = $this->Auth->user('id');
            			$now = date('Y-m-d H:i:s');
            			$this->User->saveField('last_login', $now);
            	
            			$this->redirect($this->Auth->redirect());
            		} else {
            			$this->Session->setFlash('Your username or password was incorrect.');
            		}
            	}
            } else{
            	
            }
        }
        $this->set('menu', $this->Menu->buildMenu($this, NULL));
        $this->set('adminMode', false);
        $this->set('systemPage', true);
    }

    /**
     * logout function
     *
     * @return void
     */
    function logout()
    {
        $this->redirect($this->Auth->logout());
    }

    /**
     * beforeFilter function
     *
     * @return void
     */
    function beforeFilter()
    {
        parent::beforeFilter();
        $this->Auth->allow('register', 'logout', 'activateUser', 'resetPassword');
        $this->Auth->autoRedirect = false;
    }

    /**
     * resetPassword function
     * Generates a new password and send it per email to the user
     * Passwordlength is 10 characters
     * @return void
     */
    function resetPassword($id = null)
    {
        if($this->request->is('post') || $this->request->is('put')) {
        	$id = $this->request->data['id'];
        }
    	$this->User->id = $id;
        //check if user exist
        if (!$this->User->exists()) {
            throw new NotFoundException('Invalid user');
        }
        $userDB = $this->User->findById($id);

        // Generates a new password (10 characters)
        $newpw = $this->Password->generatePassword(10);
        //Set new password
        $this->User->password = $newpw;

		if ($this->User->save($this->request->data)) {
			//create email and set header fields and viewVars
			$anEmail = new CakeEmail();
			$anEmail->template('password_resetted', 'email')
			->emailFormat('html')
			->to($userDB['User']['email'])
			->from('noreply@'.env('SERVER_NAME'))
			->subject(env('SERVER_NAME').' - Your new password')
			->viewVars(array(
				'username' => $userDB['User']['username'],
				'url' => env('SERVER_NAME')
			))
			->send();
			
			$this->redirect(array('action'=>'login'));
		} else {
			$this->Session->setFlash(('User password was not resetted. You received an email with your new password!'));
		}
    }
    
    function changePassword($userId = null, $newPassword = null){
    	if($this->request->is('post') || $this->request->is('ajax')){
    		$userId = $this->request->data['userId'];
    		$newPassword = $this->request->data['newPassword'];
    	}
    	
    	$this->User->id = $userId;
    	if (!$this->User->exists()) {
            throw new NotFoundException(('Invalid user'));
        }
        
        if($this->User->saveField('password', $newPassword)){
        	
        } else{
        	
        }
    }

    /**
     * changeRole method
     *
     * @param string $id
     * @return void
     */
    function changeRole($id = null, $newRole = null)
    {
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        //
        if ($this->request->is('post') || $this->request->is('put')) {
            //Read user
            $user = $this->request->data['User'];
            //Set new Role
            $role = $this->Role->findById($newRole);
            $roleId = $role['Role']['id'];
            $user['role_id'] = $roleId;
            //
            if ($this->User->save($user)) {
                $this->Session->setFlash(__('The user has been saved'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The user could not be saved. Please, try again.'));
            }
        } else {
            $this->request->data = $this->User->read(null, $id);
        }
    }

}
