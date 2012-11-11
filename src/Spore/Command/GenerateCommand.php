<?php
/**
 * @package     Spore
 * @author      Axel Etcheverry <axel@etcheverry.biz>
 * @copyright   Copyright (c) 2012 Axel Etcheverry (http://www.axel-etcheverry.com)
 * @license     MIT
 */

/**
 * @namespace
 */
namespace Spore\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use SplFileInfo;
use InvalidArgumentException;
use RuntimeException;
use Spore\Command\Helper\DialogHelper;
use Spore\Builder;

class GenerateCommand extends Command
{
    protected function configure()
    {
        $this->setDefinition(array(
            new InputArgument('spec', InputArgument::REQUIRED, 'spec file'),
            new InputOption('namespace', '', InputOption::VALUE_REQUIRED, 'The namespace of the client to create', 'Acme/Client'),
        ))
        ->setDescription('Creating the client from the spec file.')
        ->setName('generate:client');
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getDialogHelper();
        $dialog->writeSection($output, 'Welcome to the Spore client generator');

        $output->writeln(array(
            '',
            'Use <comment>/</comment> instead of <comment>\\ </comment> for the namespace delimiter to avoid any problem.',
            '',
        ));
        $namespace = $dialog->askAndValidate(
            $output, 
            $dialog->getQuestion('Client namespace', $input->getOption('namespace')), 
            array('Spore\Command\Validators', 'validateNamespace'), 
            false, 
            $input->getOption('namespace')
        );
        $input->setOption('namespace', $namespace);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getDialogHelper();

        foreach (array('namespace') as $option) {
            if (null === $input->getOption($option)) {
                throw new RuntimeException(sprintf('The "%s" option must be provided.', $option));
            }
        }

        $namespace = Validators::validateNamespace($input->getOption('namespace'));
        $dir = str_replace("\\", DIRECTORY_SEPARATOR, $namespace);

        $dialog->writeSection($output, 'Client generation');

        $file = new SplFileInfo($input->getArgument('spec'));

        if(!$file->isFile()) {
            throw new InvalidArgumentException(sprintf(
                "%s it is not a file.", 
                $file->getRealPath()
            ));
        }

        if(!$file->isReadable()) {
            throw new InvalidArgumentException(sprintf(
                "Unable to read file %s", 
                $file->getRealPath()
            ));
        }

        $spec = json_decode(file_get_contents($file->getRealPath()), true);
        $className = ucfirst(strtolower($spec['name']));
        
        $spec["name"]       = $className;
        $spec["namespace"]  = $namespace;
        $spec["base_url"]   = rtrim($spec["base_url"], '/');

        $methods = array();

        foreach ($spec["methods"] as $key => $value) {

            $parts = explode('_', $key);

            if (in_array($parts[0], array(
                'get', 
                'post', 
                'put', 
                'delete', 
                'head', 
                'options', 
                'patch', 
                'trace', 
                'connect'
            ))) {
                unset($parts[0]);
            }

            $parts = array_map(function($part) {
                return ucfirst(strtolower($part));
            }, $parts);

            $value["name"] = strtolower($value["method"]) . implode('', $parts);
            $value["method"] = strtolower($value["method"]);
            $methods[$key] = $value;
        }

        $spec["methods"] = $methods;
        unset($methods);

        $build = realpath(__DIR__ . '/../../../build/') . DIRECTORY_SEPARATOR . $dir;
        
        $builder = new Builder();

        $builder->renderFile($build . DIRECTORY_SEPARATOR . $className . '.php', $spec);
        
        unset($spec);

        $output->writeln('Generating the client code: <info>OK</info>');
    }

    protected function getDialogHelper()
    {
        $dialog = $this->getHelperSet()->get('dialog');
        if (!$dialog || get_class($dialog) !== 'Spore\Command\Helper\DialogHelper') {
            $this->getHelperSet()->set($dialog = new DialogHelper());
        }

        return $dialog;
    }
}