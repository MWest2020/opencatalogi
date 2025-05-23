<?php

namespace OCA\OpenCatalogi\Cron;

use OCA\OpenCatalogi\Service\DirectoryService;
use OCP\BackgroundJob\TimedJob;
use OCP\AppFramework\Utility\ITimeFactory;

/**
 *
 * Docs: https://docs.nextcloud.com/server/latest/developer_manual/basics/backgroundjobs.html
 */
class DirectorySync extends TimedJob {

    private DirectoryService $directoryService;

    public function __construct() {

        // Run once an hour
        $this->setInterval(3600);

        // Only run one instance of this job at a time.
        $this->setAllowParallelRuns(false);
    }//end __construct

    /**
     * Lets run the cron sync
     * 
     * @param array $arguments
     * @param DirectoryService $directoryService
     */
    protected function run($arguments) {
        // @todo disabled for now, triggers to many times and current state is broken and needs fixing/refactor
        // $this->directoryService->doCronSync();
    }


}
