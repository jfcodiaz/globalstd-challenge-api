<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;

class HomeControllerTest extends TestCase
{
    public function test_it_redirects_to_api_doc_route()
    {
        $response = $this->get('/');

        $response->assertRedirect(url('/api/docs'));
    }
}
