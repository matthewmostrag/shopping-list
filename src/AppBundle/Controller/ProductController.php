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
     * @Route("/produits/rechercher", name="products_search", options={"expose": true})
     */
    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $productRepository = $em->getRepository("AppBundle:Product");

        // We retrieve the search term
        $search = $request->get("search");

        // We get the products that matches the research
        $products = $productRepository->search($search);

        return $this->render("product/search.html.twig", array(
            "products" => $products
        ));
    }
}
