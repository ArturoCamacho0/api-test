<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Post;
use App\Http\Requests\Post as PostRequests;

class PostController extends Controller
{

    protected $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    // Mostrar todo
    public function index()
    {
        return response()->json($this->post->paginate());
    }

    // Crear
    public function store(PostRequests $request)
    {
        $post = $this->post->create($request->all());

        return response()->json($post, 201);
    }

    // Mostrar un elemento
    public function show(Post $post)
    {
        return response()->json($post);
    }

    // Actualizar
    public function update(PostRequests $request, Post $post)
    {
        $post->update($request->all());

        return response()->json($post);
    }

    // Borrar
    public function destroy(Post $post)
    {
        $post->delete();

        return response()->json(null, 204);
    }
}
