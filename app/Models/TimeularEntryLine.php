<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class TimeularEntryLine {

    protected string $Version;         // ["Version"]=> string(1) "4"
    protected string $TimeEntryID;     // ["TimeEntryID"]=> string(8) "51261821"
    protected string $StartDate;       // ["StartDate"]=> string(10) "2021-12-10"
    protected string $StartTime;       // ["StartTime"]=> string(8) "13:57:33"
    protected string $StartTimeOffset; // ["StartTimeOffset"]=> string(5) "+0100"
    protected string $EndDate;         // ["EndDate"]=> string(10) "2021-12-10"
    protected string $EndTime;         // ["EndTime"]=> string(8) "14:07:20"
    protected string $EndTimeOffset;   // ["EndTimeOffset"]=> string(5) "+0100"
    protected string $Duration;        // ["Duration"]=> string(8) "00:09:47"
    protected string $ActivityID;      // ["ActivityID"]=> string(7) "1066099"
    protected string $Activity;        // ["Activity"]=> string(13) "Pause / Other"
    protected string $SpaceId;         // ["SpaceId"]=> string(6) "111227"
    protected string $Space;           // ["Space"]=> string(13) "Kosmo Default"
    protected string $Username;        // ["Username"]=> string(23) "andreas@kosmoskosmos.de"
    protected string $Note;            // ["Note"]=> string(0) ""
    protected string $Mentions;        // ["Mentions"]=> string(0) ""
    protected string $Tags;            // ["Tags"]=> string(0) ""

    protected array $rawData;

    public function __construct(array $rawData) {
        $this->rawData = $rawData;
    }

    public static function make(array $data) {
        return new static($data);
    }

    public function map(array $map) : TimeularEntryLine {
        foreach ($this->rawData as $i => $column) {
            $this->{$map[$i]} = $column;
        }
        unset($this->rawData);

        return $this;
    }

    public function getVersion() {
        return $this->Version;
    }

    public function getTimeEntryID() {
        return $this->TimeEntryID;
    }

    public function getStartDate() {
        return $this->StartDate;
    }

    public function getStartTime() {
        return $this->StartTime;
    }

    public function getStartTimeOffset() {
        return $this->StartTimeOffset;
    }

    public function getEndDate() {
        return $this->EndDate;
    }

    public function getEndTime() {
        return $this->EndTime;
    }

    public function getEndTimeOffset() {
        return $this->EndTimeOffset;
    }

    public function getDuration() {
        return $this->Duration;
    }

    public function getDurationHHii() {
        return Carbon::createFromTimeString($this->Duration)->addMinute()->format("H:i");
    }

    public function getActivityID() {
        return $this->ActivityID;
    }

    public function getActivity() {
        return $this->Activity;
    }

    public function getSpaceId() {
        return $this->SpaceId;
    }

    public function getSpace() {
        return $this->Space;
    }

    public function getUsername() {
        return $this->Username;
    }

    public function getNote() {
        return $this->Note;
    }

    public function getMentions() {
        return $this->Mentions;
    }

    public function getTags() {
        return $this->Tags;
    }

    public function getQualifiedTitle() : string {
        return strlen($this->Note) ? sprintf("[%s] %s: %s ", $this->getDurationHHii(), $this->Activity, $this->Note) : sprintf("[%s] %s", $this->getDurationHHii(), $this->Activity);
    }

    public function getDescriptionWithoutMentions() : string {
        return Str::remove($this->Mentions, $this->Note);
    }

    public function __toString() : string {
        return $this->getQualifiedTitle();
    }

}
