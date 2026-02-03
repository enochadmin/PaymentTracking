<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use App\Http\Requests\TeamRequest;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        
        if ($user->hasRole('admin')) {
            $teams = Team::paginate(15);
        } else {
            // Only show teams where the user is the manager
            $teams = Team::where('manager_id', $user->id)->paginate(15);
        }
        
        return view('teams.index', compact('teams'));
    }

    public function create()
    {
        // Only admins can create teams
        if (!auth()->user()->hasRole('admin')) {
             abort(403, 'Unauthorized action.');
        }

        // Get users who can be team managers (not already assigned to teams)
        $availableManagers = User::whereNull('team_id')->get();
        return view('teams.create', compact('availableManagers'));
    }

    public function store(TeamRequest $request)
    {
        if (!auth()->user()->hasRole('admin')) {
             abort(403, 'Unauthorized action.');
        }

        Team::create($request->validated());
        return redirect()->route('teams.index')->with('success', 'Team created successfully.');
    }

    public function show(Team $team)
    {
        $this->authorizeManager($team);
        
        $team->load('users');
        $availableUsers = $this->getAvailableUsers();
        return view('teams.show', compact('team', 'availableUsers'));
    }

    public function addMembersForm(Team $team)
    {
        $this->authorizeManager($team);
        
        $team->load('users', 'manager');
        $availableUsers = $this->getAvailableUsers();
        return view('teams.add_members', compact('team', 'availableUsers'));
    }

    private function getAvailableUsers()
    {
        return User::whereNull('team_id')
            ->whereDoesntHave('roles', function ($q) {
                $q->whereIn('name', ['admin', 'manager']);
            })
            ->get();
    }

    public function addMember(Request $request, Team $team)
    {
        $this->authorizeManager($team);
        
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $user = User::findOrFail($validated['user_id']);

        if ($user->team_id) {
             return back()->withErrors(['user_id' => 'User already has a team.']);
        }

        // if ($user->hasRole('admin') || $user->hasRole('manager')) {
        //     return back()->withErrors(['user_id' => 'Managers and Admins cannot be added to a team.']);
        // }
        // Commenting out explicit role check if we want flexibility, but keeping it is safer. 
        // Let's keep the logic consistent with previous implementation but cleaner.
        if ($user->hasRole('admin')) {
             return back()->withErrors(['user_id' => 'Admins cannot be added to a team.']);
        }
        
        $user->update(['team_id' => $team->id]);

        return redirect()->route('teams.edit', $team)->with('success', 'Member added successfully.');
    }

    public function edit(Team $team)
    {
        $this->authorizeManager($team);

        $team->load('users', 'manager');
        
        // available for manager: anyone not in a team OR the current manager
        $availableManagers = User::whereNull('team_id')
            ->orWhere('id', $team->manager_id)
            ->get();
            
        // available for members: anyone not in a team OR currently in this team
        $availableUsers = User::whereNull('team_id')
            ->orWhere('team_id', $team->id)
            ->orderBy('name')
            ->get();

        return view('teams.edit', compact('team', 'availableManagers', 'availableUsers'));
    }

    public function update(TeamRequest $request, Team $team)
    {
        $this->authorizeManager($team);

        $validated = $request->validated();
        
        // Only Admin can change the Manager? 
        // The requirement says "Project managers... edit and view their teams". 
        // If a Manager changes the Manager to someone else, they lose access.
        // That seems acceptable/expected, or we restrict Manager change to Admin only.
        // For now, I'll allow it as requested "full edit", but typically Managers shouldn't replace themselves.
        // However, if I enforce "Admin only" for manager_id, I need to know.
        // User said: "I want the project managers ... have access to add members, edit and view their teams".
        // It doesn't explicitly forbid changing the manager name (maybe correcting a name).
        // I will keep it as is.
        
        // Update Team details
        $updateData = ['name' => $validated['name']];
        
        // Only allow updating manager_id if user is Admin
        if (auth()->user()->hasRole('admin')) {
             $updateData['manager_id'] = $validated['manager_id'] ?? null;
        }
        
        $team->update($updateData);
        
        // Ensure manager is also in the team if not already (Only relevant if admin changed it)
        if (auth()->user()->hasRole('admin') && $team->manager_id) {
            $manager = User::find($team->manager_id);
            if ($manager && $manager->team_id !== $team->id) {
                 $manager->update(['team_id' => $team->id]);
            }
        }

        return redirect()->route('teams.edit', $team)->with('success', 'Team updated successfully.');
    }
    
    public function removeMember(Team $team, User $user)
    {
        $this->authorizeManager($team);

        if ($user->team_id === $team->id) {
            $user->update(['team_id' => null]);
            return back()->with('success', 'Member removed successfully.');
        }
        return back()->with('error', 'User is not a member of this team.');
    }

    public function destroy(Team $team)
    {
        if (!auth()->user()->hasRole('admin')) {
             abort(403, 'Only Admins can delete teams.');
        }

        // Release users
        User::where('team_id', $team->id)->update(['team_id' => null]);
        
        $team->delete();
        return redirect()->route('teams.index')->with('success', 'Team deleted successfully.');
    }

    private function authorizeManager(Team $team)
    {
        $user = auth()->user();
        if (!$user->hasRole('admin') && !$team->isManager($user)) {
            abort(403, 'Unauthorized action.');
        }
    }
}
