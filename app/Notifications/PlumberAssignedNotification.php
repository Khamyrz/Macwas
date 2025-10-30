<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;

class PlumberAssignedNotification extends Notification
{
    use Queueable;

    protected $customer;
    protected $plumber;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $customer, User $plumber)
    {
        $this->customer = $customer;
        $this->plumber = $plumber;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Customer Assignment - Water Connection')
            ->greeting('Hello ' . $this->plumber->first_name . '!')
            ->line('You have been assigned to a new customer for water connection setup.')
            ->line('Customer Details:')
            ->line('• Name: ' . $this->customer->full_name)
            ->line('• Customer Number: ' . $this->customer->customer_number)
            ->line('• Address: ' . $this->customer->address)
            ->line('• Phone: ' . $this->customer->phone_number)
            ->line('• Email: ' . $this->customer->email)
            ->action('View Dashboard', url('/plumber/dashboard'))
            ->line('Please contact the customer to schedule the water connection setup.')
            ->line('Thank you for your service!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'plumber_assigned',
            'customer_id' => $this->customer->id,
            'customer_name' => $this->customer->full_name,
            'customer_number' => $this->customer->customer_number,
            'customer_address' => $this->customer->address,
            'customer_phone' => $this->customer->phone_number,
            'customer_email' => $this->customer->email,
            'message' => 'You have been assigned to customer ' . $this->customer->full_name . ' (#' . $this->customer->customer_number . ') for water connection setup.',
            'action_url' => '/plumber/dashboard',
        ];
    }
}
