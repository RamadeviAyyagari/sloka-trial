<?php

namespace Framework;

class Profiler
{
    /** @var array */
    public static $time = [];

    /** @var array */
    public static $memory = [];

    /** @var array */
    public static $events = [];

    /**
     * @param array $profiler
     */
    public static function start(array $profiler = []): void
    {
        self::$time['start'] = $profiler['time'] ?? microtime();
        self::$memory['start'] = $profiler['memory'] ?? memory_get_usage();
    }

    /**
     * @return void
     */
    public static function stop(): void
    {
        self::$time['end'] = $startTime ?? microtime();
        self::$memory['end'] = memory_get_usage();
    }

    /**
     * @param string $eventName
     */
    public static function startEvent(string $eventName): void
    {
        self::$events[$eventName]['start'] = [
            'time' => microtime(),
            'memory' => memory_get_usage(),
        ];
    }

    /**
     * @param string $eventName
     */
    public static function stopEvent(string $eventName): void
    {
        self::$events[$eventName]['end'] = [
            'time' => microtime(),
            'memory' => memory_get_usage(),
        ];
    }

    /**
     * @param string $eventName
     * @return array
     */
    public static function getEvent(string $eventName): array
    {
        return [
            'time' => self::$events[$eventName]['end']['time'] - self::$events[$eventName]['start']['time'],
            'memory' => self::$events[$eventName]['end']['memory'] - self::$events[$eventName]['start']['memory'],
        ];
    }

    /**
     * @return array
     */
    public static function getAllEvents(): array
    {
        $eventsArray = [];
        foreach (self::$events as $eventName) {
            $eventsArray[$eventName] = self::getEvent($eventName);
        }
        return $eventsArray;
    }

    /**
     * @return array
     */
    public static function getCurrentState(): array
    {
        return [
            'time' => microtime() - self::$time['start'],
            'memory' => memory_get_usage() - self::$memory['start'],
        ];
    }
}