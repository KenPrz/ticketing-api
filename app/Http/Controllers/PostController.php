<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Http\Resources\TicketResource;
use App\Models\Event;
use App\Models\Ticket;
use App\Services\PostService;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    /**
     * The post service instance.
     *
     * @var \App\Services\PostService
     */
    protected $postService;

    /**
     * Construct the post controller instance.
     *
     * @param \App\Services\PostService $postService The post service instance
     */
    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get the authenticated user
        $user = $request->user();

        // Get relevant posts for the authenticated user
        $posts = $this->postService->getRelevantPosts($user);
        $data = PostResource::collection($posts)
            ->response()
            ->getData(true);

        // Return the posts as a JSON response
        return response()->json(
            [
                $data,
            ],
            200,
        );
    }

    /**
     * Get the posts of a specific user.
     * 
     * @param \Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserPosts(Request $request)
    {
        // Get the authenticated user
        $user = $request->user();

        // Get the posts of the authenticated user
        $posts = $this->postService->getUserPosts($user);

        // Return the posts as a JSON response
        return response()->json(
            [
                'posts' => PostResource::collection($posts)
                    ->response()
                    ->getData(true),
            ],
            200,
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        try {
            // Since the request is already validated, create the post
            $post = $this->postService->createPost(
                $request->user(), 
                $request->validated()
            );

            // Return the post as a JSON response
            return response()->json([
                'message' => 'Post created successfully',
                'post' => new PostResource($post),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create post',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, string $id)
    {
        try {
            // Since the request is already validated, update the post
            $post = $this->postService->updatePost(
                $request->user(), 
                $id, 
                $request->validated()
            );
            
            // Return the post as a JSON response
            return response()->json([
                'message' => 'Post updated successfully',
                'post' => new PostResource($post),
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Post not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update post',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        try {
            // Ensure user can only delete their own posts
            $user = $request->user();
            
            // Delete the post
            $this->postService->deletePost($user, $id);
            
            // Return a success response
            return response()->json([
                'message' => 'Post deleted successfully',
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Post not found or you do not have permission to delete it',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete post',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Upvote a post.
     */
    public function upvote(Request $request, string $id)
    {
        try {

            // Upvote the post
            $post = $this->postService->upvotePost($request->user(), $id);

            // Return the post as a JSON response
            return response()->json([
                'message' => 'Post upvoted successfully',
                'post' => new PostResource($post),
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Post not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to upvote post',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Downvote a post.
     */
    public function downvote(Request $request, string $id)
    {
        try {
            // Downvote the post
            $post = $this->postService->downvotePost($request->user(), $id);
            
            // Return the post as a JSON response
            return response()->json([
                'message' => 'Post downvoted successfully',
                'post' => new PostResource($post),
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Post not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to downvote post',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove a vote from a post.
     */
    public function unvote(Request $request, string $id)
    {
        try {
            // Remove vote from the post
            $post = $this->postService->unvotePost($request->user(), $id);
            
            // Return the post as a JSON response
            return response()->json([
                'message' => 'Vote removed successfully',
                'post' => new PostResource($post),
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Post not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to remove vote',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get the tickets of a specific user.
     * 
     * @param \Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserTickets(Request $request)
    {
        // Get the authenticated user
        $user = $request->user();
        $query = $request->input('query');

        // Get the tickets of the authenticated user
        $tickets = Ticket::where('owner_id', $user->id)
            ->with('event')
            ->where(function ($queryBuilder) use ($query) {
                if (!empty($query)) {
                    $queryBuilder->where('ticket_name', 'LIKE', '%' . $query . '%')
                        ->orWhere('ticket_type', 'LIKE', '%' . $query . '%')
                        ->orWhere('ticket_desc', 'LIKE', '%' . $query . '%');
                }
            })
            ->where('is_used', false)
            ->get();
        $data = TicketResource::collection($tickets)
            ->response()
            ->getData(true);

        // Return the tickets as a JSON response
        return response()->json(
            [
                $data,
            ],
            200,
        );
    }

    /**
     * Get the events of a specific user.
     * 
     * @param \Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchEvents(Request $request)
    {
        $query = $request->input('query');

        // Validate the query
        if (empty($query)) {
            return response()->json([
                'message' => 'Query parameter is required',
            ], 400);
        }

        // Search for events based on the query
        $events = Event::where('name', 'LIKE', '%' . $query . '%')
            ->orWhere('description', 'LIKE', '%' . $query . '%')
            ->get();

        // Return the events as a JSON response
        return response()->json([
            $events,
        ], 200);
    }
}