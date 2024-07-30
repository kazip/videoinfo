<?php

namespace kazip\VideoInfo;

class FFProbe
{
    public const string Q_480p = '480p';
    public const string Q_720p = '720p';
    public const string Q_1080p = '1080p';
    public const string Q_1440p = '1440p';
    public const string Q_2160p = '2160p';
    public const string Q_4320p = '4320p';
    public const string Q_UNKNOWN = 'unknown';

    private static function getFirstVideoStream(array $data): ?array
    {
        foreach ($data['streams'] as $stream) {
            if ($stream['codec_type'] === "video") {
                return $stream;
            }
        }
        return null;
    }

    public static function getInfo(string $filename): array
    {
        exec('ffprobe -v quiet -print_format json -show_format -show_streams ' . escapeshellarg($filename), $infoStr);
        return json_decode(implode("\n", $infoStr), true);
    }

    public static function getVideoInfo(string $filename): array
    {
        $data = self::getInfo($filename);
        return self::getFirstVideoStream($data);
    }

    public static function getFixedQuality(string $filename): string
    {
        $data = self::getInfo($filename);
        $stream = self::getFirstVideoStream($data);
        if ($stream === null) {
            return self::Q_UNKNOWN;
        }

        $width = $stream['width'];
        $height = $stream['height'];

        if ($height >= 400 && $height < 700) {
            return self::Q_480p;
        }

        if ($height >= 700 && $height < 1000) {
            return self::Q_720p;
        }

        if ($height >= 1000 && $height < 2000) {
            return self::Q_1080p;
        }

        if ($height >= 1200 && $height < 2000) {
            return self::Q_1440p;
        }

        if ($height >= 2000 && $height < 4000) {
            return self::Q_2160p;
        }

        if ($height >= 4000) {
            return self::Q_4320p;
        }

        if ($height > 400 && $height < 700) {
            return self::Q_480p;
        }

        return self::Q_UNKNOWN;

    }
}
