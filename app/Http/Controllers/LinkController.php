<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Link;

class LinkController extends Controller
{
    public function index()
    {
        return view('links.index');
    }

    public function create()
    {
        return view('links.create');
    }

    public function edit(Link $link)
    {
        return view('links.edit')->with('link', $link);
    }
}
