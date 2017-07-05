<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Form\CategoryType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends AbstractController
{
    /**
     * @Route("/categories", name="categories_homepage")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $categoryRepository = $em->getRepository("AppBundle:Category");

        $categories = $categoryRepository->findAll();

        return $this->render("category/index.html.twig", array(
            "categories" => $categories
        ));
    }

    /**
     * @Route("/categories/ajouter", name="categories_add")
     */
    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() )
        {
            $this->addFlash("info", "La catégorie a bien été ajoutée.");

            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute("categories_homepage");
        }

        return $this->render("category/add.html.twig", array(
            "form" => $form->createView()
        ));
    }

    /**
     * @Route("/categories/modifier/{category}", name="categories_edit", requirements={"category": "\d*"})
     */
    public function editAction(Category $category, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() )
        {
            $this->addFlash("info", "La catégorie a bien été modifiée.");

            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute("categories_homepage");
        }

        return $this->render("category/edit.html.twig", array(
            "form" => $form->createView()
        ));
    }
}
