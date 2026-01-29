@extends('layouts.app')

@section('content')
<div class="container">
<h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 mb-4">{{ $project->name }} ({{ $project->project_identifier }})</h1>
    <p class="text-gray-700 dark:text-gray-300 mb-2">Discipline: {{ $project->discipline->name }}</p>
    <p class="text-gray-700 dark:text-gray-300 mb-2">Client Name: {{ $project->client_name }}</p>
    <p class="text-gray-700 dark:text-gray-300 mb-2">Project Manager: {{ $project->project_manager }}</p>
    <p class="text-gray-700 dark:text-gray-300 mb-2">Start Date: {{ $project->start_date?->format('Y-m-d') }}</p>
    <p class="text-gray-700 dark:text-gray-300 mb-2">End Date: {{ $project->end_date?->format('Y-m-d') }}</p>
    <p class="text-gray-700 dark:text-gray-300 mb-6">Status: {{ $project->status }}</p>
    <p class="text-gray-700 dark:text-gray-300 mb-6">{{ $project->description }}</p>
    <a href="{{ route('projects.edit', $project) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-indigo-700 dark:hover:bg-indigo-600">Edit</a>
</div>
@endsection
