<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that users can log in with valid credentials.
     *
     * This test creates a user with a known email and password, then attempts to log in
     * with those credentials. It verifies that the response status is 200, the response
     * contains a token, and the token exists in the database.
     *
     * @return void
     */
    public function test_users_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure(['token'])
            ->assertJson([
                'token' => true // Checks if token exists and is not empty
            ]);

        // Verify the token exists in the database
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_type' => User::class,
            'tokenable_id' => $user->id
        ]);
    }

    /**
     * Test to ensure users cannot login with an invalid password.
     *
     * This test creates a user with a known email and password, then attempts
     * to login with the correct email but an incorrect password. The expected
     * outcome is a 401 Unauthorized status and a JSON response indicating
     * that the provided credentials are incorrect.
     *
     * @return void
     */
    public function test_users_cannot_login_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'wrong_password'
        ]);

        $response
            ->assertStatus(401)
            ->assertJson([
                'message' => 'The provided credentials are incorrect.'
            ]);
    }

    /**
     * Test to ensure users cannot login with an invalid email.
     *
     * This test creates a user with a specific email and password,
     * then attempts to login with an incorrect email but the same password.
     * It asserts that the response status is 401 (Unauthorized) and
     * the JSON response contains a message indicating incorrect credentials.
     *
     * @return void
     */
    public function test_users_cannot_login_with_invalid_email(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'wrong@example.com',
            'password' => 'password123'
        ]);

        $response
            ->assertStatus(401)
            ->assertJson([
                'message' => 'The provided credentials are incorrect.'
            ]);
    }

    /**
     * Test to ensure that the login endpoint requires an email field.
     *
     * This test sends a POST request to the /api/login endpoint without an email,
     * only providing a password. It asserts that the response status is 422
     * (Unprocessable Entity) and that the response contains a validation error
     * for the missing email field.
     *
     * @return void
     */
    public function test_login_validation_requires_email(): void
    {
        $response = $this->postJson('/api/login', [
            'password' => 'password123'
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test to ensure that the login endpoint requires a password.
     *
     * This test sends a POST request to the '/api/login' endpoint with only an email
     * and expects a 422 Unprocessable Entity status code in response. It also checks
     * that the response contains a validation error for the 'password' field.
     *
     * @return void
     */
    public function test_login_validation_requires_password(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com'
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /**
     * Test to ensure that the login endpoint requires a valid email format.
     *
     * This test sends a POST request to the '/api/login' endpoint with an invalid email format.
     * It asserts that the response status is 422 (Unprocessable Entity) and that the response
     * contains a validation error for the 'email' field.
     *
     * @return void
     */
    public function test_login_validation_requires_valid_email_format(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'invalid-email',
            'password' => 'password123'
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }
}
