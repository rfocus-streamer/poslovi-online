<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        // Glavne kategorije
        $webDevelopment = Category::create(['name' => 'Web Development']);
        $videoEditing = Category::create(['name' => 'Video Editing']);
        $graphicDesign = Category::create(['name' => 'Graphic Design']);

        // Podkategorije za Web Development
        Category::create(['name' => 'HTML/CSS', 'parent_id' => $webDevelopment->id]);
        Category::create(['name' => 'JavaScript', 'parent_id' => $webDevelopment->id]);
        Category::create(['name' => 'PHP/Laravel', 'parent_id' => $webDevelopment->id]);

        // Podkategorije za Video Editing
        Category::create(['name' => 'Premiere Pro', 'parent_id' => $videoEditing->id]);
        Category::create(['name' => 'After Effects', 'parent_id' => $videoEditing->id]);
        Category::create(['name' => 'Final Cut Pro', 'parent_id' => $videoEditing->id]);

        // Podkategorije za Graphic Design
        Category::create(['name' => 'Logo Design', 'parent_id' => $graphicDesign->id]);
        Category::create(['name' => 'Poster Design', 'parent_id' => $graphicDesign->id]);
        Category::create(['name' => 'UI/UX Design', 'parent_id' => $graphicDesign->id]);
    }
}
