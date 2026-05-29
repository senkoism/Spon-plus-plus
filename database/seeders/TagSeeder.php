<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            'Math', 'Science', 'History', 'Programming', 'AI', 'Business',
            'Marketing', 'Design', 'English', 'Finance', 'Cybersecurity',
            'Data Science', 'Leadership', 'Productivity', 'Psychology',
            'Physics', 'Chemistry', 'Biology', 'Web Development',
            'Machine Learning', 'Cloud Computing', 'UI/UX', 'Mobile Development',
            'Photography', 'Music', 'Entrepreneurship', 'Communication',
            'Research', 'Project Management', 'Language Learning',
        ];
        foreach ($tags as $tag) \App\Models\Tag::firstOrCreate(['name' => $tag]);
    }
}
