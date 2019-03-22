<?php

namespace App\Commands;

use Alr\ObjectDotNotation\Data;
use App\Models\Agent;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class AgentProcess extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'agent:process {agent}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Process specified agent';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (file_exists($this->argument('agent'))) {
            $data = json_decode(file_get_contents($this->argument('agent')));
            $data->filename = $this->argument('agent');
            $d = Data::load($data);

            $agent_type = '\\App\\Agents\\'.$d->get('type');
            $ag = new $agent_type();
            $ag->process($d);

            file_get_contents(base_path('muninn-data/agents-status.json'));
            $ag->last_check = Carbon::now();
        }
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
