<?php
/**
 * Project: ShoppingLists
 * File: HydratorService.php
 * Author: Ndifreke Ekott
 * Date: 27/06/2020 17:35
 *
 */

namespace App\Services;


use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\Extractor\SerializerExtractor;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;

class HydratorService
{
    public function hydrate(object $from, object $to, array $context = [])
    {
        $destinationAttributes = $this->getAttributes($to, $context);

        $propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
            ->enableExceptionOnInvalidIndex()
            ->getPropertyAccessor();

        foreach ($destinationAttributes as $attribute) {
            if (property_exists($from, $attribute)) {
                $propertyAccessor->setValue($to, $attribute, $propertyAccessor->getValue($from, $attribute));
            }
        }
    }

    private function getAttributes(object $class, array $context = [])
    {
        if (isset($context['groups'])) {
            $serializerClassMetadataFactory = new ClassMetadataFactory(
                new AnnotationLoader(new AnnotationReader())
            );
            $serializerExtractor            = new SerializerExtractor($serializerClassMetadataFactory);

            return $serializerExtractor->getProperties(get_class($class), ['serializer_groups' => $context['groups']]);
        } else {
            $reflectionClass = new ReflectionExtractor();

            return $reflectionClass->getProperties(get_class($class));
        }
    }
}