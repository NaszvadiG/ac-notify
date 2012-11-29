<?php

namespace ActiveCollabNotify\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use ActiveCollabNotify\ActiveCollabNotify;

/**
 * Receive updates from an AC instance in the Notification Center.
 * @author Kosta Harlan <kostajh@gmail.com>
 */
class ServiceCommand extends Command
{

    /**
     * @param ActiveCollabNotify $acNotify
     */
    public function __construct(ActiveCollabNotify $acNotify = null)
    {
        $this->acNotify = $acNotify ?: new ActiveCollabNotify();
        parent::__construct();
    }

    /**
     * @see Command
     */
    protected function configure()
    {
      $this
        ->setName('service')
        ->setDescription('Starts/stops monitoring AC for updates to Notification center.')
        ->setDefinition(array(
            new InputArgument('action', InputArgument::OPTIONAL, 'start|stop', NULL),
        ))
        ->setHelp('The <info>activity-notification</info> allows configuration of updates from an AC instance in the Notification Center.
        ');
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $action = $input->getArgument('action');
        if ($action !== 'stop' && $action !== 'start') {
          if (!$action) {
            $output->writeln('<error>Please provide an argument.</error>');
          }
          $output->writeln('<error>Valid arguments are: \'start\' and \'stop\'</error>');
          return;
        }
    }

}
