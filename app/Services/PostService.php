<?php

namespace App\Services;
use App\Models\{
    Post,
    User,
    Event,
};
use App\Enums\PostVoteType;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PostService
{
    /**
     * The post model instance.
     *
     * @var Post
     */
    protected $post;

    /**
     * Construct the post service instance.
     *
     * @param Post $post The post model instance
     */
    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    /**
     * Get relevant posts for the authenticated user ordered by upvotes and creation date.
     * 
     * @param \App\Models\User $user
     * @param string $direction
     * 
     * @return \Illuminate\Database\Eloquent\Collection<int, Post>
     */
    public function getRelevantPosts(
        User $user,
        $direction = 'desc',
    ) {
        // Get posts from other users
        return $this->post
            ->with(['user', 'event', 'ticket'])
            ->where('user_id', '!=', $user->id)
            ->withCount('upvotes')
            ->orderBy('upvotes_count', $direction)
            ->orderBy('created_at', $direction)
            ->limit(50)
            ->get();
    }

    /**
     * Get the posts of the authenticated user.
     *
     * @param \App\Models\User $user
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, Post>
     */
    public function getMyPosts(User $user)
    {
        return $this->post->where('user_id', $user->id)
            ->get();
    }

    /**
     * Create a new post.
     * 
     * @param \App\Models\User $user
     * @param array $data
     * 
     * @return \App\Models\Post
     */
    public function createPost(User $user, array $data)
    {
        return $user->posts()->create([
            'user_id' => $user->id,
            'content' => $data['content'],
            'post_context' => $data['post_context'],
            'event_id' => $data['event_id'] ?? null,
            'ticket_id' => $data['ticket_id'] ?? null,
            'price' => $data['price'] ?? null,
        ]);
    }

    /**
     * Update an existing post.
     * 
     * @param \App\Models\User $user
     * @param string $id
     * @param array $data
     * 
     * @return \App\Models\Post|null
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function updatePost(User $user, string $id, array $data)
    {
        $post = $this->post->where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $post->update([
            'content' => $data['content'] ?? $post->content,
            'post_context' => $data['post_context'] ?? $post->post_context,
            'event_id' => $data['event_id'] ?? $post->event_id,
            'ticket_id' => $data['ticket_id'] ?? $post->ticket_id,
            'price' => $data['price'] ?? $post->price,
        ]);

        return $post->fresh();
    }

    /**
     * Delete a post.
     * 
     * @param \App\Models\User $user
     * @param string $id
     * 
     * @return bool
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function deletePost(User $user, string $id)
    {
        $post = $this->post->where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        return $post->delete();
    }

    /**
     * Upvote a post.
     * 
     * @param \App\Models\User $user
     * @param string $id
     * 
     * @return \App\Models\Post
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function upvotePost(User $user, string $id)
    {
        $post = $this->post->findOrFail($id);

        // Remove any existing votes first
        $post->votes()->where('user_id', $user->id)->delete();

        // Add upvote
        $post->votes()->create([
            'user_id' => $user->id,
            'vote_type' => PostVoteType::UPVOTE,
        ]);

        return $post->fresh();
    }

    /**
     * Downvote a post.
     * 
     * @param \App\Models\User $user
     * @param string $id
     * 
     * @return \App\Models\Post
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function downvotePost(User $user, string $id)
    {
        $post = $this->post->findOrFail($id);

        // Remove any existing votes first
        $post->votes()->where('user_id', $user->id)->delete();

        // Add downvote
        $post->votes()->create([
            'user_id' => $user->id,
            'vote_type' => PostVoteType::DOWNVOTE,
        ]);

        return $post->fresh();
    }

    /**
     * Remove a user's vote from a post.
     * 
     * @param \App\Models\User $user
     * @param string $id
     * 
     * @return \App\Models\Post
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function unvotePost(User $user, string $id)
    {
        $post = $this->post->findOrFail($id);

        // Remove any existing votes
        $post->votes()->where('user_id', $user->id)->delete();

        return $post->fresh();
    }
}