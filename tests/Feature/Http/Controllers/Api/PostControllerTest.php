<?php

namespace Tests\Feature\Http\Controllers\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Post;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testStore()
    {
        // $this->withoutExceptionHandling();
        $response = $this->json('POST', '/api/posts', [
            'title' => 'El post de prueba'
        ]);

        $response->assertJsonStructure(['id', 'title', 'created_at', 'updated_at'])
            ->assertJson(['title' => 'El post de prueba'])
            ->assertStatus(201); // OK, creado un recurso

        $this->assertDatabaseHas('posts', ['title' => 'El post de prueba']);
    }

    public function testValidateTitle()
    {
        $response = $this->json('POST', '/api/posts', [
            'title' => ''
        ]);

        $response->assertStatus(422); // Estatus HTTP 422 - Fue imposible completarla
        $response->assertJsonValidationErrors('title');
    }

    public function testShow()
    {
        $post = factory(Post::class)->create();

        $response = $this->json('GET', "/api/posts/$post->id"); // id =  1

        $response->assertJsonStructure(['id', 'title', 'created_at', 'updated_at'])
            ->assertJson(['title' => $post->title])
            ->assertStatus(200); // OK
    }

    public function test404Show()
    {
        $response = $this->json('GET', '/api/posts/1000');
        $response->assertStatus(404); // Not found
    }

    public function testUpdate()
    {
        // $this->withoutExceptionHandling();
        $post = factory(Post::class)->create();

        $response = $this->json('PUT', "/api/posts/$post->id", [
            'title' => 'nuevo'
        ]);

        $response->assertJsonStructure(['id', 'title', 'created_at', 'updated_at'])
            ->assertJson(['title' => 'nuevo'])
            ->assertStatus(200); // OK

        $this->assertDatabaseHas('posts', ['title' => 'nuevo']);
    }

    public function testDelete()
    {
        // $this->withoutExceptionHandling();
        $post = factory(Post::class)->create();

        $response = $this->json('DELETE', "/api/posts/$post->id");

        $response->assertSee(null)
            ->assertStatus(204); // OK, sin contenido

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }
}
