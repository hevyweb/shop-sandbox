<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\Compiler\ResolveBindingsPass;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends Controller
{
    /**
     * @var FormView
     */
    protected $error;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * CategoryController constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @Route("/categories", methods={"GET"}, name="categories")
     * @return Response
     */
    public function indexAction(): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category, ['action' => $this->generateUrl('category-create')]);

        $categories = $this->getDoctrine()->getRepository(Category::class)->findRootCategories();

        return $this->render('categories/index.html.twig', [
            'categories' => $categories,
            'title' => 'Categories',
            'form' => $form->createView(),
            'error' => $this->error
        ]);
    }

    /**
     * @Route("/categories", methods={"POST"}, name="category-create")
     * @param Request $request
     * @return Response
     */
    public function addAction(Request $request): Response
    {
        $category = new Category();
        $category
            ->setCreated(new \DateTime())
            ->setUpdated(new \DateTime())
            ->setCreatedBy($this->getUser())
            ->setUpdatedBy($this->getUser());

        $form = $this->createForm(CategoryType::class, $category, ['action' => $this->generateUrl('category-create')]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('categories');
        } else {
            $this->error = $form->createView();
            return $this->indexAction();
        }
    }

    /**
     * @Route("/categories/{id}", requirements={"id"="\d+"}, methods={"POST"}, name="category-edit")
     * @param Request $request
     * @return Response
     */
    public function addEdit(Request $request): Response
    {
        $categoryId = $request->get('id');
        $category = $this->getCategory($categoryId);
        $category
            ->setUpdated(new \DateTime())
            ->setUpdatedBy($this->getUser());

        $form = $this->createForm(CategoryType::class, $category, ['action' => $this->generateUrl('category-create')]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();
        }
        return $this->redirectToRoute('categories');
    }

    /**
     * @Route("categories/{id}/delete", requirements={"id"="\d+"}, name="category-delete")
     * @param int $id
     * @return Response
     */
    public function deleteAction(int $id): Response
    {
        try {
            $category = $this->getCategory($id);
            $this->getDoctrine()->getRepository(Category::class)->removeHierarchy($category);
        } catch (NotFoundHttpException $exception){
            $this->logger->warning('Trying to delete category "' . $id . '". Category does not exists.');
        }

        return $this->redirectToRoute('categories');
    }

    /**
     * @param int $id category id
     * @return Category
     */
    protected function getCategory(int $id): Category
    {
        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);

        if (empty($category)){
            throw new NotFoundHttpException('Category with id: ' . $id . ' not found.');
        }
        return $category;
    }

}