<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\FollowbtnRuntime;
use App\Twig\Runtime\LikeExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class FollowbtnExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        /**
         * Enregistre la fonction Twig "is_following" et précise à Twig que
         * son exécution se fait avec la méthode "isPostLiked" de la classe Runtime.
         */
        return [
            new TwigFunction(
                'is_following',                                  // Nom de la fonction dans Twig
                [FollowbtnRuntime::class, 'userFollowed']  // Classe Runtime + méthode associée
            ),
        ];
    }
}
