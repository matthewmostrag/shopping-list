<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Category;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadCategoryData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $names = array(
            "Nourriture",
            "Boisson",
            "Produit d'entretien"
        );

        foreach ( $names as $name )
        {
            $category = new Category();
            $category->setName($name);

            $manager->persist($category);

            $this->addReference($name, $category);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}