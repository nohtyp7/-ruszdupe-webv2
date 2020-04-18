<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class AuthController extends Controller
{
    /**
     * @Route("/login", name="login")
     * @Template()
     */
    public function logInAction()
    {
	$wykop = $this->get('WykopApi');
	//redirects to wykop
        return $this->redirect($wykop->getConnectUrl($this->container->getParameter('app_url') . '/checkIn'));
    }

    /**
     * @Route("/checkIn", name="loginCheck")
     * @Template()
     */
    public function checkInAction()
    {
	if(isset($_GET['connectData'])){//Jeżeli są dane, to loguje
	    $wykop = $this->get('WykopApi');
	    $connect_data = $wykop->handleConnectData();

	    $session = new Session();
	    $session->set('token', $connect_data['token']);
	    $session->set('sign', $connect_data['sign']);

	    $profile = $wykop->doRequest('Profiles/Index/'.$connect_data['login']);
	    if(!$wykop->isValid()){
		    throw new \Exception($wykop->getError());
	    }else{
		// $answer = $wykop->doRequest('user/login', array('login' => $profile['data']['login'], 'accountkey' => $session->get('token')));
		// if(!$wykop->isValid()) throw new \Exception ($wykop->getError());
			
		$roles = ['ROLE_USER_WYKOP'];
		
		if($profile['data']['login'] === 'anonim1133')
		    $roles[] = 'ROLE_ADMIN';
		
		$token = new UsernamePasswordToken($profile['data']['login'], $session->get('token'), 'wykop', $roles);
		$token->setAttribute('wykop_login', $profile['data']['login']);
		$token->setAttribute('wykop_sex', $profile['data']['sex']);
		$token->setAttribute('wykop_group', $profile['data']['rank']);
		$token->setAttribute('wykop_avatar', $profile['data']['avatar']);
		$token->setAttribute('wykop_login_date', new \DateTime('now'));
		
		$this->get('security.token_storage')->setToken($token);
		$session->set('_security_main',  serialize($token));
	    }
	}
	return $this->redirect('/');
    }
    
    /**
     * @Route("/signUp")
     * @Template()
     */
    public function signUpAction()
    {
        return array(
                // ...
            );    }

    /**
     * @Route("/signOut")
     * @Template()
     */
    public function signOutAction()
    {
        return array(
                // ...
            );    }

}
