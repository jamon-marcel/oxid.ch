<?php
namespace App\Observers;
use App\Models\Profile;

class ProfileObserver
{
    /**
     * Handle the project "created" event.
     *
     * @param  \App\Profile $profile
     * @return void
     */
    public function created(Profile $profile)
    {
        //
    }

    /**
     * Handle the project "updated" event.
     *
     * @param  \App\Profile $profile
     * @return void
     */
    public function updated(Profile $profile)
    {
        //
    }

    /**
     * Handle the project "deleted" event.
     *
     * @param  \App\Profile $profile
     * @return void
     */
    public function deleted(Profile $profile)
    {
        //
    }

    /**
     * Handle the project "deleting" event.
     *
     * @param  \App\Profile $profile
     * @return void
     */
    public function deleting(Profile $profile)
    {
       $profile->images()->delete();
    }

    /**
     * Handle the project "restored" event.
     *
     * @param  \App\Profile $profile
     * @return void
     */
    public function restored(Profile $profile)
    {
        //
    }

    /**
     * Handle the project "force deleted" event.
     *
     * @param  \App\Profile $profile
     * @return void
     */
    public function forceDeleted(Profile $profile)
    {
        //
    }
}
