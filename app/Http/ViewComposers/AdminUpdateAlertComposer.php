<?php

namespace App\Http\ViewComposers;

use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class AdminUpdateAlertComposer
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

    private function hasUpdate(): bool
    {
        $local = $this->getLocalCommit();
        $remote = $this->getRemoteCommit();

        return $local && $remote && $local !== $remote;
    }

    public function compose(View $view)
    {
        $cacheKey = 'admin_update_check';
        
        $hasUpdate = cache()->remember($cacheKey, 300, function () {
            try {
                return $this->hasUpdate();
            } catch (\Exception $e) {
                return false;
            }
        });

        $view->with('hasAdminUpdate', $hasUpdate);
    }
}
