<?php

namespace Tests\Feature;

use Tests\TestCase;

class AyanAuthTest extends TestCase
{
    public function test_guest_api_request_returns_unauthorized_json(): void
    {
        $response = $this->get('/api/ayan/trips');

        $response
            ->assertUnauthorized()
            ->assertJson([
                'message' => 'Unauthenticated.'
            ]);
    }
}
