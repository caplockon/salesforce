<?php


namespace Twobes\Salesforce;


class Helper
{
    /**
     * Build headers from mixed array
     * @param array $mixHeaders
     * @return array
     */
    public static function buildHeaders($mixHeaders = [])
    {
        $headers = [];
        foreach ( $mixHeaders as $name => $val) {
            if (is_int($name)) {
                $headers[] = $val;
            }
            else {
                $headers[] = "{$name}: {$val}";
            }
        }

        return $headers;
    }

    /**
     * Parse response body to obtain headers and content body
     *
     * @param int $headerSize
     * @param string|null $response
     * @return array
     */
    public static function parseResponse($headerSize, ?string $response)
    {
        $headerString = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);
        $headers = self::extractHeaderString($headerString);
        $contentType = isset($headers['Content-Type']) ? $headers['Content-Type'] : null;
        if ($contentType && strpos($contentType, "application/json") === 0) {
            $body = self::parseJsonString($body, true);
        }

        return [
            'headers'   => $headers,
            'body'      => $body
        ];
    }

    /**
     * Convert json string to array/stdClass
     * @param string $jsonString
     * @param bool $assoc
     * @param int $depth
     * @param int $options
     * @return false|array|\stdClass
     */
    public static function parseJsonString($jsonString, $assoc = false, $depth = 512, $options = 0)
    {
        $data = json_decode($jsonString, $assoc, $depth, $options);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $data = false;
        }

        return $data;
    }

    public static function extractHeaderString($headerString)
    {
        $lines = explode(PHP_EOL, $headerString);
        $headers = [];
        foreach ( $lines as $line ) {
            $parts = explode(": ", $line, 2);
            if (count($parts) !== 2) {
                continue;
            }

            $headerName = trim($parts[0], " \t\n\r\0\x0B");
            if ( strlen($headerName) === 0 ) {
                continue;
            }

            $headers[$headerName] = $parts[1];
        }

        return $headers;
    }
}