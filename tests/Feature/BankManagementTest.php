<?php

use App\Models\Bank;
use App\Models\User;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

 beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

 it('allows a user to create a bank', function () {
    $response = $this->post(route('banks.store'), [
        'name' => 'Test Bank',
        'branch' => 'Test Branch',
        'address' => '123 Test St',
        'contact_person' => 'Test Contact',
        'bank_number' => '1234567890',
    ]);

    $response->assertRedirect(route('banks.index'));
    assertDatabaseHas('banks', [
        'name' => 'Test Bank',
        'branch' => 'Test Branch',
    ]);
});

 it('allows a user to view banks', function () {
    $bank = Bank::factory()->create();

    $response = $this->get(route('banks.index'));

    $response->assertOk();
    $response->assertSee($bank->name);
    $response->assertSee($bank->branch);
    $response->assertSee($bank->bank_number);
});

 it('allows a user to view a single bank', function () {
    $bank = Bank::factory()->create();

    $response = $this->get(route('banks.show', $bank));

    $response->assertOk();
    $response->assertSee($bank->name);
    $response->assertSee($bank->address);
});

 it('allows a user to update a bank', function () {
    $bank = Bank::factory()->create();

    $response = $this->put(route('banks.update', $bank), [
        'name' => 'Updated Bank',
        'branch' => 'Updated Branch',
        'address' => '456 Updated Ave',
        'contact_person' => 'Updated Contact',
        'bank_number' => '0987654321',
    ]);

    $response->assertRedirect(route('banks.show', $bank));
    assertDatabaseHas('banks', [
        'id' => $bank->id,
        'name' => 'Updated Bank',
        'branch' => 'Updated Branch',
    ]);
});

 it('allows a user to delete a bank', function () {
    $bank = Bank::factory()->create();

    $response = $this->delete(route('banks.destroy', $bank));

    $response->assertRedirect(route('banks.index'));
    assertDatabaseMissing('banks', ['id' => $bank->id]);
});

it('requires a name to create a bank', function () {
    $response = $this->post(route('banks.store'), [
        'name' => null,
        'branch' => 'Test Branch',
        'bank_number' => null,
    ]);

    $response->assertSessionHasErrors('name');
});
