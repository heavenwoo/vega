<?php

namespace Vega\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entity
 */
abstract class Entity
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    protected $updatedAt;

    /**
     * Get id
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param string $column
     * @param int $amount
     * @return bool
     */
    public function increment(string $column, int $amount = 1)
    {
        return $this->incrementOrDecrement($column, $amount, 'increment');
    }

    /**
     * @param string $column
     * @param int $amount
     * @return bool
     */
    public function decrement(string $column, int $amount = 1)
    {
        return $this->incrementOrDecrement($column, $amount, 'decrement');
    }

    /**
     * @param string $column
     * @param int $amount
     * @param string $method
     * @return bool
     */
    private function incrementOrDecrement(string $column, int $amount, string $method)
    {
        $this->{$column} = $this->{$column} + ($method == 'increment' ? $amount : ($amount * - 1));

        return true;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return void
     */
    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return void
     */
    public function setUpdatedAt(\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @ORM\PrePersist()
     */
    public function PrePersist()
    {
        if ($this->createdAt == null) {
            $this->setCreatedAt(new \DateTime());
        }

        $this->setUpdatedAt($this->getCreatedAt());
    }

    /**
     * @ORM\PreUpdate()
     */
    public function PreUpdate()
    {
        $this->setUpdatedAt(new \DateTime());
    }
}
