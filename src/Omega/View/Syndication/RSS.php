<?php

namespace Omega\View\Syndication;


use Omega\IO\HTTPPrintWriterInterface;
use Omega\IO\PrintWriterInterface;
use Omega\IO\StdOut;
use Omega\View\PrintableInterface;

/**
 * Class RSS
 * RSS Syndication printer
 *
 * @package Omega\View\Syndication
 * @todo tests needed
 */
class RSS implements PrintableInterface {
    static private $_allowedHeaders = array(
        'title'         => 'string',
        'description'   => 'string',
        'link'          => 'string',
        'lastBuildDate' => 'time',
        'pubDate'       => 'time',
        'language'      => 'string',
        'generator'     => 'string',
        'image'         => 'string',
        'ttl'           => 'string'     // Number of minutes
    );

    /**
     * Associative array of headers
     *
     * @var string[]
     */
    private $_headers = array();

    /**
     * Linear array of entries
     *
     * @var ItemInterface[]
     */
    private $_items = array();

    /**
     * Sets RSS header
     * Supports method chaining call
     *
     * @param string $key
     * @param mixed  $value
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setHeader($key, $value)
    {
        if (!isset(self::$_allowedHeaders[$key])) {
            throw new \InvalidArgumentException(
                "Key `$key` is not valid RSS header"
            );
        }
        if ($value !== null) {
            $this->_headers[$key] = (string) $value;
        }

        return $this;
    }

    /**
     * Adds an item toRSS
     * Supports method chaining call
     *
     * @param ItemInterface $item
     * @return $this
     */
    public function addItem(ItemInterface $item)
    {
        $this->_items[] = $item;

        return $this;
    }

    /**
     * Outputs contents of object to provided PrintWriter
     *
     * @param PrintWriterInterface $target
     * @return mixed
     * @throws \BadMethodCallException
     */
    public function printTo(PrintWriterInterface $target)
    {
        // Checking required headers
        foreach (array('title', 'link', 'description') as $requiredHeader) {
            if (!isset($this->_headers[$requiredHeader])) {
                throw new \BadMethodCallException(
                    "Required Channel attribute `{$requiredHeader}` not set"
                );
            }
        }


        // Sending header
        if ($target instanceof HTTPPrintWriterInterface) {
            $target->writeHTTPHeader('Content-Type', 'application/rss+xml');
        }

        // Sending
        $target->writeln('<?xml version="1.0" encoding="UTF-8" ?>');
        $target->writeln('<rss version="2.0">');
        $target->writeln('<channel>');
        foreach ($this->_headers as $key => $value) {
            $target->write("\t");
            $target->write('<' . $key . '>');
            $target->write($this->_envelope(self::$_allowedHeaders[$key], $value));
            $target->write('</' . $key . '>');
            $target->writeln();
        }
        foreach ($this->_items as $item) {
            $target->writeln("\t<item>");
            $target->writeln("\t\t<title>" . $this->_envelope('string', $item->getSyndicationTitle()) . '</title>');
            $target->writeln("\t\t<link>" . $this->_envelope('string', $item->getSyndicationUrl()) . '</link>');
            $target->writeln("\t\t<guid isPermaLink=\"true\">" . $this->_envelope('string', $item->getSyndicationUrl()) . '</guid>');
            $target->writeln("\t\t<description>" . $this->_envelope('string', $item->getSyndicationDescription()) . '</description>');
            $target->writeln("\t\t<pubDate>" . $this->_envelope('time', $item->getSyndicationPublishingDate()) . '</pubDate>');
            if ($item->getSyndicationAuthorEmail() !== null) {
                $target->writeln("\t\t<author>" . $this->_envelope('string', $item->getSyndicationAuthorEmail()) . '</author>');
            }
            $target->writeln("\t</item>");
        }

        $target->writeln('</channel>');
        $target->writeln('</rss>');
    }

    /**
     * Returns string representation
     *
     * @return string
     */
    public function __toString()
    {
        $this->printTo(new StdOut());
    }


    /**
     * Envelopes value in CDATA tag if needed
     *
     * @param string $type
     * @param mixed  $value
     * @return string
     */
    private function _envelope($type, $value)
    {
        if ($value === null) {
            return '';
        }

        if ($type === 'time') {
            if ($value instanceof \DateTime) {
                return $value->format(\DateTime::RSS);
            }
            $value = (string) $value;
            if (ctype_digit($value)) {
                return date('r', $value);
            }
        }

        if (strpos($value, '<') !== false || strpos($value, '>') !== false) {
            return '<![CDATA[' . $value . ']]>';
        }

        return $value;
    }

} 