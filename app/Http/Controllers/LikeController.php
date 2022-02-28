<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;
use App\Models\Like;
use Illuminate\Http\Response;
use App\Jobs\SendLike;



class LikeController extends Controller
{
     protected $user;

    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }


     public function like(Request $request)
    {
    	//Validate data
        $logged_user=$request->user_id;
        $data = $request->only('user_id', 'post_id');
        $validator = Validator::make($data, [
            'user_id' => 'required',
            'post_id' => 'required',
        ]);
        if (!$data) {
            return response()->json(['success' => false, 'error' => 'Post does not exist.'], 400);
        }

        $check = Like::where('post_id', $request->post_id)->where('user_id', $logged_user)->first();
        if ($check) {
            return response()->json('Post already liked!!');
        }

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        //Request is valid, create new user
        $user = Like::create([
        	'user_id' => $request->user_id,
        	'post_id' => $request->post_id,
        ]);

        $emailJobs = new SendLike();
        $this->dispatch($emailJobs);

        //$like_data=Like::where('post_id', $request->post_id)->where('user_id', $request->user_id)->first();


        // $likelist = likes::where('id')->get();
        // $likecount = $likelist->count();
        //$countid=$like_data->id;


        //User created, return success response
        return response()->json([
            'success' => true,
            'message' => 'Like Added Successfuly',
            'data' => $user,
            //'likecount' => $countid
        ], Response::HTTP_OK);

         $like_data=Like::where('post_id', $request->post_id)->where('user_id', $request->user_id)->first();


        // $likelist = likes::where('id')->get();
        // $likecount = $likelist->count();
        $countid=$like_data->id;
        return "likecount :".$countid;
        return response()->json([$like_data]);
    }
    public function getLikes( $req)
    {

        $data = Like::join('users','users.id','=','likes.user_id')->get(['likes.*','users.name']);
        $count = Like::where('post_id','=',$req)->count();
        return "Likecount :" .$count."</br>".$data;

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, post not found.'
            ], 400);
        }
    }
}
