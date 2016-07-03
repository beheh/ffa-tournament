<?php

namespace BehEh\Wizard\Entities;

/**
 * @Entity
 * @Table(name="matches")
 */
class Match extends Entity {

    /**
     * @OneToMany(targetEntity="Player", mappedBy="match")
     * @OrderBy({"points" = "DESC"})
     */
    private $players;

    /**
     * @ManyToOne(targetEntity="Round")
     */
    private $round;

    /** @Column(type="string", length=255) */
    private $name;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    public function getHash()
    {
        return 'match-'.str_replace(' ', '-', strtolower($this->name));
    }

    /**
     * @return Player[]
     */
    public function getPlayers()
    {
        return $this->players;
    }

    /**
     * @return Round
     */
    public function getRound()
    {
        return $this->round;
    }
}