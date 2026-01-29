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
        $teams = Team::paginate(15);
        return view('teams.index', compact('teams'));
    }

    public function create()
    {
        // Get users who can be team managers (not already assigned to teams)
        $availableManagers = User::whereNull('team_id')->get();
        return view('teams.create', compact('availableManagers'));
    }

    public function store(TeamRequest $request)
    {
        Team::create($request->validated());
        return redirect()->route('teams.index')->with('success', 'Team created successfully.');
    }

    public function show(Team $team)
    {
        $team->load('users');
        $availableUsers = $this->getAvailableUsers();
        return view('teams.show', compact('team', 'availableUsers'));
    }

    public function addMembersForm(Team $team)
    {
        // Check if user is admin or team manager
        if (!auth()->user()->hasRole('admin') && !$team->isManager(auth()->user())) {
            return redirect()->route('teams.index')->with('error', 'Only the team manager can add members.');
        }
        
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
        // Check if user is admin or team manager
        if (!auth()->user()->hasRole('admin') && !$team->isManager(auth()->user())) {
            return back()->with('error', 'Only the team manager can add members.');
        }
        
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $user = User::findOrFail($validated['user_id']);

        if ($user->team_id) {
             return back()->withErrors(['user_id' => 'User already has a team.']);
        }

        if ($user->hasRole('admin') || $user->hasRole('manager')) {
            return back()->withErrors(['user_id' => 'Managers and Admins cannot be added to a team.']);
        }
        
        $user->update(['team_id' => $team->id]);

        return redirect()->route('teams.show', $team)->with('success', 'Member added successfully.');
    }

    public function edit(Team $team)
    {
        return view('teams.edit', compact('team'));
    }

    public function update(TeamRequest $request, Team $team)
    {
        $team->update($request->validated());
        return redirect()->route('teams.show', $team)->with('success', 'Team updated successfully.');
    }

    public function destroy(Team $team)
    {
        $team->delete();
        return redirect()->route('teams.index')->with('success', 'Team deleted successfully.');
    }
}
