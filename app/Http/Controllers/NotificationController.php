<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function get_admin_notifications()
    {
        $admin = Auth::guard('admin')->user();
        if ($admin) {
            $notificationObjects = [];
            $notifications = $admin->notifications->where('notifiable_type', 'App\Models\Admin');
            foreach ($notifications as $notification) {
                $notificationObjects[] = [
                    'message' => 'A new job has been posted by ' . $notification->data['company_name'],
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'notifiable_type' => $notification->notifiable_type,
                    'notifiable_id' => $notification->notifiable_id,
                    'data' => $notification->data,
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at,
                    'updated_at' => $notification->updated_at,
                ];
            }
            return response()->json([
                'data' => $notificationObjects
            ], 200);
        } else {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }
    }
}
