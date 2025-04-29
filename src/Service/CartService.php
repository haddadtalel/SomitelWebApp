<?php

namespace App\Service;

use App\Entity\CartItem;
use App\Entity\Product;
use App\Repository\CartItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{
    private $requestStack;
    private $entityManager;
    private $cartItemRepository;

    public function __construct(
        RequestStack $requestStack,
        EntityManagerInterface $entityManager,
        CartItemRepository $cartItemRepository
    ) {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->cartItemRepository = $cartItemRepository;
    }

    /**
     * Get the current session ID
     */
    private function getSessionId(): string
    {
        $session = $this->requestStack->getSession();
        
        // If session doesn't have an ID yet, start it
        if (!$session->isStarted()) {
            $session->start();
        }
        
        return $session->getId();
    }

    /**
     * Add a product to the cart
     */
    public function addToCart(Product $product, int $quantity = 1): void
    {
        $sessionId = $this->getSessionId();
        
        // Check if product already exists in cart
        $cartItem = $this->cartItemRepository->findOneByProductAndSession(
            $product->getId(),
            $sessionId
        );
        
        if ($cartItem) {
            // Update quantity if product already in cart
            $cartItem->setQuantity($cartItem->getQuantity() + $quantity);
        } else {
            // Create new cart item
            $cartItem = new CartItem();
            $cartItem->setProduct($product);
            $cartItem->setQuantity($quantity);
            $cartItem->setSessionId($sessionId);
        }
        
        $this->entityManager->persist($cartItem);
        $this->entityManager->flush();
    }

    /**
     * Remove a product from the cart
     */
    public function removeFromCart(int $cartItemId): void
    {
        $cartItem = $this->cartItemRepository->find($cartItemId);
        
        if ($cartItem && $cartItem->getSessionId() === $this->getSessionId()) {
            $this->entityManager->remove($cartItem);
            $this->entityManager->flush();
        }
    }

    /**
     * Update cart item quantity
     */
    public function updateQuantity(int $cartItemId, int $quantity): void
    {
        $cartItem = $this->cartItemRepository->find($cartItemId);
        
        if ($cartItem && $cartItem->getSessionId() === $this->getSessionId()) {
            if ($quantity <= 0) {
                $this->entityManager->remove($cartItem);
            } else {
                $cartItem->setQuantity($quantity);
            }
            
            $this->entityManager->flush();
        }
    }

    /**
     * Get all items in the cart
     */
    public function getCartItems(): array
    {
        return $this->cartItemRepository->findBySessionId($this->getSessionId());
    }

    /**
     * Get total number of items in cart
     */
    public function getCartItemCount(): int
    {
        return $this->cartItemRepository->getTotalQuantityBySessionId($this->getSessionId());
    }

    /**
     * Calculate total price of items in cart
     */
    public function getCartTotal(): float
    {
        $total = 0;
        $cartItems = $this->getCartItems();
        
        foreach ($cartItems as $item) {
            $total += $item->getProduct()->getPrice() * $item->getQuantity();
        }
        
        return $total;
    }

    /**
     * Check if cart has items
     */
    public function hasItems(): bool
    {
        return $this->getCartItemCount() > 0;
    }

    /**
     * Clear all items from cart
     */
    public function clearCart(): void
    {
        $cartItems = $this->getCartItems();
        
        foreach ($cartItems as $item) {
            $this->entityManager->remove($item);
        }
        
        $this->entityManager->flush();
    }
}
