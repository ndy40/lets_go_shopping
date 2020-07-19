<?php
/**
 * Project: ShoppingLists
 * File: ChangePasswordDataTransformer.php
 * Author: Ndifreke Ekott
 * Date: 03/07/2020 21:59
 *
 */

namespace App\DataTransformers;


use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Validator\Exception\ValidationException;
use App\Entity\User;
use App\Requests\ChangePasswordRequests;
use App\Providers\ApiTokenResolver;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ChangePasswordDataTransformer implements DataTransformerInterface
{
    private $tokenResolver;

    private $encoder;

    private $validationService;

    public function __construct(
        ApiTokenResolver $tokenResolver,
        UserPasswordEncoderInterface $encoder,
        ValidatorInterface $validationService
    ){
        $this->tokenResolver = $tokenResolver;
        $this->encoder = $encoder;
        $this->validationService = $validationService;
    }


    public function transform($object, string $to, array $context = [])
    {
        $this->validate($object);
        $user = $this->tokenResolver->getUser();
        $encodedPassword = $this->encoder
            ->encodePassword($user, $object->password);

        $user->setPassword($encodedPassword);

        return $user;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof User) {
            return false;
        }

        return User::class === $to
            && null !== ($context['input']['class'] ?? null)
            && $context['input']['class'] === ChangePasswordRequests::class;
    }

    private function validate($object)
    {
        $violations = $this->validationService->validate($object);

        if ($violations->count() > 0) {
            throw new ValidationException($this->serializeViolations($violations));
        }

        return true;
    }

    private function serializeViolations(ConstraintViolationListInterface $list)
    {
        $data = [];
        foreach ($list as $item) {
            $data[] = $item->getMessage();
        }

        return implode(',', $data);
    }
}