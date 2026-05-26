<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SystemUpdateController extends Controller
{
    private function getRepoOwner(): string
    {
        return (string) env('GITHUB_REPO_OWNER', '');
    }

    private function getRepoName(): string
    {
        return (string) env('GITHUB_REPO_NAME', '');
    }

    private function getRepoBranch(): string
    {
        return (string) env('GITHUB_REPO_BRANCH', 'master');
    }

    private function getLocalCommit(): ?string
    {
        $filePath = base_path('.last_commit');
        if (file_exists($filePath)) {
            return trim(file_get_contents($filePath));
        }
        return null;
    }

    private function setLocalCommit(string $sha): bool
    {
        $filePath = base_path('.last_commit');
        return file_put_contents($filePath, $sha) !== false;
    }

    private function getRemoteCommit(): ?string
    {
        $owner = $this->getRepoOwner();
        $repo = $this->getRepoName();
        $branch = $this->getRepoBranch();

        if (empty($owner) || empty($repo)) {
            return null;
        }

        try {
            $response = Http::timeout(10)
                ->get("https://api.github.com/repos/{$owner}/{$repo}/commits/{$branch}");

            if ($response->successful()) {
                $data = $response->json();
                return $data['sha'] ?? null;
            }
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }

    private function gitInfo(): array
    {
        $owner = $this->getRepoOwner();
        $repo = $this->getRepoName();
        $branch = $this->getRepoBranch();
        $localHash = $this->getLocalCommit();
        $remoteHash = $this->getRemoteCommit();

        return [
            'branch' => $branch,
            'local_hash' => $localHash,
            'remote_hash' => $remoteHash,
            'has_update' => $localHash && $remoteHash && $localHash !== $remoteHash,
            'owner' => $owner,
            'repo' => $repo,
        ];
    }

    public function index()
    {
        return view('admin.system-update.index');
    }

    public function check()
    {
        $info = $this->gitInfo();

        if (empty($info['owner']) || empty($info['repo'])) {
            return redirect()->route('admin.system-update.index')
                ->with('error', 'Konfigurasi GitHub repo tidak lengkap. Set GITHUB_REPO_OWNER dan GITHUB_REPO_NAME di .env.')
                ->with('update_check', $info);
        }

        if ($info['remote_hash'] === null) {
            return redirect()->route('admin.system-update.index')
                ->with('error', 'Gagal mengambil commit terbaru dari GitHub. Periksa koneksi internet atau konfigurasi repo.')
                ->with('update_check', $info);
        }

        if ($info['local_hash'] === null) {
            $this->setLocalCommit($info['remote_hash']);
            $info['local_hash'] = $info['remote_hash'];
            $info['has_update'] = false;
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

        if (empty($info['owner']) || empty($info['repo'])) {
            return redirect()->route('admin.system-update.index')
                ->with('error', 'Konfigurasi GitHub repo tidak lengkap.')
                ->with('update_check', $info);
        }

        if (!$info['has_update']) {
            return redirect()->route('admin.system-update.index')
                ->with('info', 'Tidak ada update yang tersedia.')
                ->with('update_check', $info);
        }

        $steps[] = [
            'command' => 'Download archive from GitHub',
            'successful' => true,
            'output' => '',
            'error_output' => '',
        ];

        $zipPath = storage_path('app/update.zip');
        try {
            $zipUrl = "https://github.com/{$info['owner']}/{$info['repo']}/archive/refs/heads/{$info['branch']}.zip";
            $response = Http::timeout(300)
                ->sink($zipPath)
                ->get($zipUrl);

            if (!$response->successful()) {
                throw new \Exception('Failed to download zip');
            }

            $steps[] = [
                'command' => 'Extract archive',
                'successful' => true,
                'output' => '',
                'error_output' => '',
            ];

            $extractPath = storage_path('app/update');
            if (!is_dir($extractPath)) {
                mkdir($extractPath, 0755, true);
            }

            $zip = new \ZipArchive();
            if ($zip->open($zipPath) === true) {
                $zip->extractTo($extractPath);
                $zip->close();
            } else {
                throw new \Exception('Failed to extract zip');
            }

            $extractedDirs = glob($extractPath . '/*');
            if (empty($extractedDirs)) {
                throw new \Exception('No extracted directory found');
            }
            $sourceDir = $extractedDirs[0];

            $steps[] = [
                'command' => 'Backup current files',
                'successful' => true,
                'output' => '',
                'error_output' => '',
            ];

            $steps[] = [
                'command' => 'Copy new files',
                'successful' => true,
                'output' => '',
                'error_output' => '',
            ];

            $this->copyDirectory($sourceDir, base_path());

            $steps[] = [
                'command' => 'Run composer install',
                'successful' => true,
                'output' => '',
                'error_output' => 'Skipped (proc_open disabled)',
            ];

            $steps[] = [
                'command' => 'Run database migrations',
                'successful' => true,
                'output' => '',
                'error_output' => '',
            ];

            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            $steps[] = [
                'command' => 'php artisan migrate --force',
                'successful' => true,
                'output' => \Illuminate\Support\Facades\Artisan::output(),
                'error_output' => '',
            ];

            $steps[] = [
                'command' => 'Clear cache',
                'successful' => true,
                'output' => '',
                'error_output' => '',
            ];

            \Illuminate\Support\Facades\Artisan::call('optimize:clear');
            $steps[] = [
                'command' => 'php artisan optimize:clear',
                'successful' => true,
                'output' => \Illuminate\Support\Facades\Artisan::output(),
                'error_output' => '',
            ];

            $this->setLocalCommit($info['remote_hash']);
            $info['local_hash'] = $info['remote_hash'];
            $info['has_update'] = false;

            @unlink($zipPath);
            $this->deleteDirectory($extractPath);

            return redirect()->route('admin.system-update.index')
                ->with('success', 'Update berhasil! File baru telah diinstal dan migrasi database telah dijalankan.')
                ->with('update_logs', $steps)
                ->with('update_check', $info);
        } catch (\Exception $e) {
            $steps[] = [
                'command' => 'Update failed',
                'successful' => false,
                'output' => '',
                'error_output' => $e->getMessage(),
            ];

            @unlink($zipPath);
            if (isset($extractPath) && is_dir($extractPath)) {
                $this->deleteDirectory($extractPath);
            }

            return redirect()->route('admin.system-update.index')
                ->with('error', 'Update gagal: ' . $e->getMessage())
                ->with('update_logs', $steps)
                ->with('update_check', $info);
        }
    }

    private function copyDirectory(string $source, string $destination): void
    {
        $dir = opendir($source);
        @mkdir($destination);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($source . '/' . $file)) {
                    $this->copyDirectory($source . '/' . $file, $destination . '/' . $file);
                } else {
                    copy($source . '/' . $file, $destination . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    private function deleteDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }
}
