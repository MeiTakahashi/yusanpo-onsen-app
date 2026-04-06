<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Onsen;
use Illuminate\Support\Facades\Auth;
use App\Services\NgWordService;
use App\Models\ReviewImage;
use App\Traits\ImageUploadable;

class ReviewController extends Controller
{
    use ImageUploadable;
    private function authorizeReview(Review $review): void
    {
        abort_if($review->user_id !== auth()->id(), 403);
    }

    public function edit(Review $review)
    {
        $this->authorizeReview($review);

        return view('reviews.edit', compact('review'));
    }
    //投稿フォーム表示
    public function create($onsenId)
    {
        $onsen = Onsen::findOrFail($onsenId);
        return view('reviews.create', compact('onsen'));
    }
    // 投稿保存
    public function store(Request $request, $onsenId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ] ,[
            'rating.required' => '評価は必須です。⭐️を選んでください。',
        ]);
        // NGワードチェック
        $comment = $request->comment
            ? NgWordService::mask($request->comment)
            : null;
        $review = Review::create([
            'onsen_id' => $onsenId,
            'user_id' => Auth::id(),
            'rating' => $request->rating,
            'comment' => $comment,
        ]);

        //画像保存
        if ($request->hasFile('images')) {
            $this->storeImages($review, $request->file('images'), 'review_images', true);
        }

        return redirect()->route('onsens.show', $onsenId)->with('success', 'レビューを投稿しました');
    }
    //編集
    public function update(Request $request, Review $review)
    {
        $this->authorizeReview($review);

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'delete_images' => 'array',
            'delete_images.*' => 'integer|exists:review_images,id',
        ]);
        //NGワードマスク
        $validated['comment'] = NgWordService::mask($validated['comment']);
        $review->update($validated);
        //画像削除
        if ($request->filled('delete_images')) {
            ReviewImage::whereIn('id', $request->delete_images)->delete();
        }
        // 新しい画像追加
        if ($request->hasFile('images')) {
            $this->storeImages($review, $request->file('images'), 'review_images', true);
        }

        return redirect()
            ->route('mypage')
            ->with('success', 'レビューを更新しました');
    }
    //削除
    public function destroy(Review $review)
    {
        $this->authorizeReview($review);

        $review->delete();

        return redirect()
            ->route('mypage')
            ->with('success', 'レビューを削除しました');
    }

}