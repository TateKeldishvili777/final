<?php

namespace App\Http\Controllers;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Post;

/**
 * Created using artisan in the terminal: $ php artisan make:controller PostsController --resource
 */
class PostsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * 
     * blog/posts
     */
    public function index()
    {
        /** 
         * without eloquent:
         * $post = DB::select('SELECT * FROM posts');
         * 
         * with eloquent:
         * $posts = Post::all();
         * $posts = Post::orderBy('title','desc')->take(1)->get();
         * 
         */
        $posts = Post::orderBy('created_at','desc')->paginate(10);
        return view('posts.index')->with('posts', $posts);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     * blog\posts\create
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'body' => 'required',
            'cover_image' => 'image|nullable|max:19995'
        ]);
        // Handle file upload
        if($request->hasFile('cover_image')){
            // Get a filename with the extension 
            $fileNameWithExt = $request->file('cover_image')->getClientOriginalName();
            // Get just filename
            $fileName = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
            // Get just ext
            $extension = $request->file('cover_image')->getClientOriginalExtension();
            // Filename to store -unique- 
            $fileNameToStore = $fileName.'_'.time().'.'.$extension;
            // Upload image
            $path = $request->file('cover_image')->storeAs('public/cover_images', $fileNameToStore);
        } else {
            $fileNameToStore = 'noImage.png';
        }
        // Create post 
        $post = new Post;
        $post->title = $request->input('title');
        $post->body = $request->input('body');
        $post->user_id = auth()->user()->id;
        $post->cover_image = $fileNameToStore;
        $post->save();

        return redirect('/posts')->with('success', 'Post created');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * 
     * blog\posts\id
     */
    public function show($id)
    {
        try {
            $post = Post::findOrFail($id);
            if(!$post) { 
                return redirect('/posts')->with('error','Post Not Found'); 
            }
            return view('posts.show')->with('post', $post);
        } catch (ModelNotFoundException $exception){
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $post = Post::findOrFail($id);
            /** Check for correct user */
            if (auth()->user()->id != $post->user_id){
                return redirect('/posts')->with('error', 'Unauthorized page');
            }
            return view('posts.edit')->with('post', $post);
        } catch (ModelNotFoundException $exception){
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        try {
            $this->validate($request, [
                'title' => 'required',
                'body' => 'required'
            ]);

            // Handle file upload
            if($request->hasFile('cover_image')){
                // Get a filename with the extension 
                $fileNameWithExt = $request->file('cover_image')->getClientOriginalName();
                // Get just filename
                $fileName = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
                // Get just ext
                $extension = $request->file('cover_image')->getClientOriginalExtension();
                // Filename to store -unique- 
                $fileNameToStore = $fileName.'_'.time().'.'.$extension;
                // Upload image
                $path = $request->file('cover_image')->storeAs('public/cover_images', $fileNameToStore);
            }
            // Create Post 
            $post = Post::findOrFail($id);
            if (auth()->user()->id != $post->user_id){
                return redirect('/posts')->with('error', 'Unauthorized page');
            }
            $post->title = $request->input('title');
            $post->body = $request->input('body');

            if($request->hasFile('cover_image')){
                if ($post->cover_image != 'noImage.png') {
                    Storage::delete('public/cover_images/'.$post->cover_image);
                }
                $post->cover_image = $fileNameToStore;
            }
            $post->save();
            return redirect('/posts')->with('success', 'Post updated');
        } catch (ModelNotFoundException $exception){
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        try {
            $post = Post::findOrFail($id);
            /** Check for correct user */
            if (auth()->user()->id != $post->user_id /* || Auth::user()->level == 0 */ /* DO THIS IF YOU WANT "ADMIN LEVELS" */){
                return redirect('/posts')->with('error', 'Unauthorized page');
            }
            if ($post->cover_image != 'noImage.png'){
                // Delete the image
                Storage::delete('public/cover_images/'.$post->cover_image);
            }
            $post->delete();
            return redirect('/posts')->with('success', 'Post removed');
        } catch (ModelNotFoundException $exception){
            return back()->withError($exception->getMessage())->withInput();
        }
    }
}
