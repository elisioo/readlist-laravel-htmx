<?php

namespace App\Http\Controllers;

use App\Models\Readlist;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReadlistController extends Controller
{
    public function index(): View
    {
        return view('readlist.index', [
            'readlists' => Readlist::where('user_id', auth()->id())->latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'required|string',
            'author' => 'required|string|max:100',
            'status' => 'required|in:To Read,Unread,Ongoing,Done'
        ]);

        $request->user()->readlists()->create($validate);

        return view('readlist.index', [
            'readlists' => Readlist::where('user_id', auth()->id())->latest()->get(),
        ])->with('success', 'Book added to your readlist!');
    }

    public function update(Request $request, Readlist $readlist)
    {
        if (auth()->id() !== $readlist->user_id) {
            return view('readlist.index', [
                'readlists' => Readlist::where('user_id', auth()->id())->latest()->get(),
            ])->with('error', 'Unauthorized action.');
        }

        $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'author' => 'required',
            'status' => 'required|in:To Read,Unread,Ongoing,Done',
        ]);

        $readlist->update($request->only(['title', 'description', 'author', 'status']));

        return view('readlist.index', [
            'readlists' => Readlist::where('user_id', auth()->id())->latest()->get(),
        ])->with('success', 'Book updated successfully!');
    }

    public function destroy(Readlist $readlist)
    {
        if (auth()->id() !== $readlist->user_id) {
            return view('readlist.index', [
                'readlists' => Readlist::where('user_id', auth()->id())->latest()->get(),
            ])->with('error', 'Unauthorized action.');
        }

        $readlist->delete();

        return view('readlist.index', [
            'readlists' => Readlist::where('user_id', auth()->id())->latest()->get(),
        ])->with('success', 'Book removed from your readlist.');
    }
}
