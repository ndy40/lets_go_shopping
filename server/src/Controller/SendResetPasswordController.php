<?php
/**
 * Project: ShoppingLists
 * File: ResetPasswordController.php
 * Author: Ndifreke Ekott
 * Date: 09/07/2020 20:05
 *
 */

namespace App\Controller;

use App\Services\SendResetPasswordService;
use Psr\Log\LoggerInterface;
use \Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class SendResetPasswordController extends AbstractController
{
    private $service;

    private $logger;

    public function __construct(SendResetPasswordService $service, LoggerInterface $logger)
    {
        $this->service = $service;
        $this->logger = $logger;
    }

    public function __invoke(Request $request)
    {
        try {
            $this->service->resetPassword($request->get('data')->getEmail());
            return new Response(null, 201);

        } catch (\Exception $ex) {
            $this->logger->error($ex);
        }
    }
}