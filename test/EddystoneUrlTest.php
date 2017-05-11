<?php

declare(strict_types = 1);

use PHPUnit\Framework\TestCase;
use Wingu\Eddystone\EddystoneUrl;
use Wingu\Eddystone\Exception\InvalidEncodedUrlException;
use Wingu\Eddystone\Exception\InvalidHttpUrlException;

class EddystoneUrlTest extends TestCase
{
    public static function dataProviderTestFromHttpUrlThrowsExceptionIfUrlIsNotValid(): array
    {
        return [
            ['ftp://example.com', 'Invalid HTTP URL because the prefix is not supported.'],
            ['www.example.com', 'Invalid HTTP URL because the prefix is not supported.'],
            ['http://', 'Invalid HTTP URL.'],
            ['http://example.com/1234567890', 'Invalid HTTP URL because it is to long.'],
            ['http://example.de/1234567', 'Invalid HTTP URL because it is to long.']
        ];
    }

    /**
     * @dataProvider dataProviderTestFromHttpUrlThrowsExceptionIfUrlIsNotValid
     *
     * @param string $url
     * @param string $expectedExceptionMessage
     */
    public function testFromHttpUrlThrowsExceptionIfUrlIsNotValid(string $url, string $expectedExceptionMessage): void
    {
        $this->expectException(InvalidHttpUrlException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        EddystoneUrl::fromHttpUrl($url);
    }

    public static function dataProviderTestFromEncodedUrlThrowsExceptionIfUrlIsNotValid(): array
    {
        return [
            ['', 'Invalid eddystone encoded url because it is to short.'],
            ['1', 'Invalid eddystone encoded url because it is to short.'],
            ['04', 'Invalid eddystone encoded url because the prefix is not supported.']
        ];
    }

    /**
     * @dataProvider dataProviderTestFromEncodedUrlThrowsExceptionIfUrlIsNotValid
     *
     * @param string $url
     * @param string $expectedExceptionMessage
     */
    public function testFromEncodedUrlThrowsExceptionIfUrlIsNotValid(
        string $url,
        string $expectedExceptionMessage
    ): void {
        $this->expectException(InvalidEncodedUrlException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        EddystoneUrl::fromEncodedUrl($url);
    }

    public static function dataProviderTestFromUrl(): array
    {
        return [
            ['http://example.com', '026578616d706c6507'],
            ['http://www.example.com', '006578616d706c6507'],
            ['https://example.com', '036578616d706c6507'],
            ['https://www.example.com', '016578616d706c6507'],
            ['http://www.example.com/123456789', '006578616d706c6500313233343536373839'],
            ['http://localhost/.com', '026c6f63616c686f73742f07'],
            ['http://a.com/http://', '026100687474703a2f2f'],
            ['https://www.www', '01777777'],
            ['http://com.com.com/.gov.com', '02636f6d07000d07']
        ];
    }

    /**
     * @dataProvider dataProviderTestFromUrl
     *
     * @param string $httpUrl
     * @param string $encodedUrl
     */
    public function testFromUrl(string $httpUrl, string $encodedUrl): void
    {
        self::assertSame($encodedUrl, EddystoneUrl::fromHttpUrl($httpUrl)->url());
        self::assertSame($httpUrl, EddystoneUrl::fromEncodedUrl($encodedUrl)->httpUrl());
    }
}
