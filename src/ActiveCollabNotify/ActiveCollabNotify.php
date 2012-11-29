<?php

/**
 * @file
 *   Provides command line options for interacting with activeCollab API.
 */

namespace ActiveCollabNotify;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Dumper;

/**
 * Provides methods for interacting with the ActiveCollabApi.
 */
class ActiveCollabNotify
{

  const VERSION = '0.1';


  /**
   * Constructor
   */
  public function __construct()
  {
    if (!$this->checkRequirements()) {
      return false;
    }
  }

  public function getCacheDir() {
    return __DIR__ . '/app/cache';
  }

  /**
   * Set cache.
   *
   * @param array $data
   * @param string $bin
   */
  private function cacheSet($data, $bin) {
    $file = __DIR__ . '/app/cache/' . $bin . '.yml';
    $dumper = new Dumper();
    // Convert any objects to arrays.
    $json  = json_encode($data);
    $array = json_decode($json, true);
    $yaml = $dumper->dump($array, 2);
    file_put_contents($file, $yaml);
  }

  /**
   * Get cache.
   *
   * @param string $bin
   */
  private function cacheGet($bin) {
    $yaml = new Parser();
    $fs = new Filesystem();
    $file = $this->getCacheDir . $bin . '.yml';
    if (!$fs->exists($file)) {
      $fs->touch($file);
    }
    return $yaml->parse(file_get_contents($file));
  }

  /**
   * Check to see if config file is present and for other requirements.
   *
   * @return true if all requirements pass, false otherwise.
   */
  public function checkRequirements()
  {
    $currentUser = get_current_user();
    $fs = new Filesystem();
    $configFile = '/Users/' . $currentUser . '/.active_collab';

    if (!$fs->exists($configFile)) {
      print "Please create a ~/.active_collab file.\n";
      return false;
    }

    $yaml = new Parser();

    try {
        $file = $yaml->parse(file_get_contents($configFile));
        if (isset($file['rss'])) {
          $this->rss = $file['rss'];
        } else {
          print "Please specify the RSS feed for fetching updates.";
        }
    } catch (ParseException $e) {
        printf("Unable to parse the YAML string: %s", $e->getMessage());
        return false;
    }

    // Create cache directory.
    if (!$fs->exists(__DIR__ . '/app/cache')) {
      $fs->mkdir(__DIR__ . '/app/cache');
    }

    return true;
  }

  /**
   * Clean up formatting on HTML received from activeCollab.
   *
   * @param string $text
   * @return string
   */
  public function cleanText($text) {
    $text = str_replace('</p>', "\n", $text);
    $text = str_replace('<p>', NULL, $text);
    $text = str_replace("\n \n", "\n", $text);
    $text = str_replace('<ul>', "\n", $text);
    $text = str_replace('<li>', "* ", $text);
    $text = str_replace('</li>', "\n", $text);
    $text = str_replace('</ul>', NULL, $text);
    $text = strip_tags($text);
    return $text;
  }



}
