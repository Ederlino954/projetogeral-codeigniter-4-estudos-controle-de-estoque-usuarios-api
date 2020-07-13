<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UsersModel; // usado dentro da funçaõ de login

class Users extends BaseController
{
	protected $session; // geral para todos 
	

	// =====================================================
	public function __construct(){
		$this->session = session(); // iniciando a session para ter acesso em qualquer ponto da sessão 
	}

	// =====================================================
	public function index(){ // aciona automaticamente no carregamento da página 
		// check if there is an active session
		if($this->checkSession()){
			// active session
			$this->homePage();
		} else {
			// show login form
			$this->login();
		}
	}

	// =====================================================
	public function login(){

		// check if session exists (if yes goto homepage)
		if($this->checkSession()){ // acesso ou não com sessão ativa (se a sessão existe?)
			$this->homePage();
			return;
		}

		$error = '';
		$data = array();
		$request = \Config\Services::request(); // serviço padrão 

		if($_SERVER['REQUEST_METHOD'] == 'POST'){ // houve ou não submissão do form
			
			// check fields // Preenchimento
			$username = $request->getPost('text_username');
			$password = $request->getPost('text_password');
			if($username == '' || $password == ''){
				$error = "Erro no preenchimento dos campos.";
			}

			// check database // Login 
			if($error == ''){
				$model = new UsersModel(); // carregando o modelo 
				$result = $model->verifyLogin($username, $password); /// executando verificação de login 
				if(is_array($result)){ // se resultado = array
					// valid login
					$this->setSession($result); // função a baixo 
					$this->homePage(); // função a baixo 
					return; // não executa a função até o fim 
				} else {
					// invalid login
					$error = "Login inválido.";
				}
			}
		}

		if($error != ''){
			$data['error'] = $error;
		}
		
		// show the login page // não havendo submissão 
		echo view('users/login', $data);	
	}

	// =====================================================
	private function setSession($data){
		// init session

		$session_data = array(
			'id_user' => $data['id_user'],
			'name' => $data['name'],
			'profile' => $data['profile'] // ADM ou utilizadores padrões 
		);

		$this->session->set($session_data);
	}

	// =====================================================
	public function homePage(){

		// check if session exists // permitindo ou não acesso a área
		if(!$this->checkSession()){ // se a sessão não existe?
			$this->login();
			return;
		}

		// check if user is admin
		$data = array();
		if($this->checkProfile('admin')){
			$data['admin'] = true;
		}
		
		// show homepage view
		echo view('users/homepage', $data);
	}

	// =====================================================
	public function logout(){
		// logout
		$this->session->destroy();		// destruindo sessão
		return redirect()->to(site_url('users')); // redirecionando 
	}

	// =====================================================
	public function recover(){

		// shows form to recover password
		echo view('users/recover_password');
	}

	// =====================================================
	public function reset_password(){

		// método 1 ----------------------------------------
		// // reset users password
		// // redefines the password and sends by email
		// $request = \Config\Services::request();
		// $email = $request->getPost('text_email');

		// // verifies if there is a user with this email
		// // if exists, change the password and send email
		// $users = new UsersModel();		
		// $users->resetPassword($email);	


		// método 2 ----------------------------------------
		/* 
		1. apresenta o formulário para o email > FEITO
		2. vai verificar se o email está associado a uma conta > FEITO
		3. caso esteja associado, cria um purl e envia email com o purl
		4. O link do purl permite aceder a uma área reservada para redefinir nova password		
		*/

		$request = \Config\Services::request(); // serviço padrão 
		$email = $request->getPost('text_email');
		$users = new UsersModel();		
		$result = $users->checkEmail($email); // existencia e email 
		if(count($result) != 0){
			// existe o email associado
			$users->sendPurl($email, $result[0]['id_user']); // indicando o utilizador  

			echo 'existe o email';
		} else {
			// não existe email
			echo 'Não existe o email associado.';
		}
	}

	// =====================================================
	public function redefine_password($purl){ // pegando o purl do sendPurl
		
		/* 
		1. verificar se veio o purl / se existe um purl na bd > FEITO
		2. se existir, vamos apresentar o formulário para alterar a password

			2.1 formulário vai ter 2 inputs
				nova password
				repetir a nova password
			2.2 tratamento da submissão
			2.3 se as passwords forem iguais vai guardar na bd a nova pass
				vai eliminar o purl

		3. não existindo o purl, vai para a página inicial
		*/

		$users = new UsersModel();
		$results = $users->getPurl($purl);
		if(count($results) == 0){ // não existe purl 
			
			// no purl found. Redirects to main
			return redirect()->to(site_url('main'));

		} else { // existe purl 
 
			$data['user'] = $results[0];
			echo view('users/redefine_password', $data);

		}
	}

	// =====================================================
	public function redefine_password_submit(){

		$request = \Config\Services::request(); // padrão 
		$id_user = $request->getPost('text_id_user');
		$nova_password = $request->getPost('text_nova_password');
		$nova_password_repetida = $request->getPost('text_repetir_password');

		$error = '';

		// verify if both passwords match // se as passwords não batem certo 
		if($nova_password != $nova_password_repetida){
			$error = 'As passwords não são iguais.';
			die($error);
		}

		// updates the new password // atualizando a nova password
		if($error == ''){
			$users = new UsersModel();
			$users->redefinePassword($id_user, $nova_password);
			echo "Password redefinida com sucesso.";
			echo '<p><a href="'.site_url('users').'">Voltar</a>';
		}		
	}

	// =====================================================
	public function op1(){
		echo 'Funcionalidade 1';
	}

	// =====================================================
	public function op2(){
		echo 'Funcionalidade 2';
	}

	// =====================================================
	// PRIVATE
	// =====================================================
	private function checkSession(){
		// check if session exists
		return $this->session->has('id_user'); // id de usuário
	}

	// =====================================================
	private function checkProfile($profile){ // devolve verdadeiro ou  falso

		// check if the user has permission to access feature // simples procurnao dtexto dent4ro de outro texto
		if(preg_match("/$profile/", $this->session->profile)){ // preg_match // local onde irá procurar o termo entre aspas // expressões regulares
			return true;
		} else {
			return false;
		}
	}



	// =====================================================
	// ADMIn
	// =====================================================
	public function admin_users(){

		// check if session exists (if yes goto homepage)
		if(!$this->checkSession()){
			$this->homePage();
			return;
		}

		// check if the user has permission // para evitar acesso sem permissão 
		if($this->checkProfile('admin') == false){
			return redirect()->to(site_url('users'));
		}

		// buscar a lista de utilizadores registados
		$users = new UsersModel();
		$results = $users->getUsers(); // busca todos 
		$data['users'] = $results;		
		echo view('users/admin_users', $data);
	}

	// =====================================================
	public function admin_new_user(){
		
		// check if session exists (if yes goto homepage)
		if(!$this->checkSession()){
			$this->homePage();
			return;
		}

		// check if the user has permission
		if($this->checkProfile('admin') == false){
			return redirect()->to(site_url('users'));
		}

		// adds a new user to the database
		$error = '';
		$data = array();
		
		// if there was a submission
		if($_SERVER['REQUEST_METHOD'] == 'POST'){

			// ir buscar os dados do post
			$request = \Config\Services::request();
			$dados = $request->getPost(); // pegando getpost do request  
			
			// verifica se vieram os dados corretos
			if(
				$dados['text_username'] == '' ||
				$dados['text_password'] == '' ||
				$dados['text_password_repetir'] == '' ||
				$dados['text_name'] == '' ||
				$dados['text_email'] == ''
			){
				$error = 'Preencha todos os campos de texto.';
			}

			// verificar se as password coincidem
			if($error == ''){
				if($dados['text_password'] != $dados['text_password_repetir']){
					$error = 'As passwords não coincidem.';
				}
			}

			if($error == ''){
				// verifica se, pelo menos, uma check de profile foi checkada
				if(	!isset($dados['check_admin']) &&
					!isset($dados['check_moderator']) &&
					!isset($dados['check_user'])){
						$error = 'Indique, pelo menos, um tipo de profile.';
					}
			}

			// verificar se já existe um user com o mesmo username ou email			
			$model = new UsersModel();
			if($error == ''){
					$result = $model->checkExistingUser();
				if(count($result)!=0){
					$error = "Já existe um utilizador com esses dados.";
				}
			}

			if($error == ''){								
				$model->addNewUser();								
				return redirect()->to(site_url('users/admin_users'));
			}			
		}

		// check if there is an error
		if($error != ''){
			$data['error'] = $error;
		}

		echo view('users/admin_new_user', $data); // formulario de novo user 

	}

	// =====================================================
	public function admin_edit_user($id_user){

		if($id_user == $this->session->id_user){
			return redirect()->to(site_url('users'));
		}

		// check if session exists (if yes goto homepage)
		if(!$this->checkSession()){
			$this->homePage();
			return;
		}

		// check if the user has permission
		if($this->checkProfile('admin') == false){
			return redirect()->to(site_url('users'));
		}

		$error = '';
		$data = array();

		// if there was a submission
		if($_SERVER['REQUEST_METHOD'] == 'POST'){

			// trata a alteração dos dados do user
			
			// verificar se os campos estão corretos
			$request = \Config\Services::request();
			$dados = $request->getPost();
			
			// verifica se vieram os dados corretos
			if(				
				$dados['text_name'] == '' ||
				$dados['text_email'] == ''
			){
				$error = 'Preencha todos os campos de texto.';
			}

			if($error == ''){
				// verifica se, pelo menos, uma check de profile foi checkada
				if(	!isset($dados['check_admin']) &&
					!isset($dados['check_moderator']) &&
					!isset($dados['check_user'])){
						$error = 'Indique, pelo menos, um tipo de profile.';
					}
			}

			// verificar se existe outro utilizador com os mesmos dados
			$model = new UsersModel();
			if($error == ''){
				$results = $model->checkAnotherUserEmail($dados['id_user']);
				if(count($results) != 0){
					$error = 'Já existe outro utilizador com o mesmo email.';
				}
			}
			
			// guardar (atualizar) os dados na bd
			if($error == ''){
				$model->editUser();
				return redirect()->to(site_url('users/admin_users'));
			}
		}

		// abrir o quadro para edição do utilizador
		$users = new UsersModel();

		// verifica se vieram dados do user
		$user = $users->getUser($id_user);
		if(count($user) == 0 || $user[0]['id_user'] == $this->session->id_user ){
			return redirect()->to(site_url('users/admin_users')); // retornando caso tentem editar sem permissão		
		}

		$data['user'] = $user[0];

		if($error != ''){
			$data['error'] = $error;
		}
		echo view('users/admin_edit_user', $data); // mandando erro para a view 
	}

	// =====================================================
	public function admin_delete_user($id_user, $response = ''){ // response vazio não altera

		if($id_user == $this->session->id_user){
			return redirect()->to(site_url('users'));
		}

		// check if session exists (if yes goto homepage)
		if(!$this->checkSession()){
			$this->homePage();
			return;
		}

		// check if the user has permission
		if($this->checkProfile('admin') == false){
			return redirect()->to(site_url('users'));
		}

		$model = new UsersModel();

		// verifica se veio resposta
		if($response == 'yes'){
			$model->deleteUser($id_user);
			return redirect()->to(site_url('users/admin_users'));
		}
		
		// apresentar quadro para questionar se pretende eliminar user		
		$data['user'] = $model->getUser($id_user)[0];

		echo view('users/admin_delete_user', $data);

	}

	// =====================================================
	public function admin_recover_user($id_user, $response = ''){
		
		if($id_user == $this->session->id_user){ // evitar a edição do próprio ADM logado 
			return redirect()->to(site_url('users'));
		}

		// check if session exists (if yes goto homepage)
		if(!$this->checkSession()){
			$this->homePage();
			return;
		}

		// check if the user has permission
		if($this->checkProfile('admin') == false){
			return redirect()->to(site_url('users'));
		}

		$model = new UsersModel();

		// verifica se veio resposta
		if($response == 'yes'){
			$model->recoverUser($id_user); // recuperação do usuário 
			return redirect()->to(site_url('users/admin_users'));
		}
		
		// apresentar quadro para questionar se pretende recuperar user		
		$data['user'] = $model->getUser($id_user)[0];

		echo view('users/admin_recover_user', $data);

	}


}
