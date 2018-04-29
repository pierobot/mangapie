<?php

namespace App;

use RuntimeException;

class LogParser
{
    /**
     * Gets all the lines that match a log level from a string.
     *
     * @param $level The log level to get.
     * @param $contents A string that contains the log contents.
     * @return Mixed. On success, an array that contains the matched lines on success. Otherwise FALSE.
     */
    private static function getLines($level, $contents)
    {
        $lineContents = null;
        // match all the lines including exception stacktrace
        $pattern = '/(.+' . mb_convert_case($level, MB_CASE_UPPER) . ': [\s\S]+) \n/mU';
        preg_match_all($pattern, $contents, $lineContents);

        return empty($lineContents) ? false : $lineContents[1];
    }

    /**
     * Gets the date time value from a log line.
     *
     * @param $line
     * @return Mixed. On success, a string that contains the date time or false on failure.
     */
    private static function getDateTime($line)
    {
        $pattern = '/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/';
        $dateTime = null;

        preg_match($pattern, $line, $dateTime);

        return empty($dateTime) ? false : $dateTime[1];
    }

    /**
     * Gets the environment value from a log line.
     *
     * @param $line
     * @return Mixed. On success, a string that contains the environment or false on failure.
     */
    private static function getEnvironment($line)
    {
        $pattern = '/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\] (\w+)\./';
        $environment = null;

        preg_match($pattern, $line, $environment);

        return empty($environment) ? false : $environment[1];
    }

    /**
     * Gets the message and context from a log line.
     *
     * @param $line
     * @return Mixed. On success, a string that contains the message and context or false on failure.
     */
    private static function getMessageContext($line)
    {
        $pattern = '/: (.+)/s';
        $messageContext = null;

        preg_match($pattern, $line, $messageContext);

        return empty($messageContext) ? false : $messageContext[1];
    }

    /**
     * @param $level
     * @return array
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function get($level)
    {
        if ($level != 'debug' &&
            $level != 'info'&&
            $level != 'notice' &&
            $level != 'warning' &&
            $level != 'error' &&
            $level != 'critical' &&
            $level != 'alert' &&
            $level != 'emergency') {

            throw new RuntimeException('Unsupported log level given: ' . $level);
        }

        $results = [];

        // get an array of all the log files
        $logFiles = \File::glob(storage_path('logs/*.log'));
        // go through each
        foreach ($logFiles as $logFile) {
            if (\File::isReadable($logFile)) {

                $logContents = \File::get($logFile);
                $lineContents = self::getLines($level, $logContents);
                if (empty($lineContents) == true)
                    continue;

                foreach ($lineContents as $line) {
                    $dateTime = self::getDateTime($line);
                    $environment = self::getEnvironment($line);
                    $messageContext = self::getMessageContext($line);

                    array_push($results, [
                        'log' => $logFile,
                        'datetime' => $dateTime,
                        'environment' => $environment,
                        'messagectx' => $messageContext
                    ]);
                }
            }
        }

        return $results;
    }
}
