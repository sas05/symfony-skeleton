<?php

namespace App\DataFixtures;

use App\Entity\Type;
use App\Entity\ExchangeMarket;
use App\Entity\User;
use App\Entity\Company;
use App\Entity\Stock;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $this->loadUsers($manager);
        $this->loadType($manager);
        $this->loadExchangeMarket($manager);
        $this->loadCompany($manager);
        $this->loadStock($manager);
    }

    private function loadUsers(ObjectManager $manager)
    {
        foreach ($this->getUserData() as [$fullname, $username, $password, $email, $roles]) {
            $user = new User();
            $user->setFullName($fullname);
            $user->setUsername($username);
            $user->setPassword($this->passwordEncoder->encodePassword($user, $password));
            $user->setEmail($email);
            $user->setRoles($roles);

            $manager->persist($user);
            $this->addReference($username, $user);
        }

        $manager->flush();
    }

    private function loadType(ObjectManager $manager)
    {
        foreach ($this->getTypeData() as $index => $name) {
            $type = new Type();
            $type->setName($name);

            $manager->persist($type);
            $this->addReference($name, $type);
        }

        $manager->flush();
    }

    private function loadExchangeMarket(ObjectManager $manager)
    {
        foreach ($this->getExchangeMarketData() as $index => $name) {
            $exchangeMarket = new ExchangeMarket();
            $exchangeMarket->setName($name);

            $manager->persist($exchangeMarket);
            $this->addReference($name, $exchangeMarket);
        }

        $manager->flush();
    }

    private function loadCompany(ObjectManager $manager)
    {
        foreach ($this->getCompanyData() as $index => $name) {
            $company = new Company();
            $company->setName($name);
            $company->setAuthor($this->getReference('jane_admin'));

            $manager->persist($company);
            $this->addReference($name, $company);
        }

        $manager->flush();
    }

    private function loadStock(ObjectManager $manager)
    {
        
        $stock = new Stock();
        $stock->setCompany($this->getReference('Kiveo AG'));
        $stock->setAuthor($this->getReference('jane_admin'));
        $stock->setType($this->getReference('Preferred Stock'));
        $stock->setExchangeMarket($this->getReference('New York Stock Exchange'));
        $stock->setPrice(12.66);

        $manager->persist($stock);
        $this->addReference('stock', $stock);

        $manager->flush();
    }

    private function getUserData(): array
    {
        return [
            // $userData = [$fullname, $username, $password, $email, $roles];
            ['Jane Doe', 'jane_admin', 'kitten', 'jane_admin@symfony.com', ['ROLE_ADMIN']],
            ['Tom Doe', 'tom_admin', 'kitten', 'tom_admin@symfony.com', ['ROLE_ADMIN']],
            ['John Doe', 'john_user', 'kitten', 'john_user@symfony.com', ['ROLE_USER']],
        ];
    }

    private function getExchangeMarketData(): array
    {
        return [
            'New York Stock Exchange',
            'London Stock Exchange',
            'Hong Kong Stock Exchange',
        ];
    }

    private function getCompanyData(): array
    {
        return [
            'Kiveo AG',
        ];
    }

    private function getTypeData(): array
    {
        return [
            'Preferred Stock',
            'Common Stock',
        ];
    }
}
