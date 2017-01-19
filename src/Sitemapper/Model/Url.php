<?php

namespace Sitemapper\Model;

use Sitemapper\Exception\InvalidUrlException;

/**
 * Class to store and validate URL
 */
class Url
{
    /**
     * String value of Url
     * @var string
     */
    protected $url;

    /**
     * Stores first defined Url Hostname
     * @var string
     */
    protected $host;

    /**
     * Stores first defined unparsed Url array
     * @var array
     */
    protected static $unparsedUrl;

    /**
     * Static string to store main hostname
     * @var string
     */
    static $same_host;

    /**
     * Url constructor.
     *
     * @param $url
     * @param $reset
     */
    public function __construct($url, $reset = false)
    {
        if ($reset) {
            self::$same_host = '';
            self::$unparsedUrl = [];
        }

        $url = $this->ensureValid($url);

        $this->url = $url;
        $this->setHost();
        if (empty(self::$unparsedUrl)) {
            $this->setSameHost();
            $this->setUnparsedUrl($url);
        }
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
        $this->setHost();
    }

    /**
     * @return bool
     */
    public function isSameHost() {
        return self::$same_host === $this->getDomainFromUrl($this->getUrl());
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return string
     */
    public function getSameHost()
    {
        return self::$same_host;
    }

    /**
     * @param bool $url
     */
    public function setHost($url = false)
    {
        $this->host = $this->getDomainFromUrl($url);
    }

    /**
     * @param $url
     * @return mixed|string
     */
    private function ensureValid($url)
    {
        $url = filter_var($url, FILTER_SANITIZE_URL);
        if (false === filter_var($url, FILTER_VALIDATE_URL)) {
            if (empty(self::$unparsedUrl)) {
                throw new InvalidUrlException();
            } else {
                $unparsedUrl = self::$unparsedUrl;
                if (substr($url, 0, 1) === '/') {
                    $unparsedUrl['path'] = '/' . ltrim($url, '/');
                } else {
                    $unparsedUrl['path'] = $unparsedUrl['path'] . $url;
                }
                return $this->unparseUrl($unparsedUrl);
            }
        }

        return $url;
    }

    /**
     * @param bool $url
     */
    public function setSameHost($url = false)
    {
        if ($url !== false) {
            $url = $this->getDomainFromUrl($url);
        } else {
            $url = $this->host;
        }
        self::$same_host = $url;
    }

    /**
     * @param bool $url
     */
    public function setUnparsedUrl($url = false)
    {
        if ($url === false) {
            $url = $this->url;
        }
        $parsed_url = parse_url($url);
        self::$unparsedUrl = $parsed_url;
    }

    /**
     * @param bool $url
     * @return bool
     */
    public function getDomainFromUrl($url = false)
    {
        if ($url === false) {
            $url = $this->url;
        }
        $parsed_url = parse_url($url);
        if ($parsed_url['scheme'] !== 'https' && $parsed_url['scheme'] !== 'http') {
            return false;
        }
        return $parsed_url['host'];
    }

    /**
     * @param bool $url
     * @return string
     */
    public function getFullPathFromUrl($url = false)
    {
        if ($url === false) {
            $url = $this->url;
        }
        $parsed_url = parse_url($url);
        $path = isset($parsed_url['path']) ? $parsed_url['path'] : '';
        $query = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
        $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';
        return "$path$query$fragment";
    }

    /**
     * @param $parsed_url
     * @return string
     */
    private function unparseUrl($parsed_url)
    {
        $scheme = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
        $host = isset($parsed_url['host']) ? $parsed_url['host'] : '';
        $port = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
        $user = isset($parsed_url['user']) ? $parsed_url['user'] : '';
        $pass = isset($parsed_url['pass']) ? ':' . $parsed_url['pass'] : '';
        $pass = ($user || $pass) ? "$pass@" : '';
        $path = isset($parsed_url['path']) ? $parsed_url['path'] : '';
        $query = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
        $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';
        return "$scheme$user$pass$host$port$path$query$fragment";
    }
}
