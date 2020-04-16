<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * This is a dummy class to prevent Doctrine from breaking migration over and over again
 *
 * @ORM\Entity(repositoryClass="App\Repository\RemembermeTokenRepository")
 * @ORM\Table(name="rememberme_token")
 */
class RemembermeToken
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=88, unique=true)
     */
    private $series;

    /**
     * @ORM\Column(type="string", length=88)
     */
    private $value;

    /**
     * @ORM\Column(type="datetime")
     */
    private $lastUsed;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $class;

    /**
     * @ORM\Column(type="string", length=200)
     */
    private $username;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSeries(): ?string
    {
        return $this->series;
    }

    public function setSeries(string $series): self
    {
        $this->series = $series;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getLastUsed(): ?\DateTimeInterface
    {
        return $this->lastUsed;
    }

    public function setLastUsed(\DateTimeInterface $lastUsed): self
    {
        $this->lastUsed = $lastUsed;

        return $this;
    }

    public function getClass(): ?string
    {
        return $this->class;
    }

    public function setClass(string $class): self
    {
        $this->class = $class;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }
}
