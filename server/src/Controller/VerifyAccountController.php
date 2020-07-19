<?php
/**
 * Project: ShoppingLists
 * File: VerifyAccountController.php
 * Author: Ndifreke Ekott
 * Date: 19/07/2020 11:44
 *
 */

namespace App\Controller;


use App\Exceptions\LetsGoShoppingException;
use App\Services\VerifyUserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VerifyAccountController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    private $service;

    public function __construct(VerifyUserService $service)
    {
        $this->service = $service;
    }

    /**
     * @param Request $request
     * @param $token
     * @Route("/user/verify/{token}", name="user_verify", methods={"GET"})
     */
    public function __invoke(Request $request, $token)
    {
        //TODO: we can move this into a comfortable twig template with a nice display.
        try {
            $this->service->verify($token);
            return new Response("Verified");

        } catch (LetsGoShoppingException $ex) {
            return new Response('Invalid token');
        }
    }
}