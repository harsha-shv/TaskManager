<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    // Fetch all projects
    public function index()
    {
        return response()->json(Project::all());
    }

    // Store a new project
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string'
        ]);

        $project = Project::create(['name' => $request->name]);

        return response()->json($project, 201);
    }
}
