<?php

namespace App\Jobs;

use App\Library\MailService;

class TaskMailErrorJob extends Job
{
    private $mailService;

    private
        $data;


    /**
     * TaskMailJob constructor.
     * @param MailService $mailService
     * @param array $data
     */
    public function __construct(MailService $mailService, array $data)
    {
        $this->mailService = $mailService;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->mailService->send_errors($this->data);
    }
}
