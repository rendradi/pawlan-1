<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Http\Resources\BookResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        // get all books
        $books = Book::latest()->paginate(5);

        // return collection of books as a resource
        return new BookResource(true, 'List Data Books', $books);
    }

    /**
     * store
     *
     * @param  mixed $request
     * @return void
     */
    public function store(Request $request)
    {
        // define validation rules
        $validator = Validator::make($request->all(), [
            'title'     => 'required',
            'author'    => 'required',
            'publisher' => 'required',
            'year'      => 'required|integer|min:1000|max:9999',
        ]);

        // check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // create book
        $book = Book::create([
            'title'     => $request->title,
            'author'    => $request->author,
            'publisher' => $request->publisher,
            'year'      => $request->year,
        ]);

        // return response
        return new BookResource(true, 'Data Book Berhasil Ditambahkan!', $book);
    }

    /**
     * show
     *
     * @param  mixed $id
     * @return void
     */
    public function show($id)
    {
        // find book by ID
        $book = Book::find($id);

        // check if book not found
        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Data Book Tidak Ditemukan!',
            ], 404);
        }

        // return single book as a resource
        return new BookResource(true, 'Detail Data Book!', $book);
    }

    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return void
     */
    public function update(Request $request, $id)
    {
        // define validation rules
        $validator = Validator::make($request->all(), [
            'title'     => 'required',
            'author'    => 'required',
            'publisher' => 'required',
            'year'      => 'required|integer|min:1000|max:9999',
        ]);

        // check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // find book by ID
        $book = Book::find($id);

        // check if book not found
        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Data Book Tidak Ditemukan!',
            ], 404);
        }

        // update book
        $book->update([
            'title'     => $request->title,
            'author'    => $request->author,
            'publisher' => $request->publisher,
            'year'      => $request->year,
        ]);

        // return response
        return new BookResource(true, 'Data Book Berhasil Diubah!', $book);
    }

    /**
     * destroy
     *
     * @param  mixed $id
     * @return void
     */
    public function destroy($id)
    {
        // find book by ID
        $book = Book::find($id);

        // check if book not found
        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Data Book Tidak Ditemukan!',
            ], 404);
        }

        // delete book
        $book->delete();

        // return response
        return new BookResource(true, 'Data Book Berhasil Dihapus!', null);
    }
}
