<?php

namespace App\Http\Controllers;

use App\Jobs\UserChatAnalysisJob;
use Illuminate\Bus\Batch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Inertia\Inertia;
use OpenAI\Laravel\Facades\OpenAI;
use Throwable;
use App\Http\Requests\ChatRequest;
use App\Events\UserChatEvent;
use Illuminate\Support\Facades\Auth;

class OpenAIController extends Controller
{


    public function index()
    {
        
        return Inertia::render("OpenAi/dashboard");
    }


    public function Store(ChatRequest $request)
    {
        try {
            $chatReq = $request->safe()->only(['message']);
            $ip = $request->ip();
            $batch = Bus::batch([])
                ->then(function (Batch $batch) {
                    // UserChatEvent::dispatch("W're working to provide solution", 'wait');
                })
                ->catch(function (Batch $batch, Throwable $e) {
                    report($e);
                    UserChatEvent::dispatch($e->getMessage(), 'complete');
                })
                ->onQueue("chatting")
                ->dispatch();


            $batch->add(
                new UserChatAnalysisJob(
                    $request->user()->id,
                    $chatReq['message'],
                    $ip
                )
            );

            return back();
        } catch (\Throwable $th) {

            dd($th);
        }
    }
}
