{{-- resources/views/components/news-list-section.blade.php --}}
@props([
    'newsList' => [
        ['id' => 1,  'tag' => '法會通知', 'tagClass' => 'notice',       'title' => '冬季祈福法會圓滿結束',     'description' => '感謝眾信眾參與本次冬季祈福法會。法會已圓滿結束，祈願眾信眾身體健康、闔家平安。本次法會共有百餘位信眾參與，場面莊嚴隆…', 'date' => '2024-12-08'],
        ['id' => 2,  'tag' => '節日慶典', 'tagClass' => 'festival',     'title' => '新年平安燈開放點燈登記',   'description' => '申辰年平安燈現已開放登記，歡迎信眾前來點燈祈福，祈求新年平安順遂。點燈期間自農曆正月初一至十二月三十日，功德無量。', 'date' => '2024-12-05'],
        ['id' => 3,  'tag' => '活動公告', 'tagClass' => 'announcement', 'title' => '歲末感恩祈福活動開始報名', 'description' => '歲末年終，本宮將舉辦感恩祈福活動，感謝神明庇佑、信眾護持。活動內容包含祈福儀式、平安餐會及結緣品發放，名額有限，請及…', 'date' => '2024-12-03'],
        ['id' => 4,  'tag' => '節日慶典', 'tagClass' => 'festival',     'title' => '中秋祭祖大典圓滿落幕',     'description' => '農曆八月十五中秋祭祖大典已圓滿結束，感謝眾信眾熱情參與。當日月圓人團圓，祭祀儀式莊嚴隆重，祈願闔家平安、萬事如意。', 'date' => '2024-11-28'],
        ['id' => 5,  'tag' => '法會通知', 'tagClass' => 'notice',       'title' => '每月初一十五誦經祈福',     'description' => '本宮每月農曆初一、十五定期舉行誦經祈福法會，歡迎信眾參與共修。法會時間為上午九時至十一時，現場備有茶點供應，功德回向十…', 'date' => '2024-12-01'],
        ['id' => 6,  'tag' => '活動公告', 'tagClass' => 'announcement', 'title' => '春季祈福法會通知',         'description' => '春季祈福法會將於下月舉行，歡迎信眾報名參加。法會將為信眾祈求身體健康、事業順利、闔家平安。報名請洽櫃台或電話預約。', 'date' => '2024-11-25'],
        ['id' => 7,  'tag' => '節日慶典', 'tagClass' => 'festival',     'title' => '端午節祈福活動圓滿',       'description' => '端午節祈福活動已圓滿結束，感謝眾信眾參與。活動當日進行祈福儀式、發放平安符及艾草，祈願眾信眾平安健康、諸事順遂。', 'date' => '2024-11-20'],
        ['id' => 8,  'tag' => '法會通知', 'tagClass' => 'notice',       'title' => '農曆七月普渡法會',         'description' => '農曆七月將舉行普渡法會，超渡孤魂、祈求平安。法會時間為農曆七月十五日，歡迎信眾參與共修，功德無量。', 'date' => '2024-11-15'],
        ['id' => 9,  'tag' => '活動公告', 'tagClass' => 'announcement', 'title' => '廟宇整修工程通知',         'description' => '本宮將進行外牆整修工程，預計施工期間為三個月。施工期間正常開放參拜，但請信眾注意安全，不便之處敬請見諒。', 'date' => '2024-11-10'],
        ['id' => 10, 'tag' => '節日慶典', 'tagClass' => 'festival',     'title' => '清明祭祖大典通知',         'description' => '清明節將至，本宮將舉辦祭祖大典。歡迎信眾攜家帶眷前來參拜，緬懷先祖恩德，祈求祖先庇佑後代子孫平安順遂。', 'date' => '2024-11-05'],
    ],
    'pageSize' => 5,
    'device'   => 'desktop',
])

@php
    $categories = [
        ['id' => 'all',          'name' => '全部'],
        ['id' => 'festival',     'name' => '節日慶典'],
        ['id' => 'notice',       'name' => '法會通知'],
        ['id' => 'announcement', 'name' => '活動公告'],
    ];

    // 篩選
    $selectedCategory = request('category', 'all');
    $filteredNews = $selectedCategory === 'all'
        ? $newsList
        : array_values(array_filter($newsList, fn($n) => ($n['tagClass'] ?? '') === $selectedCategory));

    // 分頁
    $total       = count($filteredNews);
    $totalPages  = $pageSize > 0 ? (int) ceil($total / $pageSize) : 1;
    $currentPage = max(1, min((int) request('page', 1), $totalPages));
    $offset      = ($currentPage - 1) * $pageSize;
    $pagedNews   = array_slice($filteredNews, $offset, $pageSize);

    // 頁碼（含省略號）
    $pageNumbers = [];
    if ($totalPages <= 7) {
        $pageNumbers = range(1, $totalPages);
    } elseif ($currentPage <= 4) {
        $pageNumbers = [1, 2, 3, 4, 5, '...', $totalPages];
    } elseif ($currentPage >= $totalPages - 3) {
        $pageNumbers = [1, '...', $totalPages-4, $totalPages-3, $totalPages-2, $totalPages-1, $totalPages];
    } else {
        $pageNumbers = [1, '...', $currentPage-1, $currentPage, $currentPage+1, '...', $totalPages];
    }

    $queryBase = array_filter(request()->except(['page', 'category']));
@endphp

<section class="news-list-section device-{{ $device }}">
    <div class="container">

        {{-- 分類 Tab --}}
        <div class="filter-bar">
            @foreach ($categories as $cat)
                <a
                    href="{{ url()->current() . '?' . http_build_query(array_merge($queryBase, ['category' => $cat['id'], 'page' => 1])) }}"
                    class="filter-btn {{ $selectedCategory === $cat['id'] ? 'active' : '' }}"
                >{{ $cat['name'] }}</a>
            @endforeach
        </div>

        {{-- 分隔線 --}}
        <hr class="divider" />

        {{-- 消息列表 --}}
        <div class="news-list">
            @foreach ($pagedNews as $news)
                <div class="news-item">
                    <div class="news-tag {{ $news['tagClass'] ?? '' }}">{{ $news['tag'] }}</div>
                    <div class="news-content">
                        <h3 class="news-title">{{ $news['title'] }}</h3>
                        <p class="news-description">{{ $news['description'] }}</p>
                    </div>
                    <div class="news-date">{{ $news['date'] }}</div>
                </div>
            @endforeach
        </div>

        {{-- 頁碼 --}}
        @if ($totalPages > 1)
            <div class="pagination">
                @if ($currentPage <= 1)
                    <span class="page-btn page-nav disabled">上一頁</span>
                @else
                    <a href="{{ url()->current() . '?' . http_build_query(array_merge($queryBase, ['category' => $selectedCategory, 'page' => $currentPage - 1])) }}"
                       class="page-btn page-nav">上一頁</a>
                @endif

                @foreach ($pageNumbers as $page)
                    @if ($page === '...')
                        <span class="page-ellipsis">...</span>
                    @else
                        <a href="{{ url()->current() . '?' . http_build_query(array_merge($queryBase, ['category' => $selectedCategory, 'page' => $page])) }}"
                           class="page-btn {{ $currentPage == $page ? 'active' : '' }}">{{ $page }}</a>
                    @endif
                @endforeach

                @if ($currentPage >= $totalPages)
                    <span class="page-btn page-nav disabled">下一頁</span>
                @else
                    <a href="{{ url()->current() . '?' . http_build_query(array_merge($queryBase, ['category' => $selectedCategory, 'page' => $currentPage + 1])) }}"
                       class="page-btn page-nav">下一頁</a>
                @endif
            </div>
        @endif

    </div>
</section>
