<?php

namespace App\Http\Controllers;

use App\Models\Project;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::latest()->get();

        return view('projects.index', [
            'projects' => $projects,
            'title' => 'Portfolio',
        ]);
    }

    public function show(Project $project)
    {
        return view('projects.single', [
            'project' => $project,
            'title' => page_title($project->title),
        ]);
    }
}
