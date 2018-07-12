<?php

namespace App\Http\Controllers;

use App\Models\Project;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::latest()->get();

        $projects = $projects->map(function ($project) {
            if (mb_strlen($project->title) > 20) {
                $project->title = mb_substr($project->title, 0, 17) . '...';
            }

            $project->thumbnail = asset("assets/images/{$project->thumbnail}");

            return $project;
        });

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
