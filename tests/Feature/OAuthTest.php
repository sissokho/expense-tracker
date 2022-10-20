<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Socialite;
use Tests\TestCase;

class OAuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function user_can_be_redirected_to_github_oauth(): void
    {
        $response = $this->get(route('oauth.redirect'));

        $this->assertStringContainsString('https://github.com/login/oauth/authorize', $response->getTargetUrl());
    }

    /**
     * @test
     */
    public function new_user_can_be_created_with_github_oauth_details(): void
    {
        $oauthUser = $this->fakeOAuthUser(email: 'Gary Lois', name: 'gary@gmail.com');

        $response = $this->get(route('oauth.callback'));

        $response->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas(User::class, [
            'email' => $oauthUser->email,
            'name' => $oauthUser->name,
        ]);
    }

    /**
     * @test
     */
    public function existing_user_can_login_with_github_oauth(): void
    {
        $user = User::factory()
            ->create([
                'name' => 'Gary Lois',
                'email' => 'gary@gmail.com',
                'password' => Hash::make('gary1234'),
            ]);

        $this->fakeOAuthUser(email: 'Gary Lois', name: 'gary@gmail.com');

        $response = $this->get(route('oauth.callback'));

        $response->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas(User::class, [
            'email' => $user->email,
            'name' => $user->name,
        ]);

        // User's password does not change
        $this->assertTrue(Hash::check('gary1234', $user->password));
    }

    private function fakeOAuthUser(string $email, string $name): \Laravel\Socialite\Two\User
    {
        $oauthUser = Mockery::mock(\Laravel\Socialite\Two\User::class);
        $oauthUser->name = $name;
        $oauthUser->email = $email;

        Socialite::shouldReceive('driver->user')
            ->andReturn($oauthUser);

        return $oauthUser;
    }
}
