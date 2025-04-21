<?php

namespace App\Http\Controllers;

use App\Enums\UserTypes;
use App\Http\Requests\AdminAdminEventsPageRequest;
use App\Http\Requests\AdminAdminUsersPageRequest;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $dashboardContent = [];

        $dashboardContent = [
            'users' => [
                'totalUsers' => User::all()->count(),
                'totalClients' => User::where('user_type', UserTypes::CLIENT)->count(),
                'totalOrganizers' => User::where('user_type', UserTypes::ORGANIZER)->count(),
            ],
            'events' => [
                'totalEvents' => Event::all()->count(),
                'totalUpcomingEvents' => Event::where('date', '>', now())->count(),
                'totalOngoingEvents' => Event::where('date', '<=', now())->where('date', '>=', now()->subDays(1))->count(),
                'totalPastEvents' => Event::where('date', '<', now())->count(),
            ],
        ];

        return view('admin.dashboard', compact('dashboardContent'));
    }

    /**
     * Show the events page.
     *
     * @return \Illuminate\View\View
     */
    public function events(AdminAdminEventsPageRequest $request)
    {
        $events = Event::all();

        return view('admin.events', compact('events'));
    }

    /**
     * Show the users page.
     *
     * @return \Illuminate\View\View
     */
    public function users(AdminAdminUsersPageRequest $request)
    {
        $validated = $request->validated();
        $search = $validated['search'] ?? null;
        $sort = $validated['sort'] ?? null;
        $userType = $validated['user_type'] ?? null;
        $query = User::query();
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        }
        if ($userType) {
            $query->where('user_type', $userType);
        }
        if ($sort) {
            $query->orderBy('created_at', $sort);
        } else {
            $query->orderBy('created_at', 'desc');
        }
        $users = $query->paginate(10);

        return view('admin.users', compact('users'));
    }

}
