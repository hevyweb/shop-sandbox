<?php

namespace App\Controller;

use App\Entity\OrderItem;
use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Service\Filesystem\LocalStorageRemover;
use App\Service\Filesystem\LocalStorageUpload;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends Controller
{
    /**
     * Number of products per page
     */
    const LIMIT = 20;

    /**
     * @var int magic number which indicates that product can't be deleted because order with this product exists.
     */
    const ERROR_ORDER_EXISTS = 1;

    /**
     * @Route("/products", name="products")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $search = $request->get('q');
        $page = intval($request->get('page', 1));
        /**
         * @var ProductRepository $productRepository
         */
        $productRepository = $this->getDoctrine()->getRepository(Product::class);
        $criteria = Criteria::create();

        if (!empty($search)) {
            $criteria->where(Criteria::expr()->contains("name", $search));
        }
        $criteria->orderBy([
            'created_at' => Criteria::DESC,
            'updated_at' => Criteria::DESC,
            'id' => Criteria::ASC])
            ->setFirstResult(($page-1)*self::LIMIT)
            ->setMaxResults(self::LIMIT);

        $products = $productRepository->matching($criteria);
        $total = $productRepository->total($criteria   );
        $totalPages = ceil($total/self::LIMIT);

        $token = md5(time());

        $_SESSION['product_delete_token'] = $token;

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'title' => 'Products',
            'totalPages' => $totalPages,
            'page' => $page,
            'token' => $token,
            'filtervariables' => ['q' => $search]
        ]);
    }

    /**
     * @Route("/product", name="product-create")
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->uploadImage($product, $request);
                $product->setCreatedAt(new \DateTime());
                $product->setCreatedBy($this->getUser());

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($product);
                $entityManager->flush();

                return $this->redirectToRoute('products');
            } catch (\Exception $e) {
                $form->addError(new FormError($e->getMessage()));
            }
        }

        return $this->render('product/form.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
            'title' => 'Create new product',
            'submit' => 'Create'
        ]);
    }

    /**
     * @Route("/product/{id}", name="product-edit", requirements={"id"="\d+"})
     * @param Request $request
     * @return Response
     */
    public function update(Request $request): Response
    {
        $id = (int) $request->get('id');
        /**
         * @var Product $product
         */
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);
        if (!$product) {
            throw new NotFoundHttpException('Product with id "' . $id . '" not fount.');
        }
        $form = $this->createForm(ProductType::class, $product);

        if (intval($request->get('error')) === 1) {
            $form->addError(new FormError('Product can not be removed because it has been added to order.'));
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->uploadImage($product, $request);
                $product->setUpdatedAt(new \DateTime());
                $product->setUpdatedBy($this->getUser());

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($product);
                $entityManager->flush();

                return $this->redirectToRoute('products');
            } catch (\Exception $e) {
                $form->addError(new FormError($e->getMessage()));
            }
        }

        return $this->render('product/form.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
            'title' => 'Update product',
            'submit' => 'Update'
        ]);
    }

    /**
     * @Route("/product/delete/{id}", name="product-delete", requirements={"id"="\d+"})
     * @param int $id
     * @param LocalStorageRemover $localStorageRemover
     * @return Response
     */
    public function delete(int $id, LocalStorageRemover $localStorageRemover): Response
    {
        /**
         * @var Product $product
         */
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);
        if (!$product) {
            throw new NotFoundHttpException('Product with id "' . $id . '" not fount.');
        }

        if ($this->getDoctrine()->getRepository(OrderItem::class)->findOneBy(['product' => $product])) {
            return $this->redirectToRoute('product-edit', ['id' => $id, 'error' => self::ERROR_ORDER_EXISTS]);
        }

        $localStorageRemover->remove($product->getImage());

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($product);
        $entityManager->flush();
        return $this->redirectToRoute('products');
    }

    /**
     * @Route("/products/delete", name="products-delete")
     * @param Request $request
     * @param LocalStorageRemover $localStorageRemover
     * @return Response
     */
    public function multiDelete(Request $request, LocalStorageRemover $localStorageRemover): Response
    {
        $ids = $request->get('id');

        if (count($ids)) {
            $products = $this->getDoctrine()->getManager()->getRepository(Product::class)->getProducts($ids);
            if (!empty($products)) {
                $orderItemRepository = $this->getDoctrine()->getRepository(OrderItem::class);
                foreach($products as $product) {
                    if ($orderItemRepository->findOneBy(['product' => $product])) {
                        continue;
                    }
                    $localStorageRemover->remove($product->getImage());

                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->remove($product);
                    $entityManager->flush();
                }
            }
        }
        return $this->redirectToRoute('products');
    }

    /**
     * @param Product $product
     * @param Request $request
     */
    public function uploadImage(Product $product, Request $request)
    {
        $files = $request->files->get('product');
        if (!empty($files['image'])) {
            /**
             * @var LocalStorageUpload $localStorageUpload
             */
            $localStorageUpload = $this->container->get(LocalStorageUpload::class);
            if ($localStorageUpload->upload($files['image'])) {
                if ($product->getImage()) {
                    $this->container->get('image_remover')->remove($product->getImage());
                }
                $product->setImage($localStorageUpload->getPublicUrl());
            }
        }
    }

    /**
     * @Route("/products/delete-image/{id}", name="product-delete-image", requirements={"id"="\d+"})
     * @param int $id product id
     * @param LocalStorageRemover $localStorageRemover
     * @return Response
     */
    public function removeImage($id, LocalStorageRemover $localStorageRemover)
    {
        /**
         * @var Product $product
         */
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);
        if (!$product) {
            throw new NotFoundHttpException('Product with id "' . $id . '" not fount.');
        }

        $localStorageRemover->remove($product->getImage());
        $product->setImage(null);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($product);
        $entityManager->flush();
        return $this->redirectToRoute('product-edit', ['id' => $product->getId()]);
    }
}
