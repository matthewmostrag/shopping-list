<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Product;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadProductData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $categories = array(
            "Nourriture" => array(
                "Pâtes",
                "Riz",
                "Patates"
            ),
            "Boisson" => array(
                "Eau",
                "Soda"
            ),
            "Produit d'entretien" => array(
                "Liquide vaisselle"
            )
        );

        foreach ( $categories as $category => $products )
        {
            foreach ( $products as $name )
            {
                $product = new Product();
                $product->setName($name);
                $product->setCategory($this->getReference($category));

                $manager->persist($product);
            }
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 2;
    }
}