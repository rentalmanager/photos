<?php
namespace RentalManager\Photos\Commands;


use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\Config;

class MakePhotoCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'rm:model-photo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Photo model if it does not exist';


    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Photo model';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__. '/../../stubs/photo.stub';
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        return 'App\RentalManager\AddOns\Photo';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [];
    }
}
