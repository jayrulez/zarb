<?php

namespace CoreBundle\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\InMemoryUserProvider;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use CoreBundle\Service\TokenHelper;
use CoreBundle\Common\Result;

class TokenAuthenticator extends AbstractGuardAuthenticator
{

  private $_tokenHelper;

  public function __construct(TokenHelper $tokenHelper) {
    $this->_tokenHelper = $tokenHelper;
  }

  public function getCredentials(Request $request)
  {
    if (!$request->headers->has('Authorization')) {
      if(function_exists('apache_request_headers'))
      {
        $headers = apache_request_headers();
        
        if (isset($headers['Authorization']))
        {
          $request->headers->set('Authorization', $headers['Authorization']);
        }
      }else{
        throw new CustomUserMessageAuthenticationException('Missing Authorization Header');
      }
    }

    $headerParts = explode(' ', $request->headers->get('Authorization'));

    if (!(count($headerParts) === 2 && $headerParts[0] === 'Bearer')) {
      throw new CustomUserMessageAuthenticationException('Malformed Authorization Header');
    }

    return $headerParts[1];
  }

  public function getUser($credentials, UserProviderInterface $userProvider)
  {
    try {
      $payload = $this->_tokenHelper->decode($credentials);
    } catch (InvalidTokenException $e) {
      throw new CustomUserMessageAuthenticationException($e->getMessage());
    } catch (\Exception $e) {
      throw new CustomUserMessageAuthenticationException('Malformed Token');
    }

    if (!isset($payload['username'])) {
      throw new CustomUserMessageAuthenticationException('Invalid Token');
    }

    return $userProvider->loadUserByUsername($payload['username']);
  }

  public function checkCredentials($credentials, UserInterface $user)
  {
    return true;
  }

  public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
  {
    return null;
  }

  public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
  {
    $result = new Result();

    return new JsonResponse($result->addError($exception->getMessage())->toArray(), 403);
  }

  public function start(Request $request, AuthenticationException $exception = null)
  {
    $result = new Result();

    return new JsonResponse($result->addError($exception != null ? $exception->getMessage() : "Authentication required.")->toArray(), 401);
  }

  public function supportsRememberMe()
  {
    return false;
  }
}