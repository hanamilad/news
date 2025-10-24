<?php

namespace App\Models;

use App\Traits\AutoTranslatableAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Reel extends Model
{
    use SoftDeletes, BelongsToTenant, HasTranslations, AutoTranslatableAttributes;
    protected $fillable = [
        'description',
        'path',
        'type',
        'is_active',
        'user_id',
    ];
    public $translatable = ['description'];
    protected $casts = [
        'is_active' => 'boolean',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}



            // $table->id();
            // $table->json('description')->nullable();
            // $table->string('path')->nullable();
            // $table->enum('type', ['video', 'image'])->default('video'); 
            // $table->boolean('is_active')->default(true);
            // $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            // $table->string('tenant_id');
            // $table->timestamps();
            // $table->softDeletes();
            // $table->foreign('tenant_id')->references('id')->on('tenants')->onUpdate('cascade')->onDelete('cascade');