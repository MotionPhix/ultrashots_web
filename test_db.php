<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Updating existing projects with Lorem Picsum URLs..." . PHP_EOL;

// Lorem Picsum URLs for each project (matching the seeder)
$urls = [
    1 => 'https://picsum.photos/seed/ngo-coalition/800/600',
    2 => 'https://picsum.photos/seed/limbe-hospitality/800/600',
    3 => 'https://picsum.photos/seed/agriinvest/800/600',
    4 => 'https://picsum.photos/seed/techhub/800/600',
    5 => 'https://picsum.photos/seed/zomba-coffee/800/600',
    6 => 'https://picsum.photos/seed/lakeshore/800/600',
    7 => 'https://picsum.photos/seed/healthfirst/800/600',
    8 => 'https://picsum.photos/seed/malawi-fashion/800/600',
];

$updated = 0;
foreach ($urls as $sortOrder => $url) {
    $project = App\Models\Project::where('sort_order', $sortOrder)->first();
    if ($project) {
        $project->update(['poster_url' => $url]);
        $updated++;
        echo "✓ Updated: {$project->name}" . PHP_EOL;
    }
}

echo PHP_EOL . str_repeat("-", 60) . PHP_EOL;
echo "Updated {$updated} projects with poster URLs" . PHP_EOL;
echo str_repeat("-", 60) . PHP_EOL . PHP_EOL;

echo "Verifying poster_url values:" . PHP_EOL;
echo str_repeat("-", 60) . PHP_EOL;

$projects = App\Models\Project::orderBy('sort_order')->get(['name', 'poster_url', 'sort_order']);

foreach ($projects as $p) {
    $url = $p->poster_url ?: 'NULL';
    $shortName = strlen($p->name) > 40 ? substr($p->name, 0, 40) . '...' : $p->name;
    echo sprintf("[%d] %-42s => %s\n", $p->sort_order, $shortName, $url);
}

echo str_repeat("-", 60) . PHP_EOL;
echo "Total projects: " . App\Models\Project::count() . PHP_EOL;
echo "With poster_url: " . App\Models\Project::whereNotNull('poster_url')->count() . PHP_EOL;
