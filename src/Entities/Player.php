<?php

namespace BehEh\Wizard\Entities;
use Symfony\Component\Console\Exception\RuntimeException;

/**
 * @Entity
 * @Table(name="players",uniqueConstraints={@UniqueConstraint(name="match_points", columns={"match_id", "points"})})
 */
class Player {
    
    /**
     * @Id
     * @ManyToOne(targetEntity="Match", inversedBy="players")
     * @var $match Match
     */
    private $match;

    /**
     * @Id
     * @ManyToOne(targetEntity="Participant", inversedBy="players")
     */
    private $participant;

    /** @Column(type="integer", nullable=true) */
    private $points;
    
    /**
     * @return integer
     */
    public function getPoints()
    {
        if(!$this->getMatch()->getRound()->isNormalRound()) {
            throw new RuntimeException('Cannot get points from a match in a special round');
        }
        return $this->points;
    }

    public function getPointDifference()
    {
        return '+' . (0 + $this->getPoints());
    }

    public function isPlaying()
    {
        return $this->points === null;
    }

    public function getRank()
    {
        return '42';
    }

    /**
     * @return Match
     */
    public function getMatch()
    {
        return $this->match;
    }

    /**
     * @return mixed
     */
    public function getParticipant()
    {
        return $this->participant;
    }

}
