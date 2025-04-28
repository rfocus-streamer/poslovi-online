<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Service;
use App\Models\Project;
use App\Models\Package;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $activeTab = request()->input('tab', 'users');

        $users = User::paginate(10)
            ->setPageName('users_page')
            ->appends(['tab' => 'users']);

        $services = Service::paginate(10)
            ->setPageName('services_page')
            ->appends(['tab' => 'services']);

        $projects = Project::paginate(10)
            ->setPageName('projects_page')
            ->appends(['tab' => 'projects']);

        $packages = Package::paginate(10)
            ->setPageName('packages_page')
            ->appends(['tab' => 'packages']);

        return view('admin.dashboard', compact(
            'users',
            'services',
            'projects',
            'packages',
            'activeTab'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
