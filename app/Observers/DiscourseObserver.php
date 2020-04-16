<?php
namespace App\Observers;
use App\Models\Discourse;

class DiscourseObserver
{
    /**
     * Handle the project "created" event.
     *
     * @param  \App\Discourse  $discourse
     * @return void
     */
    public function created(Discourse $discourse)
    {
        //
    }

    /**
     * Handle the project "updated" event.
     *
     * @param  \App\Discourse  $discourse
     * @return void
     */
    public function updated(Discourse $discourse)
    {
        //
    }

    /**
     * Handle the project "deleted" event.
     *
     * @param  \App\Discourse  $discourse
     * @return void
     */
    public function deleted(Discourse $discourse)
    {
        //
    }

    /**
     * Handle the project "deleting" event.
     *
     * @param  \App\Discourse  $discourse
     * @return void
     */
    public function deleting(Discourse $discourse)
    {
        $discourse->images()->delete();
        $discourse->documents()->delete();
    }

    /**
     * Handle the project "restored" event.
     *
     * @param  \App\Discourse  $discourse
     * @return void
     */
    public function restored(Discourse $discourse)
    {
        //
    }

    /**
     * Handle the project "force deleted" event.
     *
     * @param  \App\Discourse  $discourse
     * @return void
     */
    public function forceDeleted(Discourse $discourse)
    {
        //
    }
}
