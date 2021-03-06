<?php

namespace App\Http\Controllers\Forum;

use App\Http\Controllers\Controller;
use App\Http\Requests\Forum\ForumUpdate;
use App\Models\Catagory;
use App\Models\Forum;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

use function PHPSTORM_META\type;

class ForumController extends Controller
{

    public function index()
    {
        $review = Review::factory()->reply()->make();
        return response($review, 200);
    }
    public function getForumDetails(Request $request, Forum $forum)
    {
        $user = $request->user();
        if (!$user?->canany(['access', 'update'], $forum)) {
            return abort(403, 'You are not authorized to access this forum');
        };

        //datas
        $forum->load('coverPhoto');
        $forum->students = $forum->students()
            ->with('profilePicture')
            ->paginate(4, ['*'], 'students');

        $forum->questions = $forum->questions()
            ->with([
                'ownerDetails',
                'voted'
            ])
            ->withCount([
                'votes as incrementVotes' => fn ($q) => $q->where('vote_type', 'increment'),
                'votes as decrementVotes' => fn ($q) => $q->where('vote_type', 'decrement'),
            ])
            ->withCount('answers')
            ->orderBy('created_at', 'desc')
            ->paginate(8, ['*'], 'questions');

        //permissions
        $forum->editable = $user?->id == $forum->owner;

        if ($request->header('accept') == 'application/json') {
            return response()->json($forum, 200);
        };

        return view('pages.forum.Show', ['forum' => $forum]);
    }
    public function updateForumDetails(ForumUpdate $request, Forum $forum)
    {
        $forum->name = $request->name ?? $forum->name;
        $forum->description = $request->description ?? $forum->description;
        $forum->save();
        
        
        if ($request->hasFile('cover')) {
            $name = uniqid() .  $request->cover->extension();
            $request->file('cover')?->storeAs('forum/cover', $name, 'public');
            $forum->coverPhoto()->create([
                'file_type' => 'cover',
                'file_name' => $name,
                'file_link' => asset("storage/forum/cover/$name"),
            ]);
        }
        $forum->load('coverPhoto');
        if ($request->header('accept') == 'application/json') {
            return response()->json($forum);
        }
        return redirect("/show/forum/$forum->id");
    }
}
