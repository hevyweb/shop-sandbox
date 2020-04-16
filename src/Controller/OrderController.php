<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use App\Form\OrderType;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Service\Status;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    /**
     * Number of orders per page
     */
    const LIMIT = 20;

    /**
     * @Route("/orders", name="orders")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $search = $request->get('q');
        $page = intval($request->get('page', 1));
        /**
         * @var OrderRepository $orderRepository
         */
        $orderRepository = $this->getDoctrine()->getRepository(Order::class);
        $criteria = Criteria::create();

        if (!empty($search)) {
            $criteria->where(Criteria::expr()->contains("id", $search));
        }
        $criteria->orderBy([
            'status' => Criteria::ASC,
            'created_at' => Criteria::ASC,
            'id' => Criteria::ASC])
            ->setFirstResult(($page - 1) * self::LIMIT)
            ->setMaxResults(self::LIMIT);

        $orders = $orderRepository->matching($criteria);
        $total = $orderRepository->total($criteria);
        $totalPages = ceil($total / self::LIMIT);

        return $this->render('order/index.html.twig', [
            'orders' => $orders,
            'title' => 'Orders',
            'totalPages' => $totalPages,
            'page' => $page,
            'filtervariables' => ['q' => $search]
        ]);
    }

    /**
     * @Route("/order", name="order-create")
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        return $this->render('order/form.html.twig', array_merge([
            'order' => new Order(),
            'title' => 'New order',
            'status' => new Status()
        ], $this->searchProducts($request)));
    }

    /**
     * @Route("/order/{id}", name="order-update", requirements={"id"="\d+"})
     * @param Request $request
     * @return Response
     */
    public function update(Request $request): Response
    {
        $id = $request->get('id');
        /**
         * @var OrderRepository $orderRepository
         * @var Order $order
         */
        $orderRepository = $this->getDoctrine()->getRepository(Order::class);
        $order = $orderRepository->find($id);
        if (empty($order)) {
            throw new NotFoundHttpException('Order with id: ' . $id . ' not found.');
        }

        $form = $this->createForm(OrderType::class, $order);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($order);
                $entityManager->flush();
            } catch (\Exception $e) {
                $form->addError(new FormError($e->getMessage()));
            }
        }

        $orderItems = $request->get('itemId');
        if (!empty($orderItems)){
            foreach ($orderItems as $orderItemId => $quantity) {
                $quantity = intval($quantity);
                if ($quantity > 0) {
                    $orderItem = $this->getDoctrine()->getRepository(OrderItem::class)->find($orderItemId);
                    $orderItem->setQuantity($quantity);
                    $this->getDoctrine()->getManager()->persist($orderItem);
                    $this->getDoctrine()->getManager()->flush();
                }
            }
        }

        return $this->render('order/form.html.twig', array_merge([
            'order' => $order,
            'title' => 'Order #' . $order->getId(),
            'status' => new Status(),
            'form' => $form->createView(),
        ], $this->searchProducts($request)));
    }

    /**
     * @Route("/order/{orderId}/{itemId}", name="order-item-add", requirements={"itemId"="\d+"})
     * @param Request $request
     * @return Response
     */
    public function addItem(Request $request): Response
    {
        $orderId = $request->get('orderId');
        $itemId = $request->get('itemId');
        if (!empty($orderId)) {
            $order = $this->getDoctrine()->getRepository(Order::class)->find($orderId);
            if (empty($order)) {
                throw new NotFoundHttpException('Order with id: ' . $orderId . ' not found.');
            }
        } else {
            $order = new Order();
            $order->setCreatedAt(new \DateTime())->setStatus(Order::NEW);
        }

        $product = $this->getDoctrine()->getRepository(Product::class)->find($itemId);

        foreach ($order->getOrderItems() as $orderItem) {
            if ($product->getId() == $orderItem->getProduct()->getId() && $product->getPrice() == $orderItem->getPrice()) {
                $orderItem->setQuantity($orderItem->getQuantity() + 1);
                $newOrderItem = true;
                break;
            }
        }

        if (empty($newOrderItem)) {
            $orderItem = new OrderItem();
            $orderItem->setPrice($product->getPrice())->setQuantity(1)->setProduct($product);
            $order->addOrderItem($orderItem);
        }
        $this->getDoctrine()->getManager()->persist($order);
        $this->getDoctrine()->getManager()->persist($orderItem);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('order-update', ['id' => $order->getId()]);
    }

    /**
     * @Route("/orders/delete/{orderItem}", name="order-item-delete", requirements={"orderItem"="\d+"})
     * @param Request $request
     * @return Response
     */
    public function orderItemDelete(Request $request): Response
    {
        $orderItemId = $request->get('orderItem');
        /**
         * @var OrderItem $orderItem
         */
        $orderItem = $this->getDoctrine()->getRepository(OrderItem::class)->find($orderItemId);

        if (empty($orderItem)) {
            throw new NotFoundHttpException('Order item with id ' . $orderItemId . ' does not exist.');
        }

        $order = $orderItem->getParentOrder();
        $order->removeOrderItem($orderItem);
        $this->getDoctrine()->getManager()->remove($orderItem);
        $this->getDoctrine()->getManager()->persist($order);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('order-update', ['id' => $order->getId()]);
    }

    protected function searchProducts(Request $request)
    {
        $search = $request->get('q');

        if (!empty($search)) {
            $page = intval($request->get('page', 1));
            /**
             * @var ProductRepository $productRepository
             */
            $productRepository = $this->getDoctrine()->getRepository(Product::class);
            $criteria = Criteria::create();
            $criteria->where(Criteria::expr()->contains("name", $search));
            $criteria->orderBy([
                'updated_at' => Criteria::ASC,
                'id' => Criteria::ASC])
                ->setFirstResult(($page-1)*self::LIMIT)
                ->setMaxResults(self::LIMIT);

            $products = $productRepository->matching($criteria);
            $total = $productRepository->total($criteria);
            $totalPages = ceil($total/self::LIMIT);
            return [
                'products' => $products,
                'title' => 'Products',
                'totalPages' => $totalPages,
                'page' => $page,
                'filtervariables' => ['q' => $search]
            ];
        }
        return [];
    }
}
