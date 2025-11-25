<?php

namespace App\Services\Podcast;

use App\Models\Podcast;
use App\Repositories\Podcast\PodcastRepository;
use App\Traits\LogActivity;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PodcastService
{
    use LogActivity;

    public function __construct(
        protected PodcastRepository $repo,
        protected \App\Services\Localization\TranslationService $translator
    ) {}

    public function create(array $input, $file): Podcast
    {
        return DB::transaction(function () use ($input, $file) {
            $user = auth('api')->user();
            $input['user_id'] = $user->id;
            $input['title'] = $this->ensureEn($input['title'] ?? []);
            $input['host_name'] = $this->ensureEn($input['host_name'] ?? []);
            $input['description'] = $this->ensureEn($input['description'] ?? []);
            $input['audio_path'] = $this->storeAudioPath($file);
            $podcast = $this->repo->create($input);
            $this->log($user->id, 'اضافة', Podcast::class, $podcast->id, null, $podcast->toArray());

            return $podcast;
        });
    }

    public function update(int $id, array $input, $file): Podcast
    {
        return DB::transaction(function () use ($id, $input, $file) {
            $user = auth('api')->user();
            $input['user_id'] = $user->id;
            $podcast = $this->repo->findById($id);
            $old = $podcast->toArray();
            $uploaded = $this->storeAudioPath($file);
            if (! empty($uploaded)) {
                $originalPath = ltrim($podcast->getRawOriginal('audio_path'), '/');
                if ($podcast->audio_path && Storage::disk('spaces')->exists($originalPath)) {
                    Storage::disk('spaces')->delete($originalPath);
                }

                $input['audio_path'] = $uploaded;
            }
            foreach (['title', 'host_name', 'description'] as $field) {
                if (isset($input[$field]) && is_array($input[$field])) {
                    $input[$field] = $this->ensureEn($input[$field]);
                }
            }
            $updated = $this->repo->update($podcast, $input);
            $this->log($user->id, 'تعديل', Podcast::class, $updated->id, $old, $updated->toArray());

            return $updated;
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $user = auth('api')->user();
            $podcast = $this->repo->findById($id);
            $old = $podcast->toArray();
            $originalPath = ltrim($podcast->getRawOriginal('audio_path'), '/');
            if ($podcast->audio_path && Storage::disk('spaces')->exists($originalPath)) {
                Storage::disk('spaces')->delete($originalPath);
            }
            $deleted = $this->repo->delete($podcast);
            $this->log($user->id, 'حذف', Podcast::class, $podcast->id, $old, null);

            return $deleted;
        });
    }

    protected function storeAudioPath($file)
    {
        $path = '';
        if ($file instanceof UploadedFile && $file->isValid()) {
            $path = $file->store('podcast', ['disk' => 'spaces']);
        }

        return $path;
    }

    protected function ensureEn(array $trans): array
    {
        $ar = $trans['ar'] ?? null;
        $en = $trans['en'] ?? null;
        if (! $en && $ar) {
            $trans['en'] = $this->translator->translateOrFallback($ar, 'ar', 'en');
        }

        return $trans;
    }
}
