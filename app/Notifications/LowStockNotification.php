<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification
{
    use Queueable;

    protected Product $product;

    /**
     * Accept a Product instance in the constructor.
     */
    public function __construct(Product $product)
    {
        $this->product =$product;
    }

    /**
     * Specify the delivery channels (database, mail).
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Define the email notification content.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Low Stock Alert: {$this->product->name} ({$this->product->sku})")
            ->greeting("Hello {$notifiable->name},")
            ->line("Inventory levels for **{$this->product->name}** (SKU: `{$this->product->sku}`) have fallen to or below the reorder threshold.")
            ->line("Current Quantity: **{$this->product->quantity}**")
            ->line("Reorder Level: **{$this->product->reorder_level}**")
            ->action('View Product & Reorder', url(route('products.show', $this->product->id)))
            ->line('Suggested Action: Please generate a purchase order to replenish stock immediately.');
    }

    /**
     * Define the database notification content (JSON data).
     */
    public function toArray(object $notifiable): array
    {
        return [
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'sku' => $this->product->sku,
            'current_quantity' => $this->product->quantity,
            'reorder_level' => $this->product->reorder_level,
            'suggested_action' => 'Reorder stock immediately',
            'message' => "Stock is low ({$this->product->quantity} left, reorder level is {$this->product->reorder_level}). Suggested action: Reorder.",
        ];
    }
}