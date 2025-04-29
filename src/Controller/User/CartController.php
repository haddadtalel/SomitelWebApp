<?php

namespace App\Controller\User;

use App\Entity\Product;
use App\Repository\AboutUsRepository;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/cart')]
class CartController extends AbstractController
{
    private $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    #[Route('', name: 'app_cart_index', methods: ['GET'])]
    public function index(AboutUsRepository $aboutUs): Response
    {
        return $this->render('cart/index.html.twig', [
            'cart_items' => $this->cartService->getCartItems(),
            'cart_total' => $this->cartService->getCartTotal(),
            'aboutUs' => $aboutUs->find(1),
        ]);
    }

    #[Route('/add/{id}', name: 'app_cart_add', methods: ['POST'])]
    public function add(Request $request, Product $product): JsonResponse
    {
        $quantity = (int) $request->request->get('quantity', 1);
        
        $this->cartService->addToCart($product, $quantity);
        
        return $this->json([
            'success' => true,
            'message' => 'Product added to cart',
            'cartCount' => $this->cartService->getCartItemCount()
        ]);
    }

    #[Route('/remove/{id}', name: 'app_cart_remove', methods: ['POST'])]
    public function remove(int $id): JsonResponse
    {
        $this->cartService->removeFromCart($id);
        
        return $this->json([
            'success' => true,
            'message' => 'Product removed from cart',
            'cartCount' => $this->cartService->getCartItemCount()
        ]);
    }

    #[Route('/update/{id}', name: 'app_cart_update', methods: ['POST'])]
    public function update(Request $request, int $id): JsonResponse
    {
        $quantity = (int) $request->request->get('quantity', 1);
        
        $this->cartService->updateQuantity($id, $quantity);
        
        return $this->json([
            'success' => true,
            'message' => 'Cart updated',
            'cartCount' => $this->cartService->getCartItemCount(),
            'cartTotal' => $this->cartService->getCartTotal()
        ]);
    }

    #[Route('/count', name: 'app_cart_count', methods: ['GET'])]
    public function count(): JsonResponse
    {
        return $this->json([
            'count' => $this->cartService->getCartItemCount()
        ]);
    }
}
