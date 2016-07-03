<?php

namespace BehEh\Wizard\Entities;

/**
 * @Entity
 * @Table(name="participants")
 */
class Participant extends Entity {

    /** @Column(type="string", length=255) */
    private $name;

    /** @Column(type="integer") */
    private $fraction;

    /**
     * @OneToMany(targetEntity="Player", mappedBy="participant")
     * @OrderBy({"match" = "ASC"})
     * @var Player[]
     */
    private $players;

    /**
     * @return int
     */
    public function getPoints() {
        $points = 0;
        foreach($this->players as $player) {
            if(!$player->getMatch()->getRound()->isNormalRound()) {
                continue;
            }
            $points += $player->getPoints();
        }
        return $points;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getFraction()
    {
        return $this->fraction;
    }

    /**
     * @return Player[]
     */
    public function getPlayers()
    {
        return $this->players;
    }

    const STATUS_DISQUALIFIED = -2;
    const STATUS_ELIMINATED = -1;
    const STATUS_REGISTERED = 0;
    const STATUS_PLAYING = 1;
    const STATUS_FURTHER = 2;
    const STATUS_WINNER = 3;

    /** @Column(type="integer") */
    private $status;

    /**
     * @return boolean
     */
    protected function isStatus($status)
    {
        return $this->status == $status;
    }

    public function isPlaying() {
        return $this->isStatus(self::STATUS_PLAYING);
    }

    public function isEliminated() {
        return $this->isStatus(self::STATUS_ELIMINATED);
    }

    public function isDisqualified() {
        return $this->isStatus(self::STATUS_DISQUALIFIED);
    }

    public function isFurther() {
        return $this->isStatus(self::STATUS_FURTHER);
    }

    public function isWinner() {
        return $this->isStatus(self::STATUS_WINNER);
    }

    public function isRegistered() {
        return $this->isStatus(self::STATUS_REGISTERED);
    }

    public function isInFinale() {
        foreach($this->players as $player) {
            if($player->getMatch()->getRound()->isFinale()) {
                return true;
            }
        }
        return false;
    }

    public function isInSemifinale() {
        foreach($this->players as $player) {
            if($player->getMatch()->getRound()->isSemifinale()) {
                return true;
            }
        }
        return false;
    }
}