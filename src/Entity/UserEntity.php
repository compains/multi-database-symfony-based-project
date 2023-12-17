<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
class UserEntity {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $username;

    /**
     * @param string $username
     */
    public function __construct(string $username) {
        $this->username = $username;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getUsername(): string {
        return $this->username;
    }
}
