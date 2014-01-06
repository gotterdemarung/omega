<?php

namespace Omega\View\Syndication;


use Omega\View\PrintableInterface;

interface ItemInterface extends PrintableInterface {
    /**
     * Returns title of item
     *
     * @return string
     */
    public function getSyndicationTitle();

    /**
     * Returns description of item
     *
     * @return string
     */
    public function getSyndicationDescription();

    /**
     * Returns URL of item
     *
     * @return string
     */
    public function getSyndicationUrl();

    /**
     * Returns publishing time
     *
     * @return \DateTime
     */
    public function getSyndicationPublishingDate();

    /**
     * Returns author's email or null
     *
     * @return string|null
     */
    public function getSyndicationAuthorEmail();
}