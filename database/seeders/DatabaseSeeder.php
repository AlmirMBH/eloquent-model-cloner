<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Comment;
use App\Models\Image;
use App\Models\Post;
use App\Models\Review;
use App\Models\Tag;
use App\Models\TagType;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Author::factory()
            ->has(
                Post::factory()
                    ->sequence([
                            'title' => 'First post',
                            'body' => 'First post body'
                        ],[
                            'title' => 'Second post',
                            'body' => 'Second post body'
                        ])
                    ->for(Image::factory(
                        [
                            'file_name' => 'https://picsum.photos/200/300',
                            'file_path' => 'https://picsum.photos/200/300',
                        ]
                    ))
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
    }
}
