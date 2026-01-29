<?php

use App\Models\Discipline;
use App\Models\Project;
use App\Models\User;

use function Pest\Laravel\assertDatabaseHas;

 beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

 it('allows a user to create a project', function () {
    $discipline = Discipline::factory()->create();

    $response = $this->post(route('projects.store'), [
        'project_identifier' => 'P123',
        'name' => 'Test Project',
        'description' => 'This is a test project.',
        'discipline_id' => $discipline->id,
        'client_name' => 'Test Client',
        'project_manager' => 'Test Manager',
        'start_date' => '2026-01-01',
        'end_date' => '2026-12-31',
        'status' => 'Planned',
    ]);

    $response->assertRedirect(route('projects.index'));
    assertDatabaseHas('projects', [
        'project_identifier' => 'P123',
        'name' => 'Test Project',
        'discipline_id' => $discipline->id,
        'client_name' => 'Test Client',
        'project_manager' => 'Test Manager',
        'start_date' => '2026-01-01 00:00:00',
        'end_date' => '2026-12-31 00:00:00',
        'status' => 'Planned',
    ]);
});

 it('allows a user to view projects', function () {
    $discipline = Discipline::factory()->create();
    $project = Project::factory()->create(['discipline_id' => $discipline->id]);

    $response = $this->get(route('projects.index'));

    $response->assertOk();
    $response->assertSee($project->name);
    $response->assertSee($discipline->name);
    $response->assertSee($project->client_name);
    $response->assertSee($project->project_manager);
    $response->assertSee($project->start_date?->format('Y-m-d'));
    $response->assertSee($project->end_date?->format('Y-m-d'));
    $response->assertSee($project->status);
});

 it('allows a user to view a single project', function () {
    $discipline = Discipline::factory()->create();
    $project = Project::factory()->create(['discipline_id' => $discipline->id]);

    $response = $this->get(route('projects.show', $project));

    $response->assertOk();
    $response->assertSee($project->name);
    $response->assertSee($project->discipline->name);
    $response->assertSee($project->client_name);
    $response->assertSee($project->project_manager);
    $response->assertSee($project->start_date?->format('Y-m-d'));
    $response->assertSee($project->end_date?->format('Y-m-d'));
    $response->assertSee($project->status);
});

 it('allows a user to update a project', function () {
    $discipline1 = Discipline::factory()->create();
    $discipline2 = Discipline::factory()->create();
    $project = Project::factory()->create(['discipline_id' => $discipline1->id]);

    $response = $this->put(route('projects.update', $project), [
        'project_identifier' => 'P456',
        'name' => 'Updated Project',
        'description' => 'This is an updated project.',
        'discipline_id' => $discipline2->id,
        'client_name' => 'Updated Client',
        'project_manager' => 'Updated Manager',
        'start_date' => '2026-02-01',
        'end_date' => '2027-01-31',
        'status' => 'Active',
    ]);

    $response->assertRedirect(route('projects.show', $project));
    assertDatabaseHas('projects', [
        'id' => $project->id,
        'project_identifier' => 'P456',
        'name' => 'Updated Project',
        'discipline_id' => $discipline2->id,
        'client_name' => 'Updated Client',
        'project_manager' => 'Updated Manager',
        'start_date' => '2026-02-01 00:00:00',
        'end_date' => '2027-01-31 00:00:00',
        'status' => 'Active',
    ]);
});

 it('allows a user to delete a project', function () {
    $discipline = Discipline::factory()->create();
    $project = Project::factory()->create(['discipline_id' => $discipline->id]);

    $response = $this->delete(route('projects.destroy', $project));

    $response->assertRedirect(route('projects.index'));
    $this->assertSoftDeleted($project);
});

it('requires a discipline to create a project', function () {
    $response = $this->post(route('projects.store'), [
        'project_identifier' => 'P123',
        'name' => 'Test Project',
        'description' => 'This is a test project.',
        'discipline_id' => null,
        'client_name' => 'Test Client',
        'project_manager' => 'Test Manager',
        'start_date' => '2026-01-01',
        'end_date' => '2026-12-31',
        'status' => 'Planned',
    ]);

    $response->assertSessionHasErrors('discipline_id');
});

it('requires a client name to create a project', function () {
    $discipline = Discipline::factory()->create();

    $response = $this->post(route('projects.store'), [
        'project_identifier' => 'P123',
        'name' => 'Test Project',
        'description' => 'This is a test project.',
        'discipline_id' => $discipline->id,
        'client_name' => null,
        'project_manager' => 'Test Manager',
        'start_date' => '2026-01-01',
        'end_date' => '2026-12-31',
        'status' => 'Planned',
    ]);

    $response->assertSessionHasErrors('client_name');
});

it('requires a status to create a project', function () {
    $discipline = Discipline::factory()->create();

    $response = $this->post(route('projects.store'), [
        'project_identifier' => 'P123',
        'name' => 'Test Project',
        'description' => 'This is a test project.',
        'discipline_id' => $discipline->id,
        'client_name' => 'Test Client',
        'project_manager' => 'Test Manager',
        'start_date' => '2026-01-01',
        'end_date' => '2026-12-31',
        'status' => null,
    ]);

    $response->assertSessionHasErrors('status');
});
