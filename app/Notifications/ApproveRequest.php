<?php

namespace App\Notifications;

use App\Models\Job;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class ApproveRequest extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    private $job;

    public function __construct(Job $job)
    {
        $this->job = $job;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toDatabase(object $notifiable)
    {
        return [
            'company' => $this->job->company()->first()->company_Name,
            'job_id' => $this->job->id,
            'job_title' => $this->job->job_title,
        ];
    }

    // public function toBroadcast($notifiable)
    // {
    //     $company = $this->job->company()->first()->company_Name; // Fix the method name
    //     Log::info('toBroadcast notification');
    //     return new BroadcastMessage([
    //         'message' => 'A new job has been posted by ' . $company,
    //         'job_id' => $this->job->id,
    //         'job_title' => $this->job->title,
    //     ]);
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
