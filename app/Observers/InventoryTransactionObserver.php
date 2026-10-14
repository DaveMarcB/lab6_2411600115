<?php

namespace App\Observers;

use App\Models\InventoryTransaction;
use App\Models\User;
use App\Notifications\LowStockNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Exception;

class InventoryTransactionObserver
{
    /**
     * Handle the InventoryTransaction "created" event.
     */
    public function created(InventoryTransaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
            $product = $transaction->product()->lockForUpdate()->first();

            if (!$product) {
                throw new Exception("Related product not found.");
            }

            // Calculate new quantity based on transaction type
            if ($transaction->type === 'stock_in') {
                $newQuantity = $product->quantity + abs($transaction->quantity);
            } elseif ($transaction->type === 'stock_out') {
                $newQuantity = $product->quantity - abs($transaction->quantity);
            } else {
                $newQuantity = $product->quantity + $transaction->quantity;
            }

            // Prevent negative inventory
            if ($newQuantity < 0) {
                throw new Exception("Insufficient stock for {$product->name}. Available: {$product->quantity}");
            }

            $product->quantity = $newQuantity;
            $product->save();

            // Part 6, Step 3: Trigger LowStockNotification if at or below reorder level
            if ($product->isLowStock()) {
                $users = User::all();

                // Filter out users who already have an unread notification for this product
                $usersToNotify = $users->filter(function ($user) use ($product) {
                    return !$user->unreadNotifications()
                        ->where('data->product_id', $product->id)
                        ->exists();
                });

                if ($usersToNotify->isNotEmpty()) {
                    Notification::send($usersToNotify, new LowStockNotification($product));
                }
            }
        });
    }

    /**
     * Handle the InventoryTransaction "deleted" event.
     */
    public function deleted(InventoryTransaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
            $product = $transaction->product()->lockForUpdate()->first();

            if (!$product) {
                return;
            }

            if ($transaction->type === 'stock_in') {
                $newQuantity = $product->quantity - abs($transaction->quantity);
            } elseif ($transaction->type === 'stock_out') {
                $newQuantity = $product->quantity + abs($transaction->quantity);
            } else {
                $newQuantity = $product->quantity - $transaction->quantity;
            }

            if ($newQuantity < 0) {
                throw new Exception("Cannot delete transaction: reversing this stock-in would result in negative inventory.");
            }

            $product->quantity = $newQuantity;
            $product->save();
        });
    }
}