<?php
	namespace Controller;

	use \W\Controller\Controller;
	use \Manager\UserManager;
	use \W\security\authentificationManager

	class UserController extends Controller
	{

		public function logout()
		{
			$am = new authentificationManager();
			$am->logUserOut();
			$this->redirectToRoute('login');
		}

		public function login()
		{
			$am = new authentificationManager();
			$error="";
			$username="";
			$date = [];

			// traitement du formulaire
			if(!empty($_POST)){
				$username = $_POST['username'];
				$password = $_POST['password'];

				$result = $am->isValidLoginInfo($username, $password);

				// si valide : connexion
				if($result > 0){
					$userId = $result;

					// récuper l'utilisateur
					$UserManager = new \Manager\UserManager();
					$user = $UserManager->find($userId);

					// connexion l'user
					$am->loginUserIn($user);

					// redirection

					$this->redirectToRoute('show_all_terms');

					
				}else{
					$error = "Mauvais identifiants !";
				}
			}

			$date['error'] = $error;
			$data['username'] = $username;
			$this->show('user/login', $data)
		}
		public function register()
		{
			$this->allowTo('admin');
			$user = $this->getUser();

			

			$UserManager = new UserManager();
			$error = "";
			$username = "";
			$email = "";

			// formulaire d'inscription
			 // if(!empty($_POST)){
			// 	foreach ($$_POST as $k => $v) {
			// 		// crée une variable $username, $email, $password, etc...
			// 		$$k = trim(strip_tags($v));
			// }

			if(!empty($_POST)){
				$username 			=trim(strip_tags($_POST['username']));
				$email 				=trim(strip_tags($_POST['email']));
				$password 			=trim(strip_tags($_POST['password']));
				$password_confirm 	=trim(strip_tags($_POST['password_confirm']));
				/* validation */
					// username assez long
					if(strlen($username) < 4){
						$error = "pseudo trop court !";
					}

					// email
					if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
						$error = "email non valide !";
					}

					// email present

					// mot de passe correspondent
					if($password != $password_confirm){
						$error = "Les mots de passe ne correspondent pas !";
					}
				/* fin de la validation */

					// si valide...
						if(empty($error)){

						}
						// hacher le mot de passe 
						$hashedPassword  = password_hash($password, PASSWORD_DEFAULT);
						
						$newAdmin = [
									"username" 		=> $username,
									"email" 		=> $email,
									"password" 		=> $hashedPassword,
									"role" 			=> "admin",
									"dateCreated"	=> date("Y-m-d H:i:s"),
									"dateModied" 	=> date("Y-m-d H:i:s"),
						];
						//inseérrer en base
						$UserManager->insert($newAdmin);

						//afficher bravo ou rediriger ou faire quelque 

					//si invalide..
					   //envoyer les erreurs et les données soumises à la vue 
			}
			$dataToPassToTheView = [
				"error" 	=> $error,
				"username" 	=> $username,
				"email" 	=> $email
			];	
			$this->show('user/register_administrator', $dataToPassToTheView);
		}

	}