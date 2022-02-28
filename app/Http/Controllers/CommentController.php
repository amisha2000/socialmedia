<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Jobs\CommentSendMail;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Post;

class CommentController extends Controller
{
     protected $user;

    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }


    public function createcomment(Request $request)
    {
        //Validate data
        $data = $request->only('name', 'comment','post_id');
        $validator = Validator::make($data, [
            'name' => 'required',
            'comment' => 'required',
            'post_id' => 'required'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        //Request is valid, create new product
        $comment = Comment::create([
            'name' => $request->name,
            'comment' => $request->comment,
            'post_id' => $request->post_id
        ]);
        $emailJobs = new CommentSendMail();
        $this->dispatch($emailJobs);


        //Product created, return success response
        return response()->json([
            'success' => true,
            'message' => 'comment created successfully',
            'data' => $comment
        ], Response::HTTP_OK);
    }

    public function showcomment()
    {
        $data = Post::all();
        foreach ($data as $post) {
            $comment = Comment::where('post_id', $post->id)->get();
            echo"post id: $post->id</br>";
            echo"post title: $post->title</br>";
            echo"post desc: $post->description</br>";

            foreach($comment as $cmt){
                echo"<p style='padding-left:20px'>comment Name:$cmt->name</p><br/>";
                echo"<p style='padding-left:20px'>comment:$cmt->comment</p><br/>";
            }
        }

       if(!$data){
            return response()->json([
                'success' => false,
                'message' => 'Sorry, comment not found.'
            ], 400);
        }
    }
}
