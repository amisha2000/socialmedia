<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Share;
use App\Models\Post;
use App\Jobs\ShareSendMail;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Response;



class ShareController extends Controller
{
     protected $user;

    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function sharePost(Request $request){
        try {
            $post = Post::find($request->post_id);
            if (!$post) {
                return response()->json(['success' => false, 'error' => 'Post does not exist.'], 400);
            }
            $share = Share::create([
                "user_id" => $request->user_id,
                "post_id" => $request->post_id,
                "shared_to" => $request->shared_to,
                "platform" => $request->platform
            ]);
            $emailJobs = new ShareSendMail();
            $this->dispatch($emailJobs);

            if($share){
                $count = Post::where('id',$request->post_id)->withCount('shares')->first();
                return response()->json([
                    'success' => true,
                    'data' => $share,
                    'shares_count'=>$count
                    ],Response::HTTP_OK);
            } else {
                return response()->json(['success' => false, 'error' => 'Create share post failed.'], 400);
            }

        } catch (\Exception $error) {
            return response()->json(['success' => false, 'error' => 'Looks like error'], 500);
        }
    }
    public function destroy(Share $delete)
    {
        $delete->delete();

        return response()->json([
            'success' => true,
            'message' => 'Share deleted successfully'
        ], Response::HTTP_OK);
    }
}
