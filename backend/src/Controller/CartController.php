<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\CartItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    private function getCart(EntityManagerInterface $em, Request $request): Cart
    {
        $session = $request->getSession();
        if (!$session->isStarted()) {
            $session->start();
        }
        $session->set('cart_active', true);

        $sessionId = $session->getId();

        $cart = $em->getRepository(Cart::class)->findOneBy(['sessionId' => $sessionId]);
        if (!$cart) {
            $cart = new Cart();
            $cart->setSessionId($sessionId);
            $em->persist($cart);
            $em->flush();
        }

        return $cart;
    }

    #[Route('/api/cart', methods: ['GET'])]
    public function getCartItems(EntityManagerInterface $em, Request $request): JsonResponse
    {
        $cart = $this->getCart($em, $request);
        $items = [];

        foreach ($cart->getItems() as $item) {
            $items[] = [
                'id' => $item->getId(),
                'productId' => $item->getProductId(),
                'quantity' => $item->getQuantity(),
            ];
        }

        return new JsonResponse($items);
    }

    #[Route('/api/cart/add', methods: ['POST'])]
    public function addToCart(EntityManagerInterface $em, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (empty($data['productId']) || empty($data['quantity'])) {
            return new JsonResponse(['error' => 'Missing productId or quantity'], 400);
        }

        $cart = $this->getCart($em, $request);

        // Termék ell. (hogy létezik-e már)
        foreach ($cart->getItems() as $item) {
            if ($item->getProductId() === $data['productId']) {
                $item->setQuantity($item->getQuantity() + $data['quantity']);
                $em->flush();
                return new JsonResponse(['success' => true]);
            }
        }

        $cartItem = new CartItem();
        $cartItem->setProductId($data['productId']);
        $cartItem->setQuantity($data['quantity']);
        $cart->addItem($cartItem);

        $em->persist($cartItem);
        $em->flush();

        return new JsonResponse(['success' => true]);
    }

    #[Route('/api/cart/remove/{id}', methods: ['DELETE'])]
    public function removeFromCart(int $id, EntityManagerInterface $em, Request $request): JsonResponse
    {
        $cart = $this->getCart($em, $request);
        $item = $em->getRepository(CartItem::class)->find($id);

        if (!$item || $item->getCart()->getId() !== $cart->getId()) {
            return new JsonResponse(['error' => 'Item not found'], 404);
        }

        $cart->removeItem($item);
        $em->remove($item);
        $em->flush();

        return new JsonResponse(['success' => true]);
    }

    #[Route('/api/cart/clear', methods: ['DELETE'])]
    public function clearCart(EntityManagerInterface $em, Request $request): JsonResponse
    {
        $cart = $this->getCart($em, $request);
        foreach ($cart->getItems() as $item) {
            $em->remove($item);
        }
        $em->flush();

        return new JsonResponse(['success' => true]);
    }
}
