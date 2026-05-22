<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that guest users are redirected to the login page.
     */
    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect(route('login'));
    }

    /**
     * Test that Member level users receive a 403 Forbidden.
     */
    public function test_member_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create([
            'level' => 'Member',
        ]);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertStatus(403);
    }

    /**
     * Test that Admin level users can access the dashboard.
     */
    public function test_admin_can_access_admin_dashboard(): void
    {
        $user = User::factory()->create([
            'level' => 'Admin',
        ]);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertStatus(200);
    }
}
