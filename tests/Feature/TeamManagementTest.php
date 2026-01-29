<?php

use App\Models\Team;
use App\Models\User;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('allows a user to create a team', function () {
    $response = $this->post(route('teams.store'), [
        'name' => 'Test Team',
    ]);

    $response->assertRedirect(route('teams.index'));
    assertDatabaseHas('teams', [
        'name' => 'Test Team',
    ]);
});

it('allows a user to view teams', function () {
    $team = Team::factory()->create();

    $response = $this->get(route('teams.index'));

    $response->assertOk();
    $response->assertSee($team->name);
});

it('allows a user to view a single team', function () {
    $team = Team::factory()->create();

    $response = $this->get(route('teams.show', $team));

    $response->assertOk();
    $response->assertSee($team->name);
});

it('allows a user to update a team', function () {
    $team = Team::factory()->create();

    $response = $this->put(route('teams.update', $team), [
        'name' => 'Updated Team',
    ]);

    $response->assertRedirect(route('teams.show', $team));
    assertDatabaseHas('teams', [
        'id' => $team->id,
        'name' => 'Updated Team',
    ]);
});

it('allows a user to delete a team', function () {
    $team = Team::factory()->create();

    $response = $this->delete(route('teams.destroy', $team));

    $response->assertRedirect(route('teams.index'));
    assertDatabaseMissing('teams', ['id' => $team->id]);
});

it('requires a name to create a team', function () {
    $response = $this->post(route('teams.store'), [
        'name' => null,
    ]);

    $response->assertSessionHasErrors('name');
});

it('requires a unique name to create a team', function () {
    Team::factory()->create(['name' => 'Existing Team']);

    $response = $this->post(route('teams.store'), [
        'name' => 'Existing Team',
    ]);

    $response->assertSessionHasErrors('name');
});