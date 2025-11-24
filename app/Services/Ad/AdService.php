<?php

namespace App\Services\Ad;

use App\Repositories\Ad\AdRepository;
use App\Models\Ad;
use App\Traits\LogActivity;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class AdService
{
    use LogActivity;

    public function __construct(
        protected AdRepository $repo,
        protected \App\Services\Localization\TranslationService $translator
    ) {}

    public function create(array $input, $file): Ad
    {
        return DB::transaction(function () use ($input, $file) {
            $user = auth('api')->user();
            $input['user_id'] = $user->id;
            $input['title'] = $this->ensureEn($input['title'] ?? []);
            $input['image'] = $this->storeImage($file);
            $ad = $this->repo->create($input);
            $this->log($user->id, 'create', Ad::class, $ad->id, null, $ad->toArray());
            return $ad;
        });
    }

    public function update(int $id, array $input, $file): Ad
    {
        return DB::transaction(function () use ($id, $input, $file) {
            $user = auth('api')->user();
            $input['user_id'] = $user->id;
            $ad = $this->repo->findById($id);
            $old = $ad->toArray();
            $uploaded = $this->storeImage($file);
            if (!empty($uploaded)) {
                $originalPath = ltrim($ad->getRawOriginal('image'), '/');
                if ($ad->image && Storage::disk('spaces')->exists($originalPath)) {
                    Storage::disk('spaces')->delete($originalPath);
                }
                $input['image'] = $uploaded;
            }
            if (isset($input['title']) && is_array($input['title'])) {
                $input['title'] = $this->ensureEn($input['title']);
            }
            $updated = $this->repo->update($ad, $input);
            $this->log($user->id, 'update', Ad::class, $updated->id, $old, $updated->toArray());
            return $updated;
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $user = auth('api')->user();
            $ad = $this->repo->findById($id);
            $old = $ad->toArray();
            $originalPath = ltrim($ad->getRawOriginal('image'), '/');
            if ($ad->image && Storage::disk('spaces')->exists($originalPath)) {
                Storage::disk('spaces')->delete($originalPath);
            }
            $deleted = $this->repo->delete($ad);
            $this->log($user->id, 'delete', Ad::class, $ad->id, $old, null);
            return $deleted;
        });
    }

    public function changeStatus(int $id, bool $isActive): Ad
    {
        return DB::transaction(function () use ($id, $isActive) {
            $user = auth('api')->user();
            $ad = $this->repo->findById($id);
            $old = $ad->toArray();
            $updated = $this->repo->update($ad, ['is_active' => $isActive]);
            $this->log($user->id, 'change_status', Ad::class, $updated->id, $old, $updated->toArray());
            return $updated;
        });
    }

    protected function storeImage($file)
    {
        $path = "";
        if ($file instanceof UploadedFile && $file->isValid()) {
            $path = $file->store('ads_images', ['disk' => 'spaces']);
        }
        return $path;
    }

    protected function ensureEn(array $trans): array
    {
        $ar = $trans['ar'] ?? null;
        $en = $trans['en'] ?? null;
        if (!$en && $ar) {
            $trans['en'] = $this->translator->translateOrFallback($ar, 'ar', 'en');
        }
        return $trans;
    }
}