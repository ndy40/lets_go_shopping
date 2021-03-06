<?php
/**
 * Project: ShoppingLists
 * File: SignUpDataTransformer.php
 * Author: Ndifreke Ekott
 * Date: 27/06/2020 17:07
 *
 */

namespace App\DataTransformers;


use App\Requests\SignUpRequests;
use App\Services\HydratorService;

class SignUpDataTransformer implements \ApiPlatform\Core\DataTransformer\DataTransformerInterface
{
    private $hydratorService;

    public function __construct(HydratorService $hydratorService)
    {
        $this->hydratorService = $hydratorService;
    }

    /**
     * @inheritDoc
     */
    public function transform($from, string $to, array $context = [])
    {
        $user = new User();
        $this->hydratorService->hydrate($from, $user, $context);

        return $user;
    }

    /**
     * @inheritDoc
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof User) {
            return false;
        }

        return User::class === $to
            && null !== ($context['input']['class'] ?? null)
            && $context['input']['class'] === SignUpRequests::class;
    }
}