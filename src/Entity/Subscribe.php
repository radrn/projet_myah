<?php

namespace App\Entity;

use App\Repository\SubscribeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubscribeRepository::class)]
class Subscribe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'subscribes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $follower = null;

    #[ORM\ManyToOne(inversedBy: 'subscribeFolloweds')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $followed = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFollower(): ?User
    {
        return $this->follower;
    }

    public function setFollower(?User $follower): static
    {
        $this->follower = $follower;

        return $this;
    }

    public function getFollowed(): ?User
    {
        return $this->followed;
    }

    public function setFollowed(?User $followed): static
    {
        $this->followed = $followed;

        return $this;
    }
}
