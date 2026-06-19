<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TodoController extends Controller
{
    public function index()
    {
        $todos = session('todos', []);

        return view('todos', compact('todos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $todos = session('todos', []);

        $todos[] = [
            'id' => Str::uuid()->toString(),
            'title' => $request->input('title'),
            'completed' => false,
            'created_at' => now()->toDateTimeString(),
        ];

        session(['todos' => $todos]);

        return back();
    }

    public function toggle(string $id)
    {
        $todos = session('todos', []);

        foreach ($todos as &$todo) {
            if ($todo['id'] === $id) {
                $todo['completed'] = ! $todo['completed'];
                break;
            }
        }

        session(['todos' => $todos]);

        return back();
    }

    public function destroy(string $id)
    {
        $todos = session('todos', []);
        $todos = array_values(array_filter($todos, fn ($todo) => $todo['id'] !== $id));

        session(['todos' => $todos]);

        return back();
    }
}
