<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PagesController extends Controller
{
    public function index(){
        $title = 'Simply join us or register';
        return view('pages.index')->with('title', $title);
    }
    public function about(){
        $title = 'About us: This is a little project for laravel course final exam by tate keldishvili.
        Here you can log into our service and view, add , remove or edit posts';
        return view('pages.about')->with('title', $title);
    }
    public function services(){
        $data = array(
        'title' => 'Services:',
        'services' => ['Log in', 'Create blog', 'Delete Blog', 'Edit blog', 'View Blog']
     );
        return view('pages.services')->with($data);
    }
}
