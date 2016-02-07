<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use CoreBundle\Common\Result;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
    	$result = new Result();

		return new JsonResponse($result->setData(["api_version" => "1.0"])->toArray());
    }

    /**
     * @Route("/foo", name="foo")
     */
    public function fooAction(Request $request)
    {
    	$result = new Result();

		return new JsonResponse($result->setData(["bar" => "baz"])->toArray());
    }
}
