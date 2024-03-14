<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JobTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $projectTypes = [
            'UI/UX',
            '3D Animations',
            'Photography',
            'Logo Design',
            'Web Design',
            'Graphic Design',
            'Illustration',
            'Motion Graphics',
            'Product Design',
            'Branding',
            'Packaging Design',
            'User Interface Design',
            'User Experience Design',
            'Fashion Design',
            'Game Design',
            'Industrial Design',
            'Digital Painting',
            'Character Design',
            'Icon Design',
            'Typography Design',
            'Mobile App Design',
            'Website Redesign',
            'Print Design',
            'Concept Art',
            'Environmental Design',
            'Infographic Design',
            'Book Cover Design',
            'Magazine Layout Design',
            'Poster Design',
            'Animation Design',
            'Logo Animation',
            'Visual Effects (VFX)',
            'Typography Design',
            'Interactive Design',
            'Virtual Reality (VR) Design',
            'Augmented Reality (AR) Design',
            'Pattern Design',
            'Textile Design',
            'Jewelry Design',
            'Caricature Design',
            'Tattoo Design',
            'Album Cover Design',
            'Podcast Cover Design',
            'UI Design',
            'Iconography Design',
            'Illustrative Design',
            'Motion Design',
            'Conceptual Design',
            'Storyboarding',
            'Responsive Design',
            'Digital Illustration',
            'Game Environment Design',
            'Visual Identity Design',
            'Advertising Design',
            'Character Animation',
            'Storyboard Illustration',
            'Print Layout Design',
            'Package Label Design',
            'UI Animation',
            'Digital Sculpting',
            'User Flow Design',
            'Data Visualization Design',
            'Sound Design'
        ];
        foreach ($projectTypes as $types) {
            DB::table('job_type')->insert([
                'project_type' => $types,
            ]);
        }

    }
}
