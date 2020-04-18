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

	    $profile = $wykop->doRequest('profile/index/'.$connect_data['login']);
	    if(!$wykop->isValid()){
		    throw new Exception($this->api->getError());
	    }else{
		$answer = $wykop->doRequest('user/login', array('login' => $profile['login'], 'accountkey' => $session->get('token')));
		if(!$wykop->isValid()) throw new Exception ($this->api->getError());
			
		$roles = ['ROLE_USER_WYKOP'];
		
		if($profile['login'] === 'anonim1133')
		    $roles[] = 'ROLE_ADMIN';
		
		$token = new UsernamePasswordToken($profile['login'], $answer['userkey'], 'wykop', $roles);
		$token->setAttribute('wykop_login', $profile['login']);
		$token->setAttribute('wykop_sex', $profile['sex']);
		$token->setAttribute('wykop_group', $profile['author_group']);
		$token->setAttribute('wykop_avatar', $profile['avatar_med']);
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
