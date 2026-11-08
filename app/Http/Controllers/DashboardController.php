<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use App\Services\ServerStatusService;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Everybody that logs in lands here, but only the admins get the statistics: they are deferred props, so the page
     * is shown right away and each group is loaded on its own. The rest of the users get the page without them.
     * Statistics are not a listing of entities, so they are not behind the Filter System.
     * The status of the Minecraft server is for everybody, deferred too, and the page polls it.
     */
    public function show(DashboardService $dashboard, ServerStatusService $serverStatus): Response
    {
        $server = Inertia::defer(fn () => $serverStatus->current(), 'server');

        if (Gate::denies('view-statistics')) {
            return Inertia::render('Dashboard/DashboardPage', ['server' => $server]);
        }

        return Inertia::render('Dashboard/DashboardPage', [
            'server' => $server,
            'stats' => Inertia::defer(fn () => $dashboard->stats(), 'stats'),
            'usersByMonth' => Inertia::defer(fn () => $dashboard->usersByMonth(), 'charts'),
        ]);
    }
}
