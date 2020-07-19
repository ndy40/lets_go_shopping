<?php
/**
 * Project: ShoppingLists
 * File: TimestampablePrePersistTrait.php
 * Author: Ndifreke Ekott
 * Date: 30/06/2020 08:31
 *
 */

namespace App\Traits;


use Doctrine\ORM\Mapping as ORM;

trait TimestampableTrait
{

    /**
     * @ORM\PrePersist()
     */
    public function onPrePersistTimestamps()
    {
        $currentTime = new \DateTime('now');

        if (property_exists($this, 'createdAt')) {
            $this->setCreatedAt($currentTime);
        }

        if (property_exists($this, 'updatedAt')) {
            $this->setUpdatedAt($currentTime);
        }
    }

    /**
     * @ORM\PreUpdate()
     */
    public function onPreUpdateTimestamps()
    {
        if (property_exists($this, 'updatedAt')) {
            $this->setUpdatedAt(new \DateTime('now'));
        }
    }
}