<?php

namespace App\Http\Controllers;

use App\Services\EventCalendarScraper;

class EventCalendarController extends Controller
{
    public function show(EventCalendarScraper $scraper)
    {
        $data = $scraper->fetch(12);

        return view('events.calendar', [
            'calendar' => $data,
        ]);
    }
}
