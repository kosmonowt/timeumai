<?php

namespace App\Models;

use App\Clients\KimaiClient as Client;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class KimaiTimesheet {


    protected KimaiActivity $activity;
    protected KimaiProject $project;
    protected KimaiUser $user;
    protected TimeularEntryLine $timeularEntryLine;

    public function __construct() {
    }

    public static function make() : KimaiTimesheet {
        return new static();
    }

    public function fromTimeularEntryLine(TimeularEntryLine $timeularEntryLine) : KimaiTimesheet {
        $this->timeularEntryLine = $timeularEntryLine;
        return $this;
    }

    public function ofActivity(KimaiActivity $activity) : KimaiTimesheet {
        $this->activity = $activity;
        return $this;
    }

    public function withUser(KimaiUser $user) : KimaiTimesheet {
        $this->user = $user;
        return $this;
    }

    public function onProject(KimaiProject $project) : KimaiTimesheet {
        $this->project = $project;
        return $this;
    }

    public function saveToClient(Client $client) : KimaiTimesheet {
        Log::info("Saved to Client:");
        Log::info($this->toCollection()->toJson());

        $client->postTimesheet($this->toJson());

        return $this;
    }

    public function getStartTime() : string {
        $line = $this->timeularEntryLine;
        return sprintf("%sT%s%s",
                $line->getStartDate(),
                $line->getStartTime(),
                $line->getStartTimeOffset()
        );
    }

    public function getEndTime() : string {
        $line = $this->timeularEntryLine;
        return sprintf("%sT%s%s",
                       $line->getEndDate(),
                       $line->getEndTime(),
                       $line->getEndTimeOffset()
        );
    }

    /**
     *    "begin": "<dateTime>",
    "project": "<integer>",
    "activity": "<integer>",
    "end": "<dateTime>",
    "duration": "<string>",
    "description": "<string>",
    "tags": "<string>",
    "fixedRate": "<number>",
    "hourlyRate": "<number>"
     * @return Collection
     */
    public function toCollection() : Collection {
        $collection = collect();
        $collection->put("begin", $this->getStartTime());
        $collection->put("project", $this->project->getId());
        $collection->put("activity", $this->activity->getId());
        $collection->put("end", $this->getEndTime());
        $collection->put("description", $this->timeularEntryLine->getDescriptionWithoutMentions());
        return $collection;
    }

    public function toJson() : string {
        return $this->toCollection()->toJson();
    }

}
