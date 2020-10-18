<?php
/**
 * Project: ShoppingLists
 * File: ResetPasswordController.php
 * Author: Ndifreke Ekott
 * Date: 09/07/2020 22:40
 *
 */

namespace App\Controller;


use App\Exceptions\LetsGoShoppingException;
use App\Forms\ChangePasswordType;
use App\Forms\Model\ChangePasswordForm;
use App\Repository\UserRepository;
use App\Services\ResetUserPasswordService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ResetPasswordController extends AbstractController
{
    const PASSWORD_RESET_TEMPLATE = 'emails/user/password_reset_form.html.twig';

    const PASSWORD_CHANGE_SUCCESSFUL = 'emails/user/password_changed_success.html.twig';

    private ResetUserPasswordService $service;

    public function __construct(
        ResetUserPasswordService $service
    ){
        $this->service = $service;
    }

    /**
     * @param Request $request
     * @param string $token
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/web/reset-password/{token}", name="reset_password_page", methods={"GET", "POST"})
     */
    public function __invoke(Request $request, string $token)
    {
        try {
            $form = $this->getForm($request, $token);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $model = $form->getData();
                $this->service->resetPassword($model);

                return $this->render(self::PASSWORD_CHANGE_SUCCESSFUL, []);
            }

            return $this->render(self::PASSWORD_RESET_TEMPLATE, [
                'form' => $form->createView()
            ]);

        } catch (LetsGoShoppingException $ex) {
            return new Response('invalid token', 400);
        }
    }

    private function getForm($request, $token)
    {
        $model = new ChangePasswordForm();

        if ($request->isMethod('GET')) {
            $model->setToken($token);
        }

        return $this->createForm(ChangePasswordType::class, $model);
    }
}