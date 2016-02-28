<?php

namespace Boodschappen\Console\Commands;

use Boodschappen\Database\Category;
use Illuminate\Console\Command;

class AddCategoryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cat {title} {parent}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new category with a title and parent title';

    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $category = new Category();
        $category->title = $this->argument('title');
        $parent_name = $this->argument('parent');

        if(is_int($parent_name)) {
            $parent = Category::find($parent_name);
            $category->parent_id = $parent->id;
        } else if(is_string($parent_name)) {
            $parent = Category::where('title', 'ilike', "%$parent_name%")->first();
            $category->parent_id = $parent->id;
        } else {
            $category->parent_id = 0;
            $category->depth = 0;
        }

        if($category->save()) {
            $this->info("Created category '$category->title' ($category->id) with parent '$parent->title' ($parent->id)");
        }
    }
}
