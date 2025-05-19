<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Service;
use App\Models\ServiceImage;
use App\Models\Project;
use App\Models\Package;
use App\Models\Ticket;
use App\Models\TicketResponse;
use App\Models\Complaint;
use App\Models\ProjectFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

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

        // Definicija foldera i odgovarajućih modela + kolona
        $foldersMap = [
            'tickets' => [Ticket::class, 'attachment'],
            'complaints' => [Complaint::class, 'attachment'],
            'project_files' => [ProjectFile::class, 'file_path'],
            'services' => [ServiceImage::class, 'image_path'],
            'user' => [User::class, 'avatar'],
            'ticket_responses' => [TicketResponse::class, 'attachment'],
        ];

        $unusedFiles = [];

        foreach ($foldersMap as $folder => [$model, $column]) {
            // Skeniraj sve fajlove iz foldera (npr. tickets/)
            $files = Storage::disk('public')->files($folder);

            // Učitaj sve vrednosti iz kolone za poređenje
            $usedValues = $model::pluck($column)->toArray();
            $usedFilenames = array_map('basename', $usedValues); // sigurnost

            // Provera svakog fajla da li se koristi
            foreach ($files as $filePath) {
                $filename = basename($filePath);

                if (!in_array($filename, $usedFilenames)) {
                    $unusedFiles[] = $filePath; // zadrži punu putanju, npr. "tickets/file1.pdf"
                }
            }
        }

        return view('admin.dashboard', compact(
            'users',
            'services',
            'projects',
            'packages',
            'activeTab',
            'unusedFiles',
        ));
    }

    public function profile(User $user)
    {
        return view('admin.users.profile', compact('user'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function updateProfile(Request $request, User $user)
    {
        // Ažuriranje ostalih podataka
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->phone = $request->phone;
        $user->street = $request->street;
        $user->city = $request->city;
        $user->country = $request->country;

        // Čuvanje promena u bazi
        $user->save();

        // Vraćamo korisnika na isti tab (pretpostavljam da je tab u URL-u sa parametrom 'tab')
        return redirect()->back()->with('success', 'Profil uspešno ažuriran!')
                    ->withInput(); // Ovaj .withInput() omogućava da sačuvaš podatke forme nakon što se stranica učita ponovo
    }

    public function delete(Request $request)
    {
        $file = $request->input('file_path');

        if (Storage::disk('public')->exists($file)) {
            Storage::disk('public')->delete($file);
            return back()->with('success', 'Fajl je uspešno obrisan.');
        }

        return back()->with('error', 'Fajl nije pronađen.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
