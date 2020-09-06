<?php

namespace Tests\Feature\Http\Controllers\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Post;
use App\User;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;
    // Test para la función de store
    public function test_store()
    {
        // $this->withoutExceptionHandling();

        $user = factory(User::class)->create();

        $response = $this->actingAs($user, 'api')->json('POST', '/api/posts', [
            'title' => 'El post de prueba'
        ]);

        $response->assertJsonStructure(['id', 'title', 'created_at', 'updated_at'])
            ->assertJson(['title' => 'El post de prueba'])
            ->assertStatus(201); // Se ha completado correctamente y se ha creado

        $this->assertDatabaseHas('posts', ['title' => 'El post de prueba']);
    }

    // Test para la validación del título
    public function test_validate_title(){
        $user = factory(User::class)->create();

        $response = $this->actingAs($user, 'api')->json('POST', '/api/posts', [
            'title' => ''
        ]);

        // Estatus HTTP 422 significa que no se ha completado
        $response->assertStatus(422)
            ->assertJsonValidationErrors('title');
    }

    // Test para la función de show
    public function test_show(){
        $post = factory(Post::class)->create();

        $user = factory(User::class)->create();

        $response = $this->actingAs($user, 'api')->json('GET', "/api/posts/$post->id");

        $response->assertJsonStructure(['id', 'title', 'created_at', 'updated_at'])
            ->assertJson(['title' => $post->title])
            ->assertStatus(200); // Se ha completado correctamente y se ha creado
    }

    // Test para un error 404 del post
    public function test_404_show(){
        $user = factory(User::class)->create();

        $response = $this->actingAs($user, 'api')->json('GET', '/api/posts/1000');

        $response->assertStatus(404); // Se ha completado correctamente y se ha creado
    }

    // Test para actualizar
    public function test_update(){
        $post = factory(Post::class)->create();

        $user = factory(User::class)->create();

        $response = $this->actingAs($user, 'api')->json('PUT', "/api/posts/$post->id", [
            'title' => 'Nuevo'
        ]);

        $response->assertJsonStructure(['id', 'title', 'created_at', 'updated_at'])
            ->assertJson(['title' => 'Nuevo'])
            ->assertStatus(200); // Se ha completado correctamente

        $this->assertDatabaseHas('posts', ['title' => 'Nuevo']);
    }

    // Test para borrar
    public function test_delete(){
        $post = factory(Post::class)->create();

        $user = factory(User::class)->create();

        $response = $this->actingAs($user, 'api')->json('DELETE', "/api/posts/$post->id", [
            'title' => 'Nuevo'
        ]);

        $response->assertSee(null)
            ->assertStatus(204); // Sin contenido

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    // Test del Index
    public function test_index(){
        factory(Post::class, 5)->create();

        $user = factory(User::class)->create();

        $response = $this->actingAs($user, 'api')->json('GET', '/api/posts');

        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'title', 'created_at', 'updated_at']
            ]
        ])->assertStatus(200); // OK
    }

    public function test_guest(){
        $this->json('GET',      '/api/posts')->assertStatus(401);
        $this->json('POST',     '/api/posts')->assertStatus(401);
        $this->json('GET',      '/api/posts/100')->assertStatus(401);
        $this->json('PUT',      '/api/posts/100')->assertStatus(401);
        $this->json('DELETE',   '/api/posts/100')->assertStatus(401);
    }
}
