<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CloneTest extends TestCase
{
    use DatabaseMigrations;
    use CloneTestData;


    public function testCloneAuthorAndRelatedModels(): void
    {
        [$authorId, $expectedResponse] = $this->getCloneAuthorAndRelatedModelsRequestData();

        $response = $this->getJson(
            uri: route(
                name: 'cloneAuthor',
                parameters: ['id' => $authorId]
            )
        );

        $response->assertStatus(201);
        $response->assertJson($expectedResponse);
    }

    public function testClonePostAndRelatedModels(): void
    {
        [$postId, $expectedResponse] = $this->getClonePostAndRelatedModelsRequestData();

        $response = $this->getJson(
            uri: route(
                name: 'clonePost',
                parameters: ['id' => $postId]
            )
        );

        $response->assertStatus(201);
        $response->assertJson($expectedResponse);
    }
}
