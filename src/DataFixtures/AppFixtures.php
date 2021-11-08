<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Facture;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{  /**
 * encodeur de mots de passse
 *
 * @var UserPasswordEncoderInterface
 */
    private $encoder;
    public function __Construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder=$encoder;
    }
    public function load(ObjectManager $manager)
    {
        $faker=Factory::create('fr_FR');
        $chrono= 1;

        for($u=0;$u<10;$u++)
        {
            $user=new User();
            $chrono= 1;
            $hash=$this->encoder->encodePassword($user,"password");

            $user->setNom($faker->firstName())
                ->setPrenom($faker->lastName)
                ->setEmail($faker->email)
                ->setPassword($hash);
                $manager->persist($user);

                for($c=0; $c<mt_rand(5,20) ;$c++)
            {
                $client = new Client();
                $client->setNom($faker->firstName())
                        ->setPreNom($faker->lastName)
                        ->setCompany($faker->company)
                        ->setMail($faker->email)
                        ->setUser($user);
                $manager->persist($client);
                for($i=0;$i<mt_rand( 1, 5);$i++)
                {
                    $facture = new Facture();
                    $facture ->setMontant($faker->randomFloat(2,250,500))
                            ->setSentAt($faker->dateTimeBetween('-6 months'))
                            ->setSatut($faker->randomElement(['SENT','PAID','CANCELED']))
                            ->setCustomer($client)
                            ->setChrono($chrono);
                            $manager->persist($facture);
                        
                        $chrono++;
                }
            }
        }

        

        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
