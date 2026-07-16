<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubscribeRequest;
use App\Models\Subscriber;

class SubscriberController extends Controller
{
    public function store(SubscribeRequest $request)
    {
        Subscriber::firstOrCreate(
            ['email' => strtolower(trim($request->validated('email')))],
            ['is_active' => true]
        );

        return back()->with('success', 'You are on the list! Fresh bagel news coming your way.');
    }
}
