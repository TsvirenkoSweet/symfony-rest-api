<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var \Faker\Factory
     */
    private $faker;

    private const USERS = [
        [
            'username' => 'superadmin',
            'email' => 'admin@shop.com',
            'name' => 'Tsvirenko Andrii',
            'password' => 'secret123#',
            'roles' => [User::ROLE_SUPERADMIN]
        ],
        [
            'username' => 'admin',
            'email' => 'barinov@shop.com',
            'name' => 'Barinov Igor',
            'password' => 'secret123#',
            'roles' => [User::ROLE_ADMIN]
        ],
        [
            'username' => 'writer1',
            'email' => 'ledok@shop.com',
            'name' => 'Ledok Sa',
            'password' => 'secret123#',
            'roles' => [User::ROLE_WRITER]
        ],
        [
            'username' => 'writer2',
            'email' => 'samulyak@shop.com',
            'name' => 'Samulyak Sa',
            'password' => 'secret123#',
            'roles' => [User::ROLE_WRITER]
        ],
        [
            'username' => 'editor',
            'email' => 'porosya@shop.com',
            'name' => 'Porosya Piggi',
            'password' => 'secret123#',
            'roles' => [User::ROLE_EDITOR]
        ],
        [
            'username' => 'commentator',
            'email' => 'mamba@shop.com',
            'name' => 'Black Mamba',
            'password' => 'secret123#',
            'roles' => [User::ROLE_COMMENTATOR]
        ],
    ];

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->faker = \Faker\Factory::create();
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->loadUsers($manager);
        $this->loadProducts($manager);
        $this->loadComments($manager);
    }

    public function loadProducts(ObjectManager $manager){
        for ($i = 0; $i < 30; $i++){
            $product = new Product();
            $product->setProductTitle($this->faker->realText(30));
            $product->setProductPrice($this->faker->randomFloat(2, 0, 9999));
            $product->setProductSize($this->faker->randomElement(array('xs', 's', 'm', 'l', 'xl', 'xxl', 'xxxl')));
            $product->setProductColor($this->faker->colorName);
            $product->setProductDescription($this->faker->realText());

            $authorReference = $this->getRandomUserReference($product);

            $product->setSlug($this->faker->slug(3));
            $product->setAuthor($authorReference);
            $product->setPublished($this->faker->dateTimeThisYear);

            $this->setReference("product_post_$i", $product);

            $manager->persist($product);
        }

        $manager->flush();
    }

    public function loadComments(ObjectManager $manager){
        for ($i = 0; $i < 30; $i++){
            for ($j = 0; $j < rand(1, 10); $j++){
                $comment = new Comment();
                $comment->setContent($this->faker->realText(100));
                $comment->setPublished($this->faker->dateTimeThisYear);

                $authorReference = $this->getRandomUserReference($comment);

                $comment->setAuthor($authorReference);
                $comment->setPosts($this->getReference("product_post_$i"));

                $manager->persist($comment);
            }
        }

        $manager->flush();
    }

    public function loadUsers(ObjectManager $manager)
    {
        foreach (self::USERS as $userFixture) {
            $user = new User();
            $user->setUsername($userFixture['username']);
            $user->setEmail($userFixture['email']);
            $user->setName($userFixture['name']);

            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                $userFixture['password']
            ));

            $user->setRoles($userFixture['roles']);

            $this->addReference('user_' . $userFixture['username'], $user);

            $manager->persist($user);
        }
        $manager->flush();
    }

    protected function getRandomUserReference($entity): User
    {
        $randomUser = self::USERS[rand(0,5)];

        if($entity instanceof Product &&
            count(
                array_intersect(
                    $randomUser['roles'],
                    [
                        User::ROLE_SUPERADMIN,
                        User::ROLE_ADMIN,
                        User::ROLE_WRITER
                    ]
                )
            )
        ){
            return $this->getRandomUserReference($entity);
        }

        if($entity instanceof Comment &&
            count(
                array_intersect(
                    $randomUser['roles'],
                    [
                        User::ROLE_SUPERADMIN,
                        User::ROLE_ADMIN,
                        User::ROLE_WRITER,
                        User::ROLE_COMMENTATOR
                    ]
                )
            )
        ){
            return $this->getRandomUserReference($entity);
        }

        return $this->getReference(
          'user_'.$randomUser['username']
        );
    }
}
