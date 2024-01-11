<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Userchat;
use Illuminate\Support\Facades\Auth;
use Throwable;
use App\Events\UserChatEvent;
use Exception;
use Illuminate\Support\Facades\DB;
use OpenAI\Laravel\Facades\OpenAI;



class UserChatAnalysisJob implements ShouldQueue, ShouldBeUnique
{

    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * message from user
     *
     * @var string
     */
    public $message;

    /**
     * user ip address
     *
     * @var [type]
     */
    public $ip_address;


    public $user_id;


    /**
     * Create a new event instance.
     */
    public function __construct($user_id, $message, $ip_address)
    {
        $this->message = $message;
        $this->ip_address = $ip_address;
        $this->user_id = $user_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        DB::transaction(function () {
            try {
                UserChatEvent::dispatch("Saving data is processing...", 'wait');
                $chatSaved = Userchat::create([
                    "user_id" => $this->user_id,
                    "ip_address" => $this->ip_address,
                    'message' => $this->message
                ]);
                
                UserChatEvent::dispatch("Saving data is completed , now fetching response...", 'wait');
                $response = OpenAI::chat()->create([
                    "model"  => "gpt-3.5-turbo",
                    "messages" => [
                        [
                            "role" => "user",
                            'content' => $this->message
                        ]
                    ]
                ]);

                if ($response) {
                    $output = $response->choices[0]->message->content;


                    UserChat::where('id', $chatSaved->id)
                        ->update([
                            'response' => $output
                        ]);
                    UserChatEvent::dispatch($output, 'complete');
                } else {
                    throw new Exception("Generative AI failed to provide response");
                }
            } catch (\Throwable $th) {
                report($th);
                throw new Exception($th);
            }
        }, 1);
    }

    public function failed(Throwable $e)
    {
        // broadcasting to user this failed

        throw new Exception($e);
    }
}
