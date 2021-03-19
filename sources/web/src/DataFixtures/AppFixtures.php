<?php

namespace App\DataFixtures;

use App\Entity\Commodity;
use App\Entity\Company;
use App\Entity\Country;
use App\Entity\Group;
use App\Entity\Product;
use App\Entity\Production;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Nette\Utils\DateTime;

class AppFixtures extends Fixture
{
    /**
     * @param ObjectManager $manager
     * @return void
     *
     * @throws \Exception
     */
    public function load(ObjectManager $manager): void
    {
//        //Country
//        /** @var Country[] $countries */
//        $countries = array(
//            $manager->getRepository(Country::class)->find(97), // France
//            $manager->getRepository(Country::class)->find(98), // Germany
//            $manager->getRepository(Country::class)->find(151), // USA
//            $manager->getRepository(Country::class)->find(139), // Canada
//            $manager->getRepository(Country::class)->find(147), // Mexico
//            $manager->getRepository(Country::class)->find(175), // Argentina
//            $manager->getRepository(Country::class)->find(177) // Brazil
//        );
//
//        //Commodities
//        /** @var Commodity[] $commodities */
//        $commodities = [];
//        for ($i = 1; $i < 17; $i ++) {
//            /** @var Commodity $commodity */
//            $commodity = $manager->getRepository(Commodity::class)->find($i);
//            if (!empty($commodity)) {
//                $commodities[] = $commodity;
//            }
//        }
//
//        //Groups
//        /** @var Group[] $groups */
//        $groups = [];
//        for ($i = 1; $i < 4; $i ++) {
//            $group = new Group();
//            $group->setName('Group '.$i);
//            $manager->persist($group);
//            $manager->flush();
//            $groups[] = $group;
//        }
//
//        // Company
//        $c = 0;
//        $cn = 1;
//        /** @var Company $company */
//        $companies = [];
//        foreach ($groups as $group) {
//            for ($i = 0; $i < 3; $i++) {
//                $company = new Company();
//                $company->setName('Company ' . $cn);
//                $company->setCountry($countries[$c]);
//                $company->setGroup($group);
//                $company->setStatus('active');
//                $manager->persist($company);
//                $manager->flush();
//                $cn++;
//                $companies[] = $company;
//            }
//            $c ++;
//        }
//
//        // Product
//        foreach ($groups as $group) {
//            /** @var Company[] $companies*/
//            $companies = $manager->getRepository(Company::class)->findBy(['group' => $group->getId()]);
//            foreach ($companies as $company) {
//                $c = 0;
//                for ($i = 0; $i < 3; $i++) {
//                    $product = new Product();
//                    $product->setCommodity($commodities[$c]);
//                    $product->setCompany($company);
//                    $product->setStartDate($this->setStartDateOnMiddleOfTheMonth());
//                    $product->setStatus('active');
//                    $manager->persist($product);
//                    $manager->flush();
//
//                    $c ++;
//                }
//            }
//        }
//
//        // Production
//        /** @var Product[] $products **/
//        $products = $manager->getRepository(Product::class)->findAll();
//        foreach ($products as $selectedProduct) {
//            /** @var Product $selectedProduct **/
//            $start    = $selectedProduct->getStartDate();
//            $start->modify('first day of this month');
//            $end      = new DateTime();
//            $end->modify('first day of this month');
//            $end->modify('+ 14 days');
//            $end->sub(new \DateInterval('P1M'));
//            $interval = \DateInterval::createFromDateString('1 month');
//            $period   = new \DatePeriod($start, $interval, $end);
//
//            $capacityAverage = rand(1000, 4000);
//            foreach ($period as $dateTimeInter1Month) {
//                $prod = $capacityAverage - rand(500, 1000);
//                $capacity = rand($capacityAverage - 500, $capacityAverage + 500);
//                $inv = rand(0, 300);
//                $production = new Production();
//                $dateTimeInter1Month->modify('first day of this month');
//                $dateTimeInter1Month->modify('+ 14 days');
//                $production->setDate($dateTimeInter1Month);
//                $production->setProduct($selectedProduct);
//                $production->setProduction($prod);
//                $production->setCapacity($capacity);
//                $production->setInventory($inv);
//                $manager->persist($production);
//                $manager->flush();
//            }
//        }

        /** @var Group $group */
        $group = $manager->getRepository(Group::class)->find(18);

        // Users
        $adminUser = new User();
        $adminUser->setLogin('Admin');
        $adminUser->setPlainPassword('admin');
        $adminUser->setRoles(['ADMIN']);
        $adminUser->setEmail('test@test.fr');
        $adminUser->setGroup($group);
        $manager->persist($adminUser);
        $manager->flush();

        $superUser1 = new User();
        $superUser1->setLogin('Super-User 1');
        $superUser1->setPlainPassword('super');
        $superUser1->setRoles(['SUPER-USER']);
        $superUser1->setEmail('super@test.fr');
        $superUser1->setGroup($group);
        $manager->persist($superUser1);
        $manager->flush();

        $superUser2 = new User();
        $superUser2->setLogin('Super-User 2');
        $superUser2->setPlainPassword('super');
        $superUser2->setRoles(['SUPER-USER']);
        $superUser2->setEmail('super@test.fr');
        $superUser2->setGroup($group);
        $manager->persist($superUser2);
        $manager->flush();

        $user = new User();
        $user->setLogin('User');
        $user->setPlainPassword('user');
        $user->setRoles(['USER']);
        $user->setEmail('user@test.fr');
        $user->setGroup($group);
        $manager->persist($user);
        $manager->flush();
    }

//    /**
//     * @return DateTime
//     */
//    private function setStartDateOnMiddleOfTheMonth(): DateTime
//    {
//        $now =  new DateTime();
//        $now->modify('first day of this month');
//        $now->modify('+ 14 days');
//        return $now->modify('-'.rand(6, 24) . ' months');
//    }
}
