<?php

namespace App\Console\Commands;

use App\Clients\KimaiClient;
use App\Models\KimaiActivity;
use App\Models\KimaiProject;
use App\Models\KimaiTimesheet;
use App\Models\KimaiUser;
use App\Models\TimeularEntryLine;
use App\Transformers\TimeularInputFileTransformer;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class TimeularToKimaiImporter extends Command
{

    protected array $kimaiProjects;
    protected array $kimaiActivities;
    protected KimaiUser $kimaiUser;
    protected KimaiClient $kimaiClient;
    protected Collection $tagMap;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:make {--file=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports a file';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected function processLine(TimeularEntryLine $line, $i, $total) {

        do {

            $this->output->writeln(sprintf("[%03d/%03d] Processing Entry from %s:",$i, $total, $line->getStartDate()));
            $this->output->writeln($line->getQualifiedTitle());

            $activity = $this->choice("Choose activity", $this->kimaiActivities);

            if (!($activity instanceof KimaiActivity)) {
                $this->output->writeln("Skipping...");
                return;
            }

            $mentions = $line->getMentions();

            if ($this->tagMap->has($mentions)) {
                $this->output->writeln($mentions." is already assigned to Project ".$this->tagMap->get($mentions).".");
                $useKnownProject = $this->confirm("Use with this entry?", true);
            } else {
                $useKnownProject = false;
            }

            if ($useKnownProject) {
                $project = $this->tagMap->get($mentions);
            } else {
                $project = $this->choice("Choose project:", $this->kimaiProjects);
            }

            if (!($project instanceof KimaiProject)) {
                $this->output->writeln("Skipping...");
                return;
            }

            $this->output->writeln("Entry:    " . $line->getQualifiedTitle());
            $this->output->writeln("Activity: " . $activity);
            $this->output->writeln("Project:  " . $project);


        } while (!$this->confirm("Is this correct? ", true));

        $this->tagMap->put($mentions, $project);

        KimaiTimesheet::make()
            ->withUser($this->kimaiUser)
            ->fromTimeularEntryLine($line)
            ->ofActivity($activity)
            ->onProject($project)
            ->saveToClient($this->kimaiClient);

    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $file = $this->option("file");

        if (!is_string($file)) {
            $this->output->error("Add --file");
            return Command::INVALID;
        }

        if (!file_exists($file)) {
            $this->output->error("File does not exist");
            return Command::FAILURE;
        }

        $this->output->info("Welcome to Timeular to Kimai Transformer");

        $this->tagMap = collect();

        $this->kimaiUser = KimaiUser::make(19);

        $this->kimaiClient = KimaiClient::make();
        // get possible projects
        $projects = $this->kimaiClient->getProjects();
        $projects->prepend("Skip");
        $this->kimaiProjects = $projects->toArray();
        // get possible activities
        $activities = $this->kimaiClient->getActivities();
        $activities->prepend("Skip");
        $this->kimaiActivities = $activities->toArray();
        //$activities->each(fn(KimaiActivity $activity) => $this->output->writeln((string)($activity)));

        // get times
        $timeularExport = TimeularInputFileTransformer::make()->withTimeularFile($file)->transform()->get();

        $this->output->writeln("Found ".$timeularExport->count()." time entries...\n");

        $count = $timeularExport->count();

        $timeularExport->each(function (TimeularEntryLine $line, $i) use ($count) {
            $this->processLine($line, $i, $count);
        });

        return Command::SUCCESS;
    }
}
