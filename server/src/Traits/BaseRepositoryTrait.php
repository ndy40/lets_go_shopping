<?php
/**
 * Project: ShoppingLists
 * File: BaseRepositoryTrait.php
 * Author: Ndifreke Ekott
 * Date: 19/07/2020 12:22
 *
 */

namespace App\Traits;


use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

trait BaseRepositoryTrait
{
    protected function getEm()
    {
        if (!property_exists($this, '_em')) {
            throw new \UnexpectedValueException(
                'Trait cannot find _em property. Can only be supported in class '
                . ServiceEntityRepository::class
            );
        }

        return $this->_em;
    }

    public function create($entity)
    {
        $em = $this->getEm();
        $em->persist($entity);
        $em->flush($entity);

        return $entity;
    }

    public function update($entity)
    {
        $em = $this->getEm();
        $em->merge($entity);
        $em->flush($entity);
    }

    public function delete($entity)
    {
        $em = $this->getEm();
        $em->remove($entity);
        $em->flush();
    }
}