<?php

declare(strict_types = 1);

namespace Wingu\Eddystone;

use Wingu\Eddystone\Exception\InvalidEncodedUrlException;
use Wingu\Eddystone\Exception\InvalidHttpUrlException;

/**
 * Eddystone URL.
 *
 * @see https://github.com/google/eddystone/tree/master/eddystone-url
 */
final class EddystoneUrl
{
    /**
     * 1 char for encoded prefix + 1-17 chars for encoded url.
     */
    private const EDDYSTONE_ENCODED_URL_MAX_LENGTH = 18;

    private const EDDYSTONE_URL_PREFIXES = [
        'http://www.',
        'https://www.',
        'http://',
        'https://'
    ];

    private const EDDYSTONE_URL_SUFFIXES = [
        '.com/',
        '.org/',
        '.edu/',
        '.net/',
        '.info/',
        '.biz/',
        '.gov/',
        '.com',
        '.org',
        '.edu',
        '.net',
        '.info',
        '.biz',
        '.gov'
    ];

    /**
     * @var string
     */
    private $httpUrl;

    /**
     * @var string
     */
    private $url;

    private function __construct(string $url)
    {
        $this->url = $url;
    }

    public function url(): string
    {
        return $this->url;
    }

    public function httpUrl(): string
    {
        return $this->httpUrl;
    }

    public static function fromEncodedUrl(string $encodedUrl): self
    {
        $httpUrl = self::decodeEddystoneUrl($encodedUrl);

        $eddystoneUrl = new static($encodedUrl);
        $eddystoneUrl->httpUrl = $httpUrl;

        return $eddystoneUrl;
    }

    public static function fromHttpUrl(string $httpUrl): self
    {
        $encodedPrefix = '';
        $encodedUrl = '';
        foreach (self::EDDYSTONE_URL_PREFIXES as $key => $prefix) {
            if (\strpos($httpUrl, $prefix) === 0) {
                $encodedPrefix = \chr($key);
                $encodedUrl = \substr($httpUrl, \strlen($prefix));
                break;
            }
        }

        if (\strlen($encodedPrefix) !== 1) {
            throw InvalidHttpUrlException::reason('the prefix is not supported.');
        }

        if ($encodedUrl === '') {
            throw new InvalidHttpUrlException('Invalid HTTP URL.');
        }

        $encodedUrl = \str_replace(
            self::EDDYSTONE_URL_SUFFIXES,
            \array_map('\chr', \array_keys(self::EDDYSTONE_URL_SUFFIXES)),
            $encodedUrl
        );

        if (\strlen($encodedPrefix . $encodedUrl) > self::EDDYSTONE_ENCODED_URL_MAX_LENGTH) {
            throw InvalidHttpUrlException::reason('it is to long.');
        }

        $eddystoneUrl = new self(\bin2hex($encodedPrefix . $encodedUrl));
        $eddystoneUrl->httpUrl = $httpUrl;

        return $eddystoneUrl;
    }

    private static function decodeEddystoneUrl(string $encodedUrl): string
    {
        if (\strlen($encodedUrl) < 2) {
            throw InvalidEncodedUrlException::reason('it is to short.');
        }

        $url = \hex2bin($encodedUrl);

        // First 2 characters represent a prefix.
        $prefix = \str_replace(
            \array_map('\chr', \array_keys(self::EDDYSTONE_URL_PREFIXES)),
            self::EDDYSTONE_URL_PREFIXES,
            $url[0]
        );

        if (!\in_array($prefix, self::EDDYSTONE_URL_PREFIXES, true)) {
            throw InvalidEncodedUrlException::reason('the prefix is not supported.');
        }

        $rest = \str_replace(
            \array_map('\chr', \array_keys(self::EDDYSTONE_URL_SUFFIXES)),
            self::EDDYSTONE_URL_SUFFIXES,
            \substr($url, 1)
        );

        return $prefix . $rest;
    }
}
