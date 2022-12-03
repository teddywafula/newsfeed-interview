<?php
declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class AppFixtures
 *
 * @author Teddy Wafula
 *
 */
class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $roles = ['ROLE_ADMIN','ROLE_MODERATOR'];
        $emails = ['admin@test.com', 'mod@test.com'];
        $passwords = ['admin#123@','mod#123@'];
        for ($i=0;$i<2;$i++) {
            $user = new User();
            $user->setEmail($emails[$i]);
            $password = $this->hasher->hashPassword($user, $passwords[$i]);
            $user->setPassword($password);
            $user->setRoles([$roles[$i]]);

            $manager->persist($user);
        }
        $manager->flush();

    }

}
