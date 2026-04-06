<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;

trait ImageUploadable
{
    /**
     * 画像ファイルを保存してリレーション経由で紐付ける
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model      紐付け先モデル
     * @param  UploadedFile[]                        $files      アップロードファイル配列
     * @param  string                                $directory  保存先ディレクトリ
     * @param  bool                                  $withSort   sort_order を保存するか
     */
    protected function storeImages($model, array $files, string $directory, bool $withSort = false): void
    {
        foreach ($files as $index => $file) {
            $path = $file->store($directory, 'public');
            $attributes = ['image_path' => '/storage/' . $path];
            if ($withSort) {
                $attributes['sort_order'] = $index;
            }
            $model->images()->create($attributes);
        }
    }
}
