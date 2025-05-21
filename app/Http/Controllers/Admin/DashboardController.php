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
    public function index(Request $request)
    {
        // Proveri da li je korisnik autentifikovan i da li ima rolu 'admin'
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Access denied');
        }

        $activeTab = request()->input('tab', 'users');

        $users = User::paginate(10, ['*'], 'page', $request->input('users_page', 1))
            ->setPageName('users_page')
            ->appends(['tab' => 'users']);

        $services = Service::paginate(10, ['*'], 'page', $request->input('services_page', 1))
            ->setPageName('services_page')
            ->appends(['tab' => 'services']);

        $projects = Project::paginate(10, ['*'], 'page', $request->input('projects_page', 1))
            ->setPageName('projects_page')
            ->appends(['tab' => 'projects']);

        $packages = Package::paginate(10, ['*'], 'page', $request->input('packages_page', 1))
            ->setPageName('packages_page')
            ->appends(['tab' => 'packages']);

        // FOLDER => [Model, kolona]
            $folderMap = [
                'attachments' => [Ticket::class, 'attachment'],
                'complaints' => [Complaint::class, 'attachment'],
                'project_files' => [ProjectFile::class, 'file_path'],
                'services' => [ServiceImage::class, 'image_path'],
                'user' => [User::class, 'avatar'],
                'response-attachments' => [TicketResponse::class, 'attachment'],
            ];

            $allFiles = [];
            $unusedFiles = [];

            foreach ($folderMap as $folder => [$model, $column]) {
                $files = Storage::disk('public')->files($folder); // npr. tickets/file1.pdf

                // Učitaj sve vrednosti iz baze za odgovarajuću kolonu
                $dbValues = $model::pluck($column)->filter()->toArray(); // filter() uklanja null

                // Izvuci samo ime fajla radi sigurnosti (ako je u bazi samo ime ili puna putanja)
                $usedFilenames = array_map('basename', $dbValues);

                foreach ($files as $filePath) {
                    $allFiles[] = $filePath;

                    $filename = basename($filePath);

                    if (!in_array($filename, $usedFilenames)) {
                        $unusedFiles[] = $filePath;
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
        // Proveri da li je korisnik autentifikovan i da li ima rolu 'admin'
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Access denied');
        }
        return view('admin.users.profile', compact('user'));
    }

    public function deposit(User $user)
    {
        // Proveri da li je korisnik autentifikovan i da li ima rolu 'admin'
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Access denied');
        }

        return view('admin.users.deposit', compact('user'));
    }

    public function depositAmount(Request $request, User $user)
    {
        // Proveri da li je korisnik autentifikovan i da li ima rolu 'admin'
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Access denied');
        }

        $depositAmount = $request->amount;
        $user->deposits += $depositAmount;
        $user->save();
        return redirect()->back()->with('success', 'Depozit za '.$user->firstname.' '.$user->lastname.' je uspešno dodat!')
                    ->withInput(); // Ovaj .withInput() omogućava da sačuvaš podatke forme nakon što se stranica učita ponovo
    }


    /**
     * Update the specified resource in storage.
     */
    public function updateProfile(Request $request, User $user)
    {
        // Proveri da li je korisnik autentifikovan i da li ima rolu 'admin'
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Access denied');
        }

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

    public function deleteFile(Request $request)
    {
        // Proveri da li je korisnik autentifikovan i da li ima rolu 'admin'
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Access denied');
        }

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
