<?php

namespace App\Clients;

use App\Models\KimaiActivity;
use App\Models\KimaiProject;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;

class KimaiClient {

    protected string $KIMAI_API_URL;
    protected string $X_AUTH_USER;
    protected string $X_AUTH_TOKEN;
    protected Client $client;

    public function __construct() {
        $this->KIMAI_API_URL  =  config("timeumai.kimai_api_url");
        $this->X_AUTH_USER = config("timeumai.kimai_user");
        $this->X_AUTH_TOKEN = config("timeumai.kimai_token");

        $this->client = new Client([
            'headers' => ['X-AUTH-USER' => $this->X_AUTH_USER, 'X-AUTH-TOKEN' => $this->X_AUTH_TOKEN],
            'verify' => false,
            'base_uri' => $this->KIMAI_API_URL
        ]);

    }

    public static function make() : KimaiClient {
        return new static();
    }

    public function getProjects() : Collection {
        $response = $this->client->get("/api/projects");

        $projectsRaw = json_decode($response->getBody(), true);

        $projects = collect();

        foreach ($projectsRaw as $project) {
            $projects->push(new KimaiProject($project));
        }

        return $projects;
    }

    public function getActivities() : Collection {
        $response = $this->client->get("/api/activities");

        $activitiesRaw = json_decode($response->getBody(), true);
        $activities = collect();

        foreach ($activitiesRaw as $activity) {
            $activities->push(new KimaiActivity($activity));
        }

        return $activities;
    }

    public function postTimesheet(string $timesheetAsJson) : bool {
        $response = $this->client->post("/api/timesheets", ["body" => $timesheetAsJson]);
        return true;
    }

}
