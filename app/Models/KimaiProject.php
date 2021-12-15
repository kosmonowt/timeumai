<?php

namespace App\Models;

use Illuminate\Support\Carbon;

class KimaiProject {

    protected string $parentTitle;
    protected int    $customer;
    protected int    $id;
    protected string $name;
    protected ?string $start;
    protected ?string $end;
    protected bool   $visible;
    protected array  $metaFields;

    public function __construct(array $data) {
        $this->parentTitle = $data['parentTitle'];
        $this->customer = $data['customer'];
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->start = $data['start'];
        $this->end = $data['end'];
        $this->visible = $data['visible'];
        $this->metaFields = $data['metaFields'];
    }

    public function getParentTitle() : string {
        return $this->parentTitle;
    }
    public function getCustomer() : int {
        return $this->customer;
    }
    public function getId() : int {
        return $this->id;
    }
    public function getName() : string {
        return $this->name;
    }
    public function getStart() : string {
        return $this->start;
    }
    public function getEnd() : string {
        return $this->end;
    }
    public function getVisible() : bool {
        return $this->visible;
    }
    public function getMetaFields() : array {
        return $this->metaFields;
    }

    public function isFinished() : bool {
        return Carbon::parse($this->end)->isPast();
    }

    public function getQualifiedName() : string {
        return sprintf("%s - %s (%s)", $this->parentTitle, $this->name, $this->id);
    }

    public function __toString() : string {
        return $this->getQualifiedName();
    }

}
