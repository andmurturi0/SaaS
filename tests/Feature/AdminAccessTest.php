<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_dashboard()
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect('/login');
    }

    public function test_user_cannot_access_admin_routes()
    {
        $user = User::factory()->create(['role' => 'user']);
        
        $response = $this->actingAs($user)->get(route('dashboard'));
        $response->assertRedirect('/');
        $response->assertSessionHas('error');
    }

    public function test_admin_can_access_admin_routes()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
        
        try {
            $response = $this->actingAs($admin)->get(route('dashboard'));
            if ($response->status() === 500) {
                // Kjo do të na ndihmojë të shohim gabimin në GitHub logs
                fwrite(STDERR, "Error 500 detected! Check the logs above or the exception trace.\n");
            }
            $response->assertStatus(200);
        } catch (\Exception $e) {
            fwrite(STDERR, "Exception caught: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            throw $e;
        }
    }
}
