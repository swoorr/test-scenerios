<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PostController extends Controller
{
    //
    public function index(): \Illuminate\Http\JsonResponse
    {
        return response()->json(['status' => true, 'message' => 'Authenticated']);
    }
}
