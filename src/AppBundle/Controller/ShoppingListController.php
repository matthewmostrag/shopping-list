<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Product;
use AppBundle\Entity\ShoppingList;
use AppBundle\Form\ProductType;
use AppBundle\Form\ShoppingListType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ShoppingListController extends AbstractController
{
    /**
     * @Route("/listes", name="lists_homepage")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $listsRepository = $em->getRepository("AppBundle:ShoppingList");

        $lists = $listsRepository->findAll();

        return $this->render("shopping_list/index.html.twig", array(
            "lists" => $lists
        ));
    }

    /**
     * @Route("/listes/creer", name="lists_create")
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $list = new ShoppingList();

        $form = $this->createForm(ShoppingListType::class, $list);

        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() )
        {
            $em->persist($list);
            $em->flush();

            return $this->redirectToRoute("lists_add_products", ["list" => $list->getId()]);
        }

        return $this->render("shopping_list/create.html.twig", array(
            "form"    => $form->createView()
        ));
    }

    /**
     * @Route("/listes/{list}/ajouter-produits", name="lists_add_products", requirements={"list": "\d*"})
     */
    public function addProductsAction(ShoppingList $list, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $categoryRepository = $em->getRepository("AppBundle:Category");

        $categories = $categoryRepository->findAll();

        $formProduct = $this->createForm(ProductType::class);
        $formList = $this->createForm(ShoppingListType::class, $list);

        $formList->handleRequest($request);

        if ( $formList->isSubmitted() && $formList->isValid() )
        {
            $em->persist($list);
            $em->flush();

            $this->addFlash("info", "Les produits ont bien été ajoutés à la liste.");

            return $this->redirectToRoute("lists_homepage");
        }

        return $this->render("shopping_list/add_products.html.twig", array(
            "formProduct" => $formProduct->createView(),
            "formList"    => $formList->createView(),
            "list"        => $list,
            "categories"  => $categories
        ));
    }

    /**
     * @Route("listes/{list}/nouveau-produit", name="lists_add_new_product", requirements={"list": "\d*"}, methods={"POST"}, options={"expose": true})
     */
    public function addNewProductAction(ShoppingList $list, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $categoryRepository = $em->getRepository("AppBundle:Category");

        // We retrieve our data
        $productName = $request->get("productName");
        $productCategory = $request->get("productCategory");

        // We receive a new product, so first we need to create it
        $product = new Product();
        $product->setName($productName);

        $category = $categoryRepository->find($productCategory);
        $product->setCategory($category);

        $em->persist($product);

        // Then we add the new product to the list
        $list->addProduct($product);

        $em->persist($list);

        $em->flush();

        return $this->render("shopping_list/list_item.html.twig", array(
            "product" => $product
        ));
    }

    /**
     * @Route("listes/{list}/produit-existant", name="lists_add_existing_product", requirements={"list": "\d*"}, methods={"POST"}, options={"expose": true})
     */
    public function addExistingProductAction(ShoppingList $list, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $productRepository = $em->getRepository("AppBundle:Product");

        // We retrieve data to get the product
        $product = $productRepository->find($request->get("product"));

        // Then we add the existing product to the list
        $list->addProduct($product);

        $em->persist($list);

        $em->flush();

        return $this->render("shopping_list/list_item.html.twig", array(
            "product" => $product
        ));
    }

    /**
     * @Route("listes/{list}/supprimer-produit", name="lists_remove_product", requirements={"list": "\d*"}, methods={"POST"}, options={"expose": true})
     */
    public function removeProductAction(ShoppingList $list, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $productRepository = $em->getRepository("AppBundle:Product");

        // We retrieve the product
        $product = $productRepository->find($request->get("product"));

        // We remove the product from the list
        $list->removeProduct($product);

        $em->persist($list);

        $em->flush();

        return $this->render("shopping_list/category_list_item.html.twig", array(
            "product" => $product
        ));
    }

    /**
     * @Route("listes/{list}/supprimer-produits", name="lists_remove_all_products", requirements={"list": "\d*"}, options={"expose": true})
     */
    public function removeAllProductsAction(ShoppingList $list)
    {
        $em = $this->getDoctrine()->getManager();

        $list->clearProducts();

        $em->persist($list);
        $em->flush();

        return $this->redirectToRoute("lists_add_products", ["list" => $list->getId()]);
    }

    /**
     * @Route("listes/{list}/supprimer-categorie/{category}", name="lists_remove_category", requirements={"list": "\d*", "category": "\d*"}, options={"expose": true})
     */
    public function removeCategoryAction(ShoppingList $list, Category $category)
    {
        $em = $this->getDoctrine()->getManager();

        $products = $list->getProducts();

        foreach ( $products as $product )
        {
            if ( $product->getCategory() == $category )
            {
                $list->removeProduct($product);
            }
        }

        $em->persist($list);
        $em->flush();

        return $this->redirectToRoute("lists_add_products", ["list" => $list->getId()]);
    }

    /**
     * @Route("/listes/{list}/filtre-categorie", name="lists_category_filter", requirements={"list": "\d*"}, methods={"POST"}, options={"expose": true})
     */
    public function categoryFilterAction(ShoppingList $list, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $categoryRepository = $em->getRepository("AppBundle:Category");

        $products = $list->getProducts();
        $category = $request->get("category");

        if ( !empty($category) )
        {
            $category = $categoryRepository->find($category);

            $products = array_filter($products->toArray(), function($value) use($category) {
                return $value->getCategory() == $category;
            });
        }

        return $this->render("shopping_list/category_filter.html.twig", array(
            "products" => $products
        ));
    }
}