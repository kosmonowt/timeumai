<?php

namespace App\Models;

class KimaiActivity {

    protected ?string $parentTitle;
    protected ?string $project;
    protected int    $id;
    protected string $name;
    protected bool   $visible;
    protected array  $metaFields;
    protected array  $teams;
    protected string $color;


    public function __construct(array $data) {
        $this->parentTitle = $data['parentTitle'];
        $this->project = $data['project'];
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->visible = $data['visible'];
        $this->metaFields = $data['metaFields'];
        $this->teams = $data['teams'];
        $this->color = $data['color'];
    }


    public function getParentTitle() {
        return $this->parentTitle;
    }

    public function getProject() {
        return $this->project;
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getVisible() {
        return $this->visible;
    }

    public function getMetaFields() {
        return $this->metaFields;
    }

    public function getTeams() {
        return $this->teams;
    }

    public function getColor() {
        return $this->color;
    }

    public function getQualifiedName() : string {
        return sprintf("%s (%s)", $this->name, $this->id);
    }

    public function __toString() : string {
        return $this->getQualifiedName();
    }


}
