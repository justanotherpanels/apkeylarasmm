<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;

class SystemUpdateController extends Controller
{
    private function branchName(): string
    {
        return (string) env('APP_UPDATE_BRANCH', 'master');
    }

    private function runCommand(array $command, int $timeout = 900): array
    {
        $process = new Process($command, base_path());
        $process->setTimeout($timeout);
        $process->run();

        return [
            'command' => implode(' ', $command),
            'successful' => $process->isSuccessful(),
            'exit_code' => $process->getExitCode(),
            'output' => trim($process->getOutput()),
            'error_output' => trim($process->getErrorOutput()),
        ];
    }

    private function gitInfo(): array
    {
        $branch = $this->branchName();

        $local = $this->runCommand(['git', 'rev-parse', 'HEAD'], 30);
        $remote = $this->runCommand(['git', 'ls-remote', 'origin', '-h', "refs/heads/{$branch}"], 60);

        $localHash = $local['successful'] ? trim($local['output']) : null;
        $remoteHash = null;

        if ($remote['successful'] && $remote['output'] !== '') {
            $parts = preg_split('/\s+/', $remote['output']);
            $remoteHash = $parts[0] ?? null;
        }

        return [
            'branch' => $branch,
            'local_hash' => $localHash,
            'remote_hash' => $remoteHash,
            'has_update' => $localHash && $remoteHash && $localHash !== $remoteHash,
            'local_meta' => $local,
            'remote_meta' => $remote,
        ];
    }

    public function index()
    {
        return view('admin.system-update.index');
    }

    public function check()
    {
        $info = $this->gitInfo();

        if (!$info['local_meta']['successful'] || !$info['remote_meta']['successful']) {
            return redirect()->route('admin.system-update.index')
                ->with('error', 'Gagal check update. Pastikan git remote/server akses internet normal.')
                ->with('update_check', $info);
        }

        $message = $info['has_update']
            ? 'Update tersedia dari GitHub.'
            : 'Versi server sudah paling baru.';

        return redirect()->route('admin.system-update.index')
            ->with('success', $message)
            ->with('update_check', $info);
    }

    public function update(Request $request)
    {
        $info = $this->gitInfo();
        $steps = [];

        if (!$info['local_meta']['successful'] || !$info['remote_meta']['successful']) {
            return redirect()->route('admin.system-update.index')
                ->with('error', 'Gagal membaca status git lokal/remote.')
                ->with('update_logs', [$info['local_meta'], $info['remote_meta']])
                ->with('update_check', $info);
        }

        $dirtyCheck = $this->runCommand(['git', 'status', '--porcelain'], 30);
        $steps[] = $dirtyCheck;

        if (!$dirtyCheck['successful']) {
            return redirect()->route('admin.system-update.index')
                ->with('error', 'Gagal memeriksa status repository.')
                ->with('update_logs', $steps)
                ->with('update_check', $info);
        }

        if ($dirtyCheck['output'] !== '') {
            return redirect()->route('admin.system-update.index')
                ->with('error', 'Update dibatalkan karena ada perubahan lokal di server. Commit/stash dulu perubahan lokal.')
                ->with('update_logs', $steps)
                ->with('update_check', $info);
        }

        $commands = [
            ['git', 'fetch', 'origin'],
            ['git', 'pull', 'origin', $this->branchName()],
            ['composer', 'install', '--no-dev', '--optimize-autoloader', '--no-interaction'],
            ['php', 'artisan', 'migrate', '--force'],
            ['php', 'artisan', 'optimize:clear'],
            ['php', 'artisan', 'config:cache'],
            ['php', 'artisan', 'route:cache'],
            ['php', 'artisan', 'view:cache'],
            ['php', 'artisan', 'queue:restart'],
        ];

        foreach ($commands as $command) {
            $result = $this->runCommand($command);
            $steps[] = $result;

            if (!$result['successful']) {
                return redirect()->route('admin.system-update.index')
                    ->with('error', 'Update gagal pada step: ' . $result['command'])
                    ->with('update_logs', $steps)
                    ->with('update_check', $this->gitInfo());
            }
        }

        return redirect()->route('admin.system-update.index')
            ->with('success', 'Update berhasil. Code, fitur, design, dan migrasi database telah dijalankan.')
            ->with('update_logs', $steps)
            ->with('update_check', $this->gitInfo());
    }
}

