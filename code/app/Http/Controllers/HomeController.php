<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class HomeController
{
    public function __invoke(): RedirectResponse
    {
        return redirect(url('/api/docs'));
    }
}
