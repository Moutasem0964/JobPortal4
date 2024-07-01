<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Job;
use App\Models\User;

class NewApply extends Notification
{
    use Queueable;

    protected $job;
    protected $user;

    public function __construct( User $user,Job $job)
    {
        $this->job = $job;
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database',];
    }

    public function toDatabase(object $notifiable)
    {
        return [

            'message' => 'a New Apply from ' . $this->user->first_name . ' ' . $this->user->last_name,
            'user_id'=>$this->user->id,
            'job_id' => $this->job->id,
            'job_title' => $this->job->job_title,
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    // public function toMail(object $notifiable): MailMessage
    // {
    //     return (new MailMessage)
    //                 ->line('The introduction to the notification.')
    //                 ->action('Notification Action', url('/'))
    //                 ->line('Thank you for using our application!');
    // }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
