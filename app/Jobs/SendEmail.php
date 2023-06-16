<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $recipient;
    protected $mail;
    /**
     * Create a new job instance.
     */
    public function __construct($recipient, $mail)
    {
        $this->recipient = $recipient;
        $this->mail = $mail;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to((isset($this->recipient['to'])) ? $this->recipient['to'] : [])
            ->cc((isset($this->recipient['cc'])) ? $this->recipient['cc'] : [])
            ->bcc((isset($this->recipient['bcc'])) ? $this->recipient['bcc'] : [])            
            ->send($this->mail);
    }
}
