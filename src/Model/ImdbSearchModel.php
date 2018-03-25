<?php

namespace App\Model;

/**
 * Class ImdbSearchModel
 * @package App\Model
 */
class ImdbSearchModel
{
    /** @var string */
    private $imdbId;

    /** @var string */
    private $title;

    /** @var string */
    private $type;

    /** @var string */
    private $years;

    /** @var string */
    private $plot;

    public function __toArray()
    {

        return [
            'i'    => $this->imdbId ? $this->imdbId : null,
            't'    => $this->title ? $this->title : null,
            'type' => $this->type ? $this->type : null,
            'y'    => $this->years ? $this->years : null,
            'plot' => $this->plot ? $this->plot : null,
        ];
    }

    /**
     * @return string
     */
    public function getImdbId()
    {
        return $this->imdbId;
    }

    /**
     * @param string $imdbId
     * @return $this
     */
    public function setImdbId($imdbId)
    {
        $this->imdbId = $imdbId;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getYears()
    {
        return $this->years;
    }

    /**
     * @param string $years
     * @return $this
     */
    public function setYears($years)
    {
        $this->years = $years;

        return $this;
    }

    /**
     * @return string
     */
    public function getPlot()
    {
        return $this->plot;
    }

    /**
     * @param string $plot
     * @return $this
     */
    public function setPlot($plot)
    {
        $this->plot = $plot;

        return $this;
    }
}