<?php

namespace App\Services\Podcast;

use App\Repositories\Podcast\PodcastRepository;
use App\Models\Podcast;
use App\Traits\LogActivity;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class PodcastService
{
    use LogActivity;
    public function __construct(protected PodcastRepository $repo) {}
    public function create(array $input,  $file): Podcast
    {
        return DB::transaction(function () use ($input, $file) {
            $input['audio_path'] = $this->storeAudioPath($file);
            $podcast = $this->repo->create($input);
            $this->log($podcast->user_id, 'create', Podcast::class, $podcast->id, null, $podcast->toArray());
            return $podcast;
        });
    }

    public function update(int $id, array $input, $file): Podcast
    {
        return DB::transaction(function () use ($id, $input, $file) {
            $podcast = $this->repo->findById($id);
            $old = $podcast->toArray();
            $uploaded = $this->storeAudioPath($file);
            if (!empty($uploaded)) {
                if ($podcast->audio_path &&Storage::disk('public')->exists($podcast->audio_path)) {
                    Storage::disk('public')->delete($podcast->audio_path);
                }
                $input['audio_path'] = $uploaded;
            }
            $updated = $this->repo->update($podcast, $input);
            $this->log( request()->user()->id ?? $updated->user_id, 'update', Podcast::class, $updated->id, $old, $updated->toArray());
            return $updated;
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $podcast = $this->repo->findById($id);
            $old = $podcast->toArray();
            if ($podcast->audio_path && Storage::disk('public')->exists($podcast->audio_path)) {
                Storage::disk('public')->delete($podcast->audio_path);
            }
            $deleted = $this->repo->delete($podcast);
            $this->log(request()->user()->id ?? $podcast->user_id, 'delete', Podcast::class, $podcast->id, $old, null);
            return $deleted;
        });
    }

    protected function storeAudioPath($file)
    {
        $path = "";
        if ($file instanceof UploadedFile && $file->isValid()) {
            $path = $file->store('podcast', ['disk' => 'public']);
        }
        return $path;
    }
}
