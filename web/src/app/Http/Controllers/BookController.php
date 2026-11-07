<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index()
    {
        return view('books.index', [
            'books' => Book::orderByDesc('id')->get(),
        ]);
    }

    public function create()
    {
        return view('books.form', ['book' => new Book()]);
    }

    public function store(Request $request)
    {
        Book::create($this->validated($request));

        return redirect()->route('books.index')
            ->with('ok', 'Đã thêm sách.');
    }

    public function show(Book $book)
    {
        return view('books.show', compact('book'));
    }

    public function edit(Book $book)
    {
        return view('books.form', compact('book'));
    }

    public function update(Request $request, Book $book)
    {
        $book->update($this->validated($request));

        return redirect()->route('books.index')
            ->with('ok', 'Đã sửa sách.');
    }

    public function destroy(Book $book)
    {
        $book->delete();

        return redirect()->route('books.index')
            ->with('ok', 'Đã xóa sách.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);
    }
}
