<?php

namespace App\Providers;

use App\Models\User;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class ScrambleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Scramble::afterOpenApiGenerated(function (OpenApi $openApi) {
            $openApi->secure(
                SecurityScheme::http('bearer'),
            );

            $openApi->info->description = <<<'MD'
                ## Required Headers

                All API requests must include the following headers:

                - `Accept: application/json`
                - `Content-Type: application/json` (for requests with a body)
                MD;
        });

        Gate::define('viewApiDocs', function (?User $user) {
            return app()->environment('local');
        });
    }
}
