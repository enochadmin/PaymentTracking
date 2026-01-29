<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectRequest;
use App\Models\Project;
use App\Models\Discipline;

class ProjectController extends Controller
{
    // public function __construct()
    // {
    //     $perm = \App\Http\Middleware\CheckPermission::class;
    //     $this->middleware($perm . ':projects.index')->only('index', 'show');
    //     $this->middleware($perm . ':projects.create')->only('create', 'store');
    //     $this->middleware($perm . ':projects.update')->only('edit', 'update');
    //     $this->middleware($perm . ':projects.delete')->only('destroy');
    // }

    public function index()
    {
        $projects = Project::paginate(15);
        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        $disciplines = Discipline::all();
        return view('projects.create', compact('disciplines'));
    }

    public function store(ProjectRequest $request)
    {
        $project = Project::create($request->validated());
        return redirect()->route('projects.index')->with('success', 'Project created');
    }

    public function show(Project $project)
    {
        return view('projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $disciplines = Discipline::all();
        return view('projects.edit', compact('project', 'disciplines'));
    }

    public function update(ProjectRequest $request, Project $project)
    {
        $project->update($request->validated());
        return redirect()->route('projects.show', $project)->with('success', 'Project updated');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Project removed');
    }
}
