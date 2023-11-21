<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use OpenAI\Laravel\Facades\OpenAI;

class OpenAIController extends Controller
{


    public function index()
    {
        return Inertia::render("OpenAi/dashboard");
    }


    public function Store(Request $request)
    {
    try {
        
        $content = $request->get("content");
        $result = OpenAI::chat()->create([
            "model" => "gpt-3.5-turbo",
            "messages" => [
               [
                "role" => "user",
                "content" => $content
               ],
            ],
        ]);
        $result =   $result->choices[0]->message->content;
        return back()->with("message",$result);
        

    } catch (\Throwable $th) {
        
        dd($th);
    }
    }
}
