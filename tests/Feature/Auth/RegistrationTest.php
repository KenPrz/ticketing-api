<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Enums\UserTypes;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that users can register with valid data.
     *
     * This test sends a POST request to the /api/register endpoint with valid user data
     * and verifies the following:
     * - The response status is 201 (Created).
     * - The response JSON structure contains a 'token' key.
     * - The 'token' key in the response JSON is not empty.
     * - A user with the provided name and email exists in the 'users' database table.
     * - The user has a 'user_type' of 'CLIENT'.
     * - A personal access token was created for the user in the 'personal_access_tokens' database table.
     *
     * @return void
     */
    public function test_users_can_register_with_valid_data(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $response
            ->assertStatus(201)
            ->assertJsonStructure(['token'])
            ->assertJson([
                'token' => true // Checks if token exists and is not empty
            ]);

        // Verify user was created in database
        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'user_type' => UserTypes::CLIENT->value
        ]);

        // Verify token was created
        $user = User::where('email', 'test@example.com')->first();
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_type' => User::class,
            'tokenable_id' => $user->id
        ]);
    }

    /**
     * Test to ensure users cannot register with an existing email.
     *
     * This test first creates a user with a specific email address.
     * Then, it attempts to register a new user with the same email address.
     * The expected outcome is a 422 Unprocessable Entity status code,
     * and a validation error for the email field.
     *
     * @return void
     */
    public function test_users_cannot_register_with_existing_email(): void
    {
        // Create a user first
        User::factory()->create([
            'email' => 'test@example.com'
        ]);

        // Try to register with same email
        $response = $this->postJson('/api/register', [
            'name' => 'Another User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test to ensure that the password must be confirmed during registration.
     *
     * This test sends a POST request to the '/api/register' endpoint with a mismatched
     * 'password' and 'password_confirmation' field. It asserts that the response status
     * is 422 (Unprocessable Entity) and that the 'password' field has validation errors.
     *
     * @return void
     */
    public function test_password_must_be_confirmed(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different_password'
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /**
     * Test to ensure that the password must be a minimum of eight characters.
     *
     * This test sends a POST request to the '/api/register' endpoint with a password
     * that is shorter than the required minimum length. It then asserts that the response
     * status is 422 (Unprocessable Entity) and that the validation error for the 'password'
     * field is present in the response.
     *
     * @return void
     */
    public function test_password_must_be_minimum_eight_characters(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'short',
            'password_confirmation' => 'short'
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /**
     * Test to ensure that the 'name' field is required during registration.
     *
     * This test sends a POST request to the '/api/register' endpoint with
     * the 'email', 'password', and 'password_confirmation' fields, but without
     * the 'name' field. It asserts that the response status is 422 (Unprocessable Entity)
     * and that the 'name' field is present in the validation errors.
     *
     * @return void
     */
    public function test_name_is_required(): void
    {
        $response = $this->postJson('/api/register', [
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /**
     * Test to ensure that the email field must be in a valid format during registration.
     *
     * This test sends a POST request to the '/api/register' endpoint with an invalid email format.
     * It asserts that the response status is 422 (Unprocessable Entity) and that the response
     * contains a validation error for the 'email' field.
     *
     * @return void
     */
    public function test_email_must_be_valid_format(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test to ensure that the name field cannot exceed 255 characters during registration.
     *
     * This test sends a POST request to the /api/register endpoint with a name that is 256 characters long.
     * It expects a 422 Unprocessable Entity status code and a validation error for the name field.
     *
     * @return void
     */
    public function test_name_cannot_exceed_255_characters(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => str_repeat('a', 256),
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /**
     * Test to ensure that the email field cannot exceed 255 characters.
     *
     * This test sends a POST request to the /api/register endpoint with an email
     * address that is exactly 255 characters long. The expected behavior is that
     * the server will respond with a 422 Unprocessable Entity status and a 
     * validation error for the email field.
     *
     * @return void
     */
    public function test_email_cannot_exceed_255_characters(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => str_repeat('a', 246) . '@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }
}