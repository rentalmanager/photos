<?php
namespace RentalManager\Photos\Commands;


use Illuminate\Console\Command;

class SetupCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'rm:setup-photos';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup models and add the traits.';


    /**
     * Commands to call with their description.
     *
     * @var array
     */
    protected $calls = [
        'rm:model-photo' => 'Setup the model',
        'rm:add-photoable-trait' => 'Add the traits'
    ];

    /**
     * Create a new command instance
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->calls as $command => $info) {
            $this->line(PHP_EOL . $info);
            $this->call($command);
        }
    }
}
