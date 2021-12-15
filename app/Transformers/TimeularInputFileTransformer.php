<?php

namespace App\Transformers;

use App\Models\TimeularEntryLine;
use Illuminate\Support\Collection;

class TimeularInputFileTransformer {

    protected string $filename;
    protected Collection $data;

    public function __construct() {
        $this->data = collect();
    }

    public static function make() : TimeularInputFileTransformer {
        return new static();
    }

    public function withTimeularFile(string $filename) : TimeularInputFileTransformer {
        $this->filename = $filename;
        return $this;
    }

    protected function parse() : void {
        $handle = fopen($this->filename, "r");

        $map = fgetcsv($handle);

        while ($line = fgetcsv($handle)){
            $this->data->push(TimeularEntryLine::make($line)->map($map));
        }

        fclose($handle);

    }

    public function transform() : TimeularInputFileTransformer {
        $this->parse();
        return $this;
    }

    public function get() : Collection {
        return $this->data;
    }

}
