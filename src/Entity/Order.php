<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrderRepository")
 * @ORM\Table(name="`order`")
 */
class Order
{
    /**
     * order just created
     */
    const NEW = 1;

    /**
     * we noticed order and started processing it
     */
    const ACKNOWLEDGE = 2;

    /**
     * order is passed to the delivery service
     */
    const SHIPPED = 3;

    /**
     * order got to the destination point
     */
    const DELIVERED = 4;

    /**
     * customer got the product
     */
    const COMPLETED = 5;

    /**
     * order invalid
     */
    const REJECTED = 6;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $completed_at;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\OrderItem", mappedBy="parent_order", orphanRemoval=true)
     */
    private $orderItems;

    public function __construct()
    {
        $this->orderItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getStatusLabel(): string
    {
        switch ($this->status) {
            case self::NEW :
                return 'New';
            case self::ACKNOWLEDGE :
                return 'Acknowledged';
            case self::SHIPPED :
                return 'Sent to delivery';
            case self::DELIVERED :
                return 'Delivered to destination';
            case self::COMPLETED :
                return 'Got to the customer';
            case self::REJECTED :
                return 'Rejected';
            default:
                return 'Unknown';
        }
    }

    public function getCompletedAt(): ?\DateTimeInterface
    {
        return $this->completed_at;
    }

    public function setCompletedAt(?\DateTimeInterface $completed_at): self
    {
        $this->completed_at = $completed_at;

        return $this;
    }

    /**
     * @return Collection|OrderItem[]
     */
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }

    public function addOrderItem(OrderItem $orderItem): self
    {
        if (!$this->orderItems->contains($orderItem)) {
            $this->orderItems[] = $orderItem;
            $orderItem->setParentOrder($this);
        }

        return $this;
    }

    public function removeOrderItem(OrderItem $orderItem): self
    {
        if ($this->orderItems->contains($orderItem)) {
            $this->orderItems->removeElement($orderItem);
            // set the owning side to null (unless already changed)
            if ($orderItem->getParentOrder() === $this) {
                $orderItem->setParentOrder(null);
            }
        }

        return $this;
    }
}
