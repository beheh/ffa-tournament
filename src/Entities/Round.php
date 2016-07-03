<?php

namespace BehEh\Wizard\Entities;

/**
 * @Entity
 * @Table(name="rounds")
 */
class Round extends Entity
{
    const TYPE_ROUND = 0;
    const TYPE_SEMIFINALE = 1;
    const TYPE_FINALE = 2;

    /**
     * @OneToMany(targetEntity="Match", mappedBy="match")
     */
    private $player;

    /**
     * @OneToMany(targetEntity="Match", mappedBy="round")
     */
    private $matches;

    /** @Column(type="integer") */
    private $type;

    /**
     * @return mixed
     */
    public function getMatches()
    {
        return $this->matches;
    }

    protected function isType($status)
    {
        return $this->type == $status;
    }

    public function isNormalRound()
    {
        return $this->isType(self::TYPE_ROUND);
    }

    public function isSemifinale()
    {
        return $this->isType(self::TYPE_SEMIFINALE);
    }

    public function isFinale()
    {
        return $this->isType(self::TYPE_FINALE);
    }
}