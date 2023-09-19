<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Http\Client;
use Cake\Core\Configure;
/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UsersController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $users = $this->paginate($this->Users);

        $this->set(compact('users'));
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => [],
        ]);

        $this->set(compact('user'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $user = $this->Users->newEmptyEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $this->set(compact('user'));
    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $this->set(compact('user'));
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('The user has been deleted.'));
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    //Login
    public function login()
    {
        if ($this->request->is('post')) {
            $user = $this->Auth->identify();
            $postData = $this->request->getData();
            $recaptchaResponse = $postData['g-recaptcha-response'];

            // Call the verify function to check reCAPTCHA
            if ($this->verifyRecaptcha($recaptchaResponse)) {
                // reCAPTCHA verification successful
                if ($user) {
                    $this->Auth->setUser($user);
                    return $this->redirect(['action' => 'index']);
                } else {
                    $this->Flash->error('Incorrect login or password.');
                }
            } else {
                // Did not pass reCAPTCHA verification
                $this->Flash->error('Incorrect reCAPTCHA verification.');
            }
        }

        // Set the variable to be sent into the view for use in the reCAPTCHA field
        $recaptcha_user = Configure::read('Recaptcha.recaptcha_user');
        $this->set(compact('recaptcha_user'));
    }

    // Define the verifyRecaptcha function
    private function verifyRecaptcha($response)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $key = Configure::read('Recaptcha.key');
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $full_url = $url . '?secret=' . $key . '&response=' . $response . '&remoteip=' . $ip;

        $http = new Client();
        $response = $http->get($full_url);
        $data = $response->getJson();

        return isset($data['success']) && $data['success'] === true;
    }
    //Logout
    public function logout(){
        $this->Flash->success('You are logged out');
        return $this->redirect($this->Auth->logout());
    }
    //Registration
    public function register()
    {
        $user = $this->Users->newEntity($this->request->getData());
        if($this->request->is('post')){
            if($this->Users->save($user)){
                $this->Flash->success('You are registered');
                return $this->redirect(['action' => 'login']);
            } else {
                $this->Flash->error('You are not registered');
            }
        }
        $this->set(compact('user'));
        $this->set('_serialize', ['user']);
    }

    //Allows going to the register form for guests
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        $this->Auth->allow(['register']);
    }
}
