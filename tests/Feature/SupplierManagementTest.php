<?php

use App\Models\Supplier;
use App\Models\User;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('allows a user to create a supplier', function () {
    $response = $this->post(route('suppliers.store'), [
        'name' => 'Test Supplier',
        'contact_person' => 'Test Contact',
        'email' => 'test@supplier.com',
        'phone' => '123-456-7890',
        'address' => '123 Test St',
        'supplier_type' => 'Supply only',
    ]);

    $response->assertRedirect(route('suppliers.index'));
    assertDatabaseHas('suppliers', [
        'name' => 'Test Supplier',
        'supplier_type' => 'Supply only',
    ]);
});

it('allows a user to view suppliers', function () {
    $supplier = Supplier::factory()->create();

    $response = $this->get(route('suppliers.index'));

    $response->assertOk();
    $response->assertSee($supplier->name);
    $response->assertSee($supplier->supplier_type);
});

it('allows a user to view a single supplier', function () {
    $supplier = Supplier::factory()->create();

    $response = $this->get(route('suppliers.show', $supplier));

    $response->assertOk();
    $response->assertSee($supplier->name);
    $response->assertSee($supplier->supplier_type);
});

it('allows a user to update a supplier', function () {
    $supplier = Supplier::factory()->create();

    $response = $this->put(route('suppliers.update', $supplier), [
        'name' => 'Updated Supplier',
        'contact_person' => 'Updated Contact',
        'email' => 'updated@supplier.com',
        'phone' => '098-765-4321',
        'address' => '456 Updated Ave',
        'supplier_type' => 'Subcontractor',
    ]);

    $response->assertRedirect(route('suppliers.show', $supplier));
    assertDatabaseHas('suppliers', [
        'id' => $supplier->id,
        'name' => 'Updated Supplier',
        'supplier_type' => 'Subcontractor',
    ]);
});

it('allows a user to delete a supplier', function () {
    $supplier = Supplier::factory()->create();

    $response = $this->delete(route('suppliers.destroy', $supplier));

    $response->assertRedirect(route('suppliers.index'));
    assertDatabaseMissing('suppliers', ['id' => $supplier->id]);
});

it('requires a name to create a supplier', function () {
    $response = $this->post(route('suppliers.store'), [
        'name' => null,
        'supplier_type' => 'Supply only',
    ]);

    $response->assertSessionHasErrors('name');
});

it('requires a supplier type to create a supplier', function () {
    $response = $this->post(route('suppliers.store'), [
        'name' => 'Test Supplier',
        'supplier_type' => null,
    ]);

    $response->assertSessionHasErrors('supplier_type');
});
