<?php

namespace ActiveCollabNotify\Console;

use Symfony\Component\Console\Application as BaseApplication;
use ActiveCollabNotify\Console\Command\ServiceCommand;
use ActiveCollabNotify\Console\Command\GetActivitiesCommand;
use ActiveCollabNotify\ActiveCollabNotify;

/**
* @author Kosta Harlan <kostajh@gmail.com>
*/
class Application extends BaseApplication
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        error_reporting(-1);
        parent::__construct('ActiveCollab Notify', ActiveCollabNotify::VERSION);
        $this->add(new ServiceCommand());
        $this->add(new GetActivitiesCommand());
    }

    /**
     * Return long version.
     *
     * @return the version info for the application.
     */
    public function getLongVersion()
    {
        return parent::getLongVersion().' by <comment>Kosta Harlan</comment>';
    }
}
