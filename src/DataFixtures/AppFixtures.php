<?php

namespace App\DataFixtures;

use App\Factory\UserFactory;
use App\Factory\PostFactory;
use App\Factory\CommentFactory;
use App\Factory\CommentLikeFactory;
use App\Factory\PostLikeFactory;
use App\Factory\SubscribeFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserFactory::createMany(100);
        PostFactory::createMany(300);
        CommentFactory::createMany(800);
        SubscribeFactory::createMany(950);
        PostLikeFactory::createMany(600);
        CommentLikeFactory::createMany(400);
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
