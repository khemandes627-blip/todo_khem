<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Todo List</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
                :root {
                    color-scheme: light;
                    font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
                }

                body {
                    margin: 0;
                    min-height: 100vh;
                    background: linear-gradient(180deg, #F7F2FF 0%, #EFECFF 100%);
                    color: #2C1543;
                }

                .page-shell {
                    width: 100%;
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    padding: 2rem 1rem;
                }

                .panel {
                    width: min(100%, 680px);
                    background: rgba(255, 255, 255, 0.9);
                    border: 1px solid rgba(105, 78, 182, 0.15);
                    box-shadow: 0 20px 80px rgba(99, 61, 174, 0.12);
                    border-radius: 28px;
                    overflow: hidden;
                }

                .hero {
                    padding: 2rem;
                    background: linear-gradient(135deg, #6C63FF 0%, #8C75FF 100%);
                    color: white;
                }

                .hero h1 {
                    margin: 0 0 0.75rem;
                    font-size: clamp(2rem, 4vw, 2.8rem);
                    letter-spacing: -0.04em;
                }

                .hero p {
                    margin: 0;
                    line-height: 1.6;
                    color: rgba(255,255,255,0.9);
                }

                .content {
                    padding: 2rem;
                    display: grid;
                    gap: 1.25rem;
                }

                .todo-form {
                    display: grid;
                    gap: 0.75rem;
                }

                .summary-band {
                    display: grid;
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                    gap: 0.85rem;
                }

                .summary-card {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 1rem 1.1rem;
                    border-radius: 16px;
                    background: #F3EEFF;
                    border: 1px solid rgba(105, 78, 182, 0.16);
                    color: #3D2267;
                }

                .summary-card strong {
                    font-size: 1.35rem;
                }

                .todo-form input[type='text'] {
                    width: 100%;
                    padding: 1rem 1.1rem;
                    border: 1px solid rgba(105, 78, 182, 0.28);
                    border-radius: 14px;
                    background: #FAF5FF;
                    color: #2C1543;
                    font-size: 1rem;
                }

                .todo-form button {
                    width: 100%;
                    padding: 1rem 1.2rem;
                    border: none;
                    border-radius: 14px;
                    background: #6F5CFF;
                    color: white;
                    font-weight: 600;
                    cursor: pointer;
                }

                .todos {
                    display: grid;
                    gap: 0.85rem;
                }

                .todo-item {
                    display: grid;
                    grid-template-columns: 1fr auto;
                    gap: 1rem;
                    align-items: center;
                    padding: 1rem 1rem;
                    border-radius: 18px;
                    background: #F7F2FF;
                    border: 1px solid rgba(105, 78, 182, 0.14);
                }

                .todo-item.completed {
                    background: #F2EEFF;
                    color: #6C5487;
                }

                .todo-item label {
                    display: flex;
                    align-items: center;
                    gap: 0.9rem;
                    cursor: pointer;
                    user-select: none;
                }

                .todo-item input[type='checkbox'] {
                    width: 1.15rem;
                    height: 1.15rem;
                    accent-color: #8B74FF;
                }

                .todo-item .title {
                    font-size: 1rem;
                    line-height: 1.5;
                    margin: 0;
                }

                .todo-item.completed .title {
                    text-decoration: line-through;
                    opacity: 0.7;
                }

                .todo-actions {
                    display: flex;
                    align-items: center;
                    gap: 0.5rem;
                }

                .todo-actions form {
                    margin: 0;
                }

                .todo-actions button {
                    border: 1px solid rgba(105, 78, 182, 0.24);
                    background: transparent;
                    color: #5A3F9D;
                    border-radius: 12px;
                    padding: 0.7rem 0.9rem;
                    cursor: pointer;
                    font-weight: 600;
                }

                .empty-state {
                    padding: 1.75rem;
                    border-radius: 18px;
                    background: rgba(111, 92, 255, 0.09);
                    color: #4B2F7C;
                    border: 1px dashed rgba(111, 92, 255, 0.35);
                }

                @media (min-width: 768px) {
                    .content {
                        gap: 1.5rem;
                    }
                }
            </style>
        @endif
    </head>
    <body>
        <div class="page-shell">
            <div class="panel">
                <section class="hero">
                    <h1>Todo List</h1>
                    <p>Track your tasks, mark them done, or remove them instantly.</p>
                </section>

                <section class="content">
                    <form class="todo-form" action="{{ route('todos.store') }}" method="POST">
                        @csrf
                        <label for="title" class="sr-only">New task</label>
                        <input id="title" name="title" type="text" placeholder="Add a new todo..." required autocomplete="off" />
                        <button type="submit">Add task</button>
                    </form>

                        @if ($errors->any())
                        <div class="empty-state" style="border-style: solid;">
                            {{ $errors->first('title') }}
                        </div>
                    @endif

                    @php
                        $openCount = count(array_filter($todos, fn($todo) => ! $todo['completed']));
                        $doneCount = count(array_filter($todos, fn($todo) => $todo['completed']));
                    @endphp

                    <div class="summary-band">
                        <div class="summary-card">
                            <span>Open</span>
                            <strong>{{ $openCount }}</strong>
                        </div>
                        <div class="summary-card">
                            <span>Done</span>
                            <strong>{{ $doneCount }}</strong>
                        </div>
                    </div>

                    @if (count($todos) === 0)
                        <div class="empty-state">
                            Your todo list is empty. Add a task to get started.
                        </div>
                    @else
                        <div class="todos">
                                @foreach ($todos as $todo)
                                    <div class="todo-item {{ $todo['completed'] ? 'completed' : '' }}">
                                        <label>
                                            <input type="checkbox" onchange="document.getElementById('toggle-{{ $todo['id'] }}').submit()" {{ $todo['completed'] ? 'checked' : '' }} />
                                            <p class="title">{{ $todo['title'] }}</p>
                                        </label>
                                        <div class="todo-actions">
                                            <form id="toggle-{{ $todo['id'] }}" action="{{ route('todos.toggle', ['id' => $todo['id']]) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                            </form>
                                            <form action="{{ route('todos.destroy', ['id' => $todo['id']]) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit">Remove</button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </section>
            </div>
        </div>
    </body>
</html>
