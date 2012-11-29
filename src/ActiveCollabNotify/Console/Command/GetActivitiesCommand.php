<?php

namespace ActiveCollabNotify\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ActiveCollabNotify\ActiveCollabNotify;
use SimplePie;

/**
 * Display recent activities feed.
 * @author Kosta Harlan <kostajh@gmail.com>
 */
class GetActivitiesCommand extends Command
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
        ->setName('get-activities')
        ->setDescription('Display recent activities feed.')
        ->setHelp('The <info>get-activities</info> command displays the recent activities feed.'
        );
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $items = $this->getRecentActivities(FALSE, 5);
        if ($items) {
          foreach ($items as $item) {
            $notify = $this->sendToNotificationCenter($item);
          }
        }
    }

    /**
     * Posts data to the notification center.
     *
     * @param array $item
     */
    public function sendToNotificationCenter($item) {
      $command = '/Applications/terminal-notifier.app/Contents/MacOS/terminal-notifier';
      $command .= ' -title \'activeCollab Notify\'';
      $command .= ' -subtitle \'' . $item['title'] . '\'';
      $command .= ' -message \'' . $item['message'] . '\'';
      $command .= ' -open \'' . $item['permalink'] . '\'';
      $command .= ' -group 139';
      $ret = exec($command);
      sleep(3);
    }

    /**
     * Returns an array of items from the recent activities RSS feed.
     *
     * @param boolean $cache
     * @param int $items
     */
    public function getRecentActivities($cache = FALSE, $items = 10)
    {
      $feed = new SimplePie();
      $savedItems = array();
      $newItems = array();

      // db file name
      $savedItemsFilename = 'feed_items.php';

      // max days to keep items in db
      $numberOfDays = 3;

      $numberOfDaysInSeconds = ($numberOfDays*24*60*60);
      $expireDate = time() - $numberOfDaysInSeconds;

      $rss = $this->acNotify->rss;
      $cacheDir = $this->acNotify->getCacheDir();
      $feed->set_feed_url($rss);
      $feed->enable_cache(FALSE);
      $feed->set_cache_location($cacheDir);
      $feed->init();
      $feed->handle_content_type();

      if(file_exists($cacheDir . '/' . $savedItemsFilename))
      {
        $savedItems = unserialize(file_get_contents($cacheDir . '/' . $savedItemsFilename));
        if (!$savedItems)
        {
          $savedItems = array();
        }
      }

      $feedData = array();
      foreach ($feed->get_items() as $item) {
        if ($item->get_date('U') < $expireDate)
        {
          continue;
        }
        $id = md5($item->get_id());
        if (isset($savedItems[$id]))
        {
          continue;
        }
        $feedItem['permalink'] = $item->get_permalink();
        $feedItem['title'] = $item->get_title();
        $feedItem['date'] = $item->get_date('U');
        $feedItem['message'] = addslashes($item->get_content());
        $newItems[$id] = $feedItem;
        $savedItems[$id] = $feedItem;
      }

      $keys = array_keys($savedItems);
      foreach($keys as $key)
      {
        if ($savedItems[$key]['date'] < $expireDate)
        {
            unset($savedItems[$key]);
        }
      }

      uasort($savedItems, array('self', 'customSort'));

      file_put_contents($cacheDir . '/feed_items.php', serialize($savedItems));

      return array_slice($newItems, 0, $items);
    }

    static function customSort($a, $b)
    {
        return $b['date'] <= $a['date'];
    }

}
