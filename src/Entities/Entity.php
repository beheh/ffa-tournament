<?php

namespace BehEh\Wizard\Entities;

abstract class Entity {
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    protected $id = null;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}