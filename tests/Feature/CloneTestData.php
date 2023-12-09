<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Comment;
use App\Models\Image;
use App\Models\Post;
use App\Models\Review;
use App\Models\Tag;
use App\Models\TagType;

trait CloneTestData
{
    public function getCloneAuthorAndRelatedModelsRequestData(): array
    {
        $author = Author::factory()
            ->has(
                Post::factory()
                    ->sequence([
                        'title' => 'First post',
                        'body' => 'First post body'
                    ],[
                        'title' => 'Second post',
                        'body' => 'Second post body'
                    ])
                    ->for(Image::factory([
                            'file_name' => 'https://picsum.photos/200/300',
                            'file_path' => 'https://picsum.photos/200/300',
                        ]))
                    ->count(2)
                    ->has(
                        Comment::factory()
                            ->sequence([
                                'body' => 'First comment'
                            ],[
                                'body' => 'Second comment'
                            ])
                            ->count(2)
                            ->has(
                                Tag::factory()
                                    ->sequence(
                                        [
                                            'type' => 'First tag',
                                            'name' => 'First tag',
                                            'slug' => 'first-tag'
                                        ],[
                                            'type' => 'Second tag',
                                            'name' => 'Second tag',
                                            'slug' => 'second-tag'
                                        ]
                                    )
                                    ->count(2)
                                    ->has(
                                        TagType::factory()
                                            ->sequence([
                                                'type_name' => 'Tag type',
                                                'slug' => 'first-tag-type'
                                            ])
                                            ->count(1)
                                    )
                            )
                    )
                    ->has(Review::factory()
                        ->sequence(
                            [
                                'author_id' => 1,
                                'body' => 'First review'
                            ],[
                                'author_id' => 1,
                                'body' => 'Second review'
                            ]
                        ))
            )
            ->create([
                'name' => 'John Doe'
            ]);

        $expectedResponse = [
                'data' => [
                'name' => 'John Doe Cloned',
                'id' => 2,
                'posts' => [
                    [
                        'id' => 3,
                        'title' => 'First post',
                        'body' => 'First post body',
                        'image_id' => 2,
                        'image_type' => 'App\\Models\\Image',
                        'pivot' => [
                            'author_id' => 2,
                            'post_id' => 3,
                        ],
                        'image' => [
                            "id" => 2,
                            "file_name" => "https://picsum.photos/200/300",
                            "file_path" => "https://picsum.photos/200/300"
                        ],
                        'comments' => [
                            [
                                'id' => 5,
                                'post_id' => 3,
                                'body' => 'First comment',
                                'tags' => [
                                    [
                                        'id' => 9,
                                        'comment_id' => 5,
                                        'type' => 'First tag',
                                        'name' => 'First tag',
                                        'slug' => 'first-tag',
                                        'tag_type' => [
                                            'id' => 9,
                                            'tag_id' => 9,
                                            'type_name' => 'Tag type',
                                            'slug' => 'first-tag-type',
                                        ],
                                    ],
                                    [
                                        'id' => 10,
                                        'comment_id' => 5,
                                        'type' => 'Second tag',
                                        'name' => 'Second tag',
                                        'slug' => 'second-tag',
                                        'tag_type' => [
                                            'id' => 10,
                                            'tag_id' => 10,
                                            'type_name' => 'Tag type',
                                            'slug' => 'first-tag-type',
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'id' => 6,
                                'post_id' => 3,
                                'body' => 'Second comment',
                                'tags' => [
                                    [
                                        'id' => 11,
                                        'comment_id' => 6,
                                        'type' => 'First tag',
                                        'name' => 'First tag',
                                        'slug' => 'first-tag',
                                        'tag_type' => [
                                            'id' => 11,
                                            'tag_id' => 11,
                                            'type_name' => 'Tag type',
                                            'slug' => 'first-tag-type',
                                        ],
                                    ],
                                    [
                                        'id' => 12,
                                        'comment_id' => 6,
                                        'type' => 'Second tag',
                                        'name' => 'Second tag',
                                        'slug' => 'second-tag',
                                        'tag_type' => [
                                            'id' => 12,
                                            'tag_id' => 12,
                                            'type_name' => 'Tag type',
                                            'slug' => 'first-tag-type',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    [
                        'id' => 4,
                        'title' => 'Second post',
                        'body' => 'Second post body',
                        'image_id' => 3,
                        'image_type' => 'App\\Models\\Image',
                        'pivot' => [
                            'author_id' => 2,
                            'post_id' => 4,
                        ],
                        'image' => [
                            "id" => 3,
                            "file_name" => "https://picsum.photos/200/300",
                            "file_path" => "https://picsum.photos/200/300"
                        ],
                        'comments' => [
                            [
                                'id' => 7,
                                'post_id' => 4,
                                'body' => 'First comment',
                                'tags' => [
                                    [
                                        'id' => 13,
                                        'comment_id' => 7,
                                        'type' => 'First tag',
                                        'name' => 'First tag',
                                        'slug' => 'first-tag',
                                        'tag_type' => [
                                            'id' => 13,
                                            'tag_id' => 13,
                                            'type_name' => 'Tag type',
                                            'slug' => 'first-tag-type',
                                        ],
                                    ],
                                    [
                                        'id' => 14,
                                        'comment_id' => 7,
                                        'type' => 'Second tag',
                                        'name' => 'Second tag',
                                        'slug' => 'second-tag',
                                        'tag_type' => [
                                            'id' => 14,
                                            'tag_id' => 14,
                                            'type_name' => 'Tag type',
                                            'slug' => 'first-tag-type',
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'id' => 8,
                                'post_id' => 4,
                                'body' => 'Second comment',
                                'tags' => [
                                    [
                                        'id' => 15,
                                        'comment_id' => 8,
                                        'type' => 'First tag',
                                        'name' => 'First tag',
                                        'slug' => 'first-tag',
                                        'tag_type' => [
                                            'id' => 15,
                                            'tag_id' => 15,
                                            'type_name' => 'Tag type',
                                            'slug' => 'first-tag-type',
                                        ],
                                    ],
                                    [
                                        'id' => 16,
                                        'comment_id' => 8,
                                        'type' => 'Second tag',
                                        'name' => 'Second tag',
                                        'slug' => 'second-tag',
                                        'tag_type' => [
                                            'id' => 16,
                                            'tag_id' => 16,
                                            'type_name' => 'Tag type',
                                            'slug' => 'first-tag-type',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'reviews' => [
                    [
                        'id' => 3,
                        'author_id' => 2,
                        'post_id' => 3,
                        'body' => 'First review',
                    ],
                    [
                        'id' => 4,
                        'author_id' => 2,
                        'post_id' => 4,
                        'body' => 'Second review',
                    ],
                ],
            ]
        ];

        return [$author->id, $expectedResponse];
    }

    public function getClonePostAndRelatedModelsRequestData(): array
    {
        $author = Author::factory()
            ->has(
                Post::factory()
                    ->sequence([
                        'title' => 'First post',
                        'body' => 'First post body'
                    ],[
                        'title' => 'Second post',
                        'body' => 'Second post body'
                    ])
                    ->for(Image::factory([
                        'file_name' => 'https://picsum.photos/200/300',
                        'file_path' => 'https://picsum.photos/200/300',
                    ]))
                    ->count(2)
                    ->has(
                        Comment::factory()
                            ->sequence([
                                'body' => 'First comment'
                            ],[
                                'body' => 'Second comment'
                            ])
                            ->count(2)
                            ->has(
                                Tag::factory()
                                    ->sequence(
                                        [
                                            'type' => 'First tag',
                                            'name' => 'First tag',
                                            'slug' => 'first-tag'
                                        ],[
                                            'type' => 'Second tag',
                                            'name' => 'Second tag',
                                            'slug' => 'second-tag'
                                        ]
                                    )
                                    ->count(2)
                                    ->has(
                                        TagType::factory()
                                            ->sequence([
                                                'type_name' => 'Tag type',
                                                'slug' => 'first-tag-type'
                                            ])
                                            ->count(1)
                                    )
                            )
                    )
                    ->has(Review::factory()
                        ->sequence(
                            [
                                'author_id' => 1,
                                'body' => 'First review'
                            ],[
                                'author_id' => 1,
                                'body' => 'Second review'
                            ]
                        ))
            )
            ->create([
                'name' => 'John Doe'
            ]);

        $expectedResponse = [
            "data" => [
                "title" => "First post",
                "body" => "First post body",
                "image_id" => 2,
                "image_type" => "App\\Models\\Image",
                "id" => 3,
                "image" => [
                    "id" => 2,
                    "file_name" => "https://picsum.photos/200/300",
                    "file_path" => "https://picsum.photos/200/300"
                ],
                "comments" => [
                    [
                        "id" => 5,
                        "post_id" => 3,
                        "body" => "First comment",
                        "tags" => [
                            [
                                "id" => 9,
                                "comment_id" => 5,
                                "type" => "First tag",
                                "name" => "First tag",
                                "slug" => "first-tag",
                                "tag_type" => [
                                    "id" => 9,
                                    "tag_id" => 9,
                                    "type_name" => "Tag type",
                                    "slug" => "first-tag-type"
                                ]
                            ],
                            [
                                "id" => 10,
                                "comment_id" => 5,
                                "type" => "Second tag",
                                "name" => "Second tag",
                                "slug" => "second-tag",
                                "tag_type" => [
                                    "id" => 10,
                                    "tag_id" => 10,
                                    "type_name" => "Tag type",
                                    "slug" => "first-tag-type"
                                ]
                            ]
                        ]
                    ],
                    [
                        "id" => 6,
                        "post_id" => 3,
                        "body" => "Second comment",
                        "tags" => [
                            [
                                "id" => 11,
                                "comment_id" => 6,
                                "type" => "First tag",
                                "name" => "First tag",
                                "slug" => "first-tag",
                                "tag_type" => [
                                    "id" => 11,
                                    "tag_id" => 11,
                                    "type_name" => "Tag type",
                                    "slug" => "first-tag-type"
                                ]
                            ],
                            [
                                "id" => 12,
                                "comment_id" => 6,
                                "type" => "Second tag",
                                "name" => "Second tag",
                                "slug" => "second-tag",
                                "tag_type" => [
                                    "id" => 12,
                                    "tag_id" => 12,
                                    "type_name" => "Tag type",
                                    "slug" => "first-tag-type"
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $postId = $author->posts->first()->id;

        return [$postId, $expectedResponse];
    }
}
