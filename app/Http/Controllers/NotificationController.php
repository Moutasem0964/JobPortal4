<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notifications;

class NotificationController extends Controller
{
    public function get_notifications()
    {
        $notificationObjects = [];
        if ($admin = Auth::guard('admin')->user()) {

            $notifications = $admin->notifications->where('notifiable_type', 'App\Models\Admin');
            foreach ($notifications as $notification) {
                $notificationObjects[] = [
                    // 'message' => 'A new job has been posted by ' . $notification->data['company_name'],
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
        } elseif ($company = Auth::guard('company')->user()) {
            $notifications = $company->notifications->where('notifiable_type', 'App\Models\Company');
            foreach ($notifications as $notification) {
                $notificationObjects[] = [
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
        } elseif ($user = Auth::guard('user')->user()) {
        } else {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }
    }

    public function markAsRead(Request $request)
    {
        if ($user = Auth::guard('admin')->user() ?? Auth::guard('company')->user() ?? Auth::guard('user')->user()) {
            $notification = $user->notifications()->find($request->id);
            if ($notification) {
                $notification->read_at = now();
                $notification->save();
                return response()->json([
                    'message' => 'Notification marked as read'
                ], 200);
            }
            return response()->json([
                'message' => 'Notification not found'
            ], 404);
        } else {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }
    }
}
