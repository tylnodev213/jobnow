<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Core\Helpers\Zipper\Zipper;

class ZipLog implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $args = [];

    protected string $type;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($args = [])
    {
        $this->type = data_get($args, 0, 'daily');
    }

    /**
     * Zip log file
     * type: month | daily
     * item:
     *     if type = month, item = 2022-01 | 2022-02, then zip file log by according to specific month specified. EX: 2022-01.zip, 2022-02.zip
     *     if type = daily, item = 2022-01-01 | 2022-01-02, then zip file log by according to specific day specified. EX: 2022-01-01.zip, 2022-01-02.zip
     * month: 2022-01-01, 2022-01-02...2022-01-31 -> 2022-01.zip
     * daily: 2022-01-01, 2022-01-02 -> 2022-01-01.zip, 2022-01-02.zip
     *
     * @return void
     */
    public function handle()
    {
        match ($this->type) {
            'month' => $this->_month(),
            'daily' => $this->_daily(),
        };
    }

    protected function _month()
    {
        try {
            $start = Carbon::now()->startofMonth()->subMonth()->firstOfMonth()->toDateString();
            $end = Carbon::now()->startofMonth()->subMonth()->endOfMonth()->toDateString();
            $filename = Carbon::now()->startOfMonth()->subMonth()->format('Y-m') . '.zip';
            $logsDir = storage_path("logs");
            $filePath = $logsDir . '/' . $filename;

            $zipper = Zipper::create($filePath);

            $listFolder = [];
            for ($i = strtotime($start); $i <= strtotime($end); $i = $i + (60 * 60 * 24)) {
                $folder = $logsDir . '/' . date('Y-m-d', $i);
                if (file_exists($folder)) {
                    $listFolder[] = $folder;
                    $zipper->add($folder);
                }
            }
            $zipper->close();
            $this->_deleteFolders($listFolder);
        } catch (\Exception $exception) {
            logError($exception->getMessage() . PHP_EOL . $exception->getTraceAsString());
        }
    }

    protected function _daily()
    {
        try {
            $keepDay = getConfig('logs.zip_log.keep_day');
            $date = Carbon::now()->subDays($keepDay)->format('Y-m-d');
            $filename = $date . '.zip';
            $logDirs = storage_path("logs");
            $filePath = $logDirs . '/' . $filename;

            $folder = $logDirs . '/' . $date;
            if (file_exists($folder)) {
                $zipper = Zipper::create($filePath);
                $zipper->add($folder);
                $zipper->close();
                $this->_deleteDir($folder);
            }
        } catch (\Exception $exception) {
            logError($exception->getMessage() . PHP_EOL . $exception->getTraceAsString());
        }
    }

    /**
     * @param $folders
     */
    protected function _deleteFolders($folders)
    {
        if (empty($folders)) {
            return;
        }

        foreach ($folders as $folder) {
            if (file_exists($folder)) {
                $this->_deleteDir($folder);
            }
        }
    }

    /**
     * @param $dir
     * @return bool
     */
    protected function _deleteDir($dir)
    {
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            if (is_dir($dir . '/' . $file)) {
                $this->_deleteDir($dir . '/' . $file);
            } else {
                unlink($dir . '/' . $file);
            }
        }
        return rmdir($dir);
    }
}
