<?php

namespace App\Enums;

/**
 * Post Vote Type Enumeration
 * 
 * Represents the different types of votes that can be cast on posts.
 */
enum PostVoteType: string
{
    /**
     * Upvote type.
     * 
     * @var string
     */
    case UPVOTE = 'UPVOTE';

    /**
     * Downvote type.
     * 
     * @var string
     */
    case DOWNVOTE = 'DOWNVOTE';

    /**
     * Get list of all ticket types.
     *
     * @return array<PostVoteType>
     */
    public static function list(): array
    {
        return [
            self::UPVOTE,
            self::DOWNVOTE,
        ];
    }
}