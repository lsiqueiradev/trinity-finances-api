<?php

namespace App\Listeners;

use Database\Seeders\CategoriesUserSeeder;
use Illuminate\Auth\Events\Registered;

class CreateUserCategories
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        $user = $event->user;

        (new CategoriesUserSeeder())->run($user->id);
    }
}
