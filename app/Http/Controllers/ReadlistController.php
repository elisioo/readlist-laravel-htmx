<?php

namespace App\Http\Controllers;

use App\Models\Readlist;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReadlistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('readlist.index', [
            'readlists' => Readlist::where('user_id', auth()->id())->latest()->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'required|string',
            'author' => 'required|string|max:100',
            'status' => 'required|in:To Read,Unread,Ongoing,Done'
        ]);

        $request->user()->readlists()->create($validate);

        return redirect()->route('readlist.index')->with('success', 'Book added to your readlist!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Readlist $readlist)
    {
        if (auth()->id() !== $readlist->user_id) {
            return redirect()->route('readlist.index')->with('error', 'Unauthorized action.');
        }

        $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'author' => 'required',
            'status' => 'required|in:To Read,Unread,Ongoing,Done',
        ]);

        $readlist->update($request->only(['title', 'description', 'author', 'status']));

        return redirect()->route('readlist.index')->with('success', 'Book updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Readlist $readlist)
    {
        if (auth()->id() !== $readlist->user_id) {
            return redirect()->route('readlist.index')->with('error', 'Unauthorized action.');
        }

        $readlist->delete();

        return redirect()->route('readlist.index')->with('success', 'Book removed from your readlist.');
    }
}
