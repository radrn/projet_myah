<?php

namespace App\Twig\Runtime;

use App\Entity\User;
use App\Repository\SubscribeRepository;
use Twig\Extension\RuntimeExtensionInterface;

class FollowbtnRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private SubscribeRepository $subscribeRepository
    )
    {
    }

    public function userFollowed(?User $currentUser, User $targetUser): bool
    {
        if (!$currentUser) {
            return false; // Not connected => can't follow anyone
        }

        $subscribe = $this->subscribeRepository->findOneBy([
            'follower' => $currentUser,
            'followed' => $targetUser,
        ]);

        return $subscribe !== null;
    }
}
