@extends('layouts.app')

@section('content')
<div class="container">
<h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 mb-6">Create Project</h1>
    <form method="POST" action="{{ route('projects.store') }}" class="space-y-4">
        @csrf
        <div><label for="project_identifier">Project ID</label><input type="text" id="project_identifier" name="project_identifier" value="{{ old('project_identifier') }}"></div>
        <div><label for="name">Name</label><input type="text" id="name" name="name" value="{{ old('name') }}"></div>
        <div><label for="description">Description</label><textarea id="description" name="description">{{ old('description') }}</textarea></div>
        <div>
            <label for="discipline_id">Discipline</label>
            <select name="discipline_id" id="discipline_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:text-gray-100">
                <option value="">Select a Discipline</option>
                @foreach($disciplines as $discipline)
                    <option value="{{ $discipline->id }}" {{ old('discipline_id') == $discipline->id ? 'selected' : '' }}>{{ $discipline->name }}</option>
                @endforeach
            </select>
        </div>
        <div><label for="client_name">Client Name</label><input type="text" id="client_name" name="client_name" value="{{ old('client_name') }}"></div>
        <div><label for="project_manager">Project Manager</label><input type="text" id="project_manager" name="project_manager" value="{{ old('project_manager') }}"></div>
        <div><label for="start_date">Start Date</label><input type="date" id="start_date" name="start_date" value="{{ old('start_date') }}"></div>
        <div><label for="end_date">End Date</label><input type="date" id="end_date" name="end_date" value="{{ old('end_date') }}"></div>
        <div>
            <label for="status">Status</label>
            <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:text-gray-100">
                @foreach(['Planned', 'Active', 'Closed'] as $statusOption)
                    <option value="{{ $statusOption }}" {{ old('status') == $statusOption ? 'selected' : '' }}>{{ $statusOption }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit">Create</button>
    </form>
</div>
@endsection
