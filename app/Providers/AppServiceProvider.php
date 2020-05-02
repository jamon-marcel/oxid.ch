<?php
namespace App\Providers;
use App\Models\Project;
use App\Models\Discourse;
use App\Models\Team;
use App\Models\Job;
use App\Models\Profile;
use App\Observers\ProjectObserver;
use App\Observers\DiscourseObserver;
use App\Observers\TeamObserver;
use App\Observers\JobObserver;
use App\Observers\ProfileObserver;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::directive('cachebust', function ($expression) {
            return "?v=" . Str::random(10);
        });
        
        setlocale(LC_MONETARY, 'de_DE');

        Project::observe(ProjectObserver::class);
        Discourse::observe(DiscourseObserver::class);
        Team::observe(TeamObserver::class);
        Job::observe(JobObserver::class);
        Profile::observe(ProfileObserver::class);

    }
}
