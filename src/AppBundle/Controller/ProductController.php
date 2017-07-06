<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Entity\ShoppingList;
use AppBundle\Form\ProductType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends AbstractController
{
    /**
     * @Route("/produits", name="products_homepage")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $productRepository = $em->getRepository("AppBundle:Product");

        $products = $productRepository->findAll();

        return $this->render("product/index.html.twig", array(
            "products" => $products
        ));
    }

    /**
     * @Route("/produits/ajouter", name="products_add")
     */
    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() )
        {
            $this->addFlash("info", "Le produit a bien été ajouté.");

            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute("products_homepage");
        }

        return $this->render("product/add.html.twig", array(
            "form" => $form->createView()
        ));
    }

    /**
     * @Route("/produits/modifier/{product}", name="products_edit", requirements={"product": "\d*"})
     */
    public function editAction(Product $product, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() )
        {
            $this->addFlash("info", "Le produit a bien été modifié.");

            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute("products_homepage");
        }

        return $this->render("product/edit.html.twig", array(
            "form" => $form->createView()
        ));
    }
}
