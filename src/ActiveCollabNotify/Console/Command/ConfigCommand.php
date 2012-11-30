<?php

namespace ActiveCollabNotify\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use ActiveCollabNotify\ActiveCollabNotify;

/**
 * Receive updates from an AC instance in the Notification Center.
 * @author Kosta Harlan <kostajh@gmail.com>
 */
class ConfigCommand extends Command
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
        ->setName('config')
        ->setDescription('Configure AC Notify.')
        ->setDefinition(array(
            new InputArgument('rss', InputArgument::OPTIONAL, '', NULL),
            new InputOption('excludes', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Exclude strings')
        ))
        ->setHelp('Use the <info>config</info> command to configure AC Notify.
        ');
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $currentRss = $this->acNotify->getRss();
        $output->writeln('<info>Current RSS feed: </info>' . $currentRss);

        $dialog = $this->getHelperSet()->get('dialog');
        $rss = $dialog->ask(
            $output,
            '<question>Enter the address of the RSS feed for AC updates:</question> ',
            ''
        );
        $input->setArgument('rss', $rss);

        $output->writeln('<info>Current exclude strings:</info>');
          $excludeStrings = $this->acNotify->getExcludes();
          if (count($excludeStrings)) {
            foreach ($excludeStrings as $string) {
            $output->writeln('<comment>' . $string . '</comment>');
          }
        }

        $excludes = array();
        $dialog = $this->getHelperSet()->get('dialog');
        while ($dialog->askConfirmation(
                $output,
                '<question>Add an exclude string?</question> ',
                false
            )) {
          $this->getHelperSet()->get('dialog');
          $exclude = $dialog->ask(
          $output,
          '<question>Enter a string to exclude from AC notifications:</question> ',
          '');
          $output->writeln('<info>' . $exclude . '</info> added to excludes.');
          $excludes[] = $exclude;
          $output->writeln('<info>Current exclude strings:</info>');
          if (count($excludes)) {
            foreach ($excludes as $string) {
              $output->writeln('<comment>' . $string . '</comment>');
            }
          }
        }
        $input->setOption('excludes', $excludes);
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $rss = $input->getArgument('rss');
        if ($rss) {
          $this->acNotify->setRss($rss);
        }
        $excludes = $input->getOption('excludes');
        if ($excludes) {
          $this->acNotify->setExcludes($excludes);
        }
        $rss = $this->acNotify->getRss();
        $excludes = $this->acNotify->getExcludes();
        $output->writeln('<info>Current configuration:</info> ');
        $output->writeln('<info>RSS:</info> ' . $rss);
        if (count($excludes)) {
          $output->writeln('<info>Excludes:</info>');
          foreach ($excludes as $exclude) {
            $output->writeln('<comment> - ' . $exclude . '</comment>');
          }
        }
    }

}
