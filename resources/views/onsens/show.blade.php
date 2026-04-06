<x-app-layout bg="images/washibg.jpg">
    <!-- 投稿完了メッセージ -->
    @if(session('success'))
        <x-alert-modal title="投稿完了">
            {{ session('success') }}
        </x-alert-modal>
    @endif

    @if($errors->any())
        <x-alert-modal title="投稿できませんでした">
            <ul class="text-sm text-gray-700 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-alert-modal>
    @endif
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold leading-tight">
                {{ $onsen->name }}
            </h2>
        </div>
    </x-slot>
    <div class="bg-[#F0F3EA] bg-center bg-white/60 shadow-xl py-2 backdrop-blur-sm shadow-xl py-2">
        <div class="max-w-6xl mx-auto px-4 md:px-6">
            <div class="max-w-5xl mx-auto py-6 md:py-10 space-y-6 md:space-y-10">

                <!-- 画像と基本情報 -->
                <div class="grid md:grid-cols-2 gap-6 md:gap-8 items-start">

                    @if($onsen->images->count())
                        <div class="space-y-4 min-w-0 overflow-hidden">
                            <div class="swiper overflow-hidden shadow w-full">
                                <div class="swiper-wrapper">
                                    @foreach($onsen->images as $image)
                                        <div class="swiper-slide">
                                            <img src="{{ $image->image_path }}"
                                                class="w-full h-[250px] md:h-[470px] object-cover cursor-pointer rounded-2xl"
                                                onclick="openModal('{{ $image->image_path }}')">
                                        </div>
                                    @endforeach
                                </div>
                                <div class="swiper-pagination"></div>
                            </div>
                        </div>
                    @endif

                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <h2 class="font-semibold text-2xl ml-">
                                {{ $onsen->name }}
                            </h2>
                            @auth
                                <button type="button" class="like-btn text-2xl" data-onsen-id="{{ $onsen->id }}">
                                    <span class="heart">
                                        {{ auth()->user()?->likedOnsens->contains($onsen->id) ? '❤️' : '🤍' }}
                                    </span>
                                </button>
                            @else
                                <a href="{{ route('login') }}" class="text-gray-400 text-sm">
                                    🤍 ログインしていいね
                                </a>
                            @endauth
                        </div>
                        <img src="{{ asset('images/kasen.png') }}" class="w-full h-auto rounded-lg opacity-[0.5]"
                            alt="筆下線">

                        <!-- 星評価 -->
                        <x-star-rating :rating="round($onsen->reviews_avg_rating ?? 0, 1)" />

                        <p class="text-gray-700 leading-relaxed">
                            {{ $onsen->description }}
                        </p>

                        <div class="text-sm text-gray-500">
                            📍{{ $onsen->address }}
                        </div>
                        <div class="rounded-2xl overflow-hidden shadow-lg">
                            <div id="map" class="w-full h-[200px]"></div>
                        </div>

                    </div>

                </div>

                <!-- レビュー一覧 -->
                <div class="space-y-3">
                    <div
                        class="flex items-center gap-3 bg-[#E6FBD0] border-l-4 border-[#2F4F2F] px-4 py-2 text-lg font-semibold mb-6">
                        <h3 class="text-xl font-semibold">みんなのレビュー</h3>
                        <p class="text-sm text-gray-500">
                            (全{{ $onsen->reviews_count }}件)
                        </p>
                    </div>
                    @forelse ($reviews as $review)
                        <div class="bg-white  shadow-lg p-5 space-y-2">

                            <x-star-rating :rating="$review->rating" />

                            @if($review->images->count())
                                <div class="flex gap-2 overflow-x-auto">
                                    @foreach($review->images as $image)
                                        <img src="{{ $image->image_path }}"
                                            class="w-28 h-28 object-cover rounded-lg cursor-pointer hover:scale-105 transition"
                                            onclick="openModal('{{ $image->image_path }}')">
                                    @endforeach
                                </div>
                            @endif

                            <p class="text-gray-700">
                                {{ $review->comment }}
                            </p>
                            <div class="flex flex-wrap items-center gap-2 md:gap-3 text-xs text-gray-400">
                                <img src="{{ $review->user->icon_url }}" class="w-8 h-8 rounded-full object-cover">

                                <p>
                                    投稿者：{{ $review->user->name ?? '匿名' }}
                                </p>
                                <p>
                                    {{ $review->posted_date }}
                                </p>
                            </div>
                        </div>

                    @empty
                        <p class="text-gray-500">まだレビューがありません</p>
                    @endforelse
                </div>
                <div class="flex justify-center gap-4 mt-6">

                    @if ($reviews->onFirstPage() === false)
                        <a href="{{ $reviews->previousPageUrl() }}"
                            class="px-4 py-2 bg-[#E6FBD0] rounded hover:bg-[#D9F5BE]">
                            ← 前へ
                        </a>
                    @endif

                    @if ($reviews->hasMorePages())
                        <a href="{{ $reviews->nextPageUrl() }}" class="px-4 py-2 bg-[#E6FBD0] rounded hover:bg-[#D9F5BE]">
                            次へ →
                        </a>
                    @endif

                </div>
                <!-- レビュー投稿 -->
                @auth
                    <div class="bg-red-50/50 backdrop-blur-sm border-2 border-[#F59E0B] shadow-sm p-4">
                        <h3 class="text-lg text-[#F59E0B] font-semibold">レビュー投稿</h3>

                        <form method="POST" action="{{ route('reviews.store', $onsen->id) }}" enctype="multipart/form-data"
                            class="space-y-2">
                            @csrf

                            <div>
                                <input type="hidden" name="rating" id="ratingInput" value="{{ old('rating') }}">

                                <div id="starRating" class="flex gap-1 text-3xl cursor-pointer">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <span data-value="{{ $i }}" class="star text-gray-300">★</span>
                                    @endfor
                                </div>
                                @error('rating')
                                    <p class="text-red-500 text-sm">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <div class="space-y-2">
                                    <label class="block font-medium">
                                        コメント(任意)
                                    </label>
                                    <!-- コメント入力 -->
                                    <textarea name="comment"
                                        class=" w-full border border-gray-300 rounded-lg p-2
                                                                                    focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400">
                                                                                    </textarea>
                                    <!-- 画像アップロード -->
                                    <div class="flex items-center gap-3">

                                        <input type="file" id="images" name="images[]" multiple accept="image/*"
                                            class="hidden">

                                        <label for="images" class="cursor-pointer text-gray-600 hover:text-orange-500">
                                            <x-icon.photo class="w-8 h-8" />
                                        </label>

                                        <span class="text-sm text-gray-500">
                                            写真を追加
                                        </span>

                                    </div>

                                    @error('images.*')
                                        <p class="text-red-500 text-sm">
                                            {{ $message }}
                                        </p>
                                    @enderror

                                </div>

                                <div class="flex items-center justify-center">
                                    <button class="px-6 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg">
                                        投稿する
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                @else
                    <p class="text-center text-gray-500">
                        レビューを書くには <a href="/login" class="text-blue-500 underline">ログイン</a> してください
                    </p>
                @endauth

            </div>
        </div>
    </div>

    <!-- 画像モーダル -->
    <div id="imageModal" class="fixed inset-0 bg-black/70 flex items-center justify-center hidden z-50">
        <span class="absolute top-4 right-6 text-white text-4xl cursor-pointer" onclick="closeModal()">&times;</span>
        <img id="modalImg" src="" class="max-h-[90%] max-w-[90%] rounded-xl shadow-xl">
    </div>

    <script>
        // 星評価
        document.querySelectorAll('#starRating .star').forEach(star => {
            star.addEventListener('click', function () {
                let value = this.dataset.value;
                document.getElementById('ratingInput').value = value;

                document.querySelectorAll('#starRating .star').forEach(s => {
                    s.classList.toggle('text-amber-400', s.dataset.value <= value);
                    s.classList.toggle('text-gray-300', s.dataset.value > value);
                });
            });
        });


        // Swiper（スライドが2枚以上の時だけloopを有効にする）
        document.addEventListener('DOMContentLoaded', function () {
            const slides = document.querySelectorAll('.swiper-slide');
            if (slides.length === 0) return;
            new Swiper('.swiper', {
                loop: slides.length > 1,
                pagination: { el: '.swiper-pagination', clickable: true },
            });
        });

        // モーダル
        function openModal(src) {
            const modal = document.getElementById('imageModal');
            const modalImg = document.getElementById('modalImg');
            modalImg.src = src;
            modal.classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('imageModal').classList.add('hidden');
        }
        //Googleマップ
        function initMap() {
            const lat = {{ $onsen->latitude ?? 'null' }};
            const lng = {{ $onsen->longitude ?? 'null' }};
            if (lat === null || lng === null) return;

            const location = { lat, lng };

            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 15,
                center: location,
                gestureHandling: 'cooperative',
                disableDefaultUI: true,
                zoomControl: true,
            });

            new google.maps.Marker({
                position: location,
                map: map,
            });
        }

    </script>

    @if($onsen->latitude && $onsen->longitude)
        <script async
            src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&callback=initMap&loading=async">
            </script>
    @endif
</x-app-layout>