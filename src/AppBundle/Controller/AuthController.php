<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use CoreBundle\Common\Result;

class AuthController extends Controller
{
    /**
     * @Route("/auth/token")
     */
    public function tokenAction(Request $request)
    {
    	$result = new Result();

    	$username = $request->request->get('username');
    	$password = $request->request->get('password');

	    $user = $this->get("core.user_repository")->findOneBy([
	    	"username" => $username
	    ]);

	    if($user == null)
	    {
	    	return new JsonResponse($result->addError("Username was not found.")->toArray());
	    }

	    if($user->getPassword() == $this->get('security.password_encoder')->encodePassword($user, $password))
	    {
	    	$token = $this->get('token_helper')->encode([
	      		'username' => $user->getUsername()
	      	], 600);

	      	return new JsonResponse($result->setData(["token" => $token])->toArray());
	    }else{
	      	return new JsonResponse($result->addError("Incorrect password.")->toArray());
	    }
    }
}