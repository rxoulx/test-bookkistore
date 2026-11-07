<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Book Store — Tủ sách</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            color: #292929;
            background: #faf9f7;
            font-family: Arial, sans-serif;
        }
        a { color: inherit; text-decoration: none; }

        .site-header {
            background: white;
            border-bottom: 1px solid #eceae7;
        }
        .header-inner {
            max-width: 1180px;
            min-height: 74px;
            margin: auto;
            padding: 12px 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 19px;
            font-weight: bold;
            letter-spacing: 2px;
        }
        .brand-mark {
            color: #ef6a32;
            font-size: 38px;
            line-height: 1;
        }
        .nav {
            display: flex;
            align-items: center;
            gap: 30px;
            font-size: 14px;
            letter-spacing: 1px;
        }
        .nav a:hover { color: #ef6a32; }
        .add-link {
            border: 1px solid #ef6a32;
            color: #ef6a32;
            padding: 10px 14px;
        }
        .add-link:hover { background: #fff2eb; }

        main {
            max-width: 1180px;
            margin: 0 auto;
            padding: 36px 22px 70px;
        }
        .page-top {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 25px;
        }
        .eyebrow {
            margin: 0 0 8px;
            color: #ef6a32;
            font-size: 12px;
            font-weight: bold;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        h1 {
            margin: 0;
            font-family: Georgia, serif;
            font-size: 34px;
            font-weight: normal;
        }
        .count { color: #777; font-size: 14px; }
        .notice {
            padding: 12px 15px;
            margin-bottom: 22px;
            color: #275d37;
            background: #e9f5ec;
            border: 1px solid #c7e4ce;
        }

        .book-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 27px;
        }
        .book-card {
            background: white;
            border: 1px solid #e9e7e3;
            min-width: 0;
        }
        .cover-area {
            height: 315px;
            padding: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            border-bottom: 1px solid #efeeeb;
        }
        .book-cover {
            position: relative;
            width: 175px;
            height: 255px;
            padding: 25px 17px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
            color: white;
            box-shadow: 8px 12px 13px #00000024;
            border-left: 8px solid #00000027;
        }
        .book-cover::after {
            content: "";
            position: absolute;
            width: 170px;
            height: 170px;
            right: -65px;
            bottom: -80px;
            border: 1px solid #ffffff55;
            border-radius: 50%;
        }
        .cover-1 { background: linear-gradient(150deg, #233e4e, #101d27); }
        .cover-2 { background: linear-gradient(150deg, #b18454, #684a34); }
        .cover-3 { background: linear-gradient(150deg, #798876, #334d44); }
        .cover-4 { background: linear-gradient(150deg, #a45d56, #522e39); }
        .cover-small {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
            opacity: .85;
        }
        .cover-title {
            position: relative;
            z-index: 1;
            font-family: Georgia, serif;
            font-size: 23px;
            line-height: 1.2;
            overflow-wrap: anywhere;
            max-height: 145px;
            overflow: hidden;
        }
        .cover-author {
            position: relative;
            z-index: 1;
            font-size: 11px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .card-body { padding: 15px 13px 17px; }
        .book-title {
            display: block;
            min-height: 42px;
            font-family: Georgia, serif;
            font-size: 17px;
            line-height: 1.35;
        }
        .book-title:hover { color: #ef6a32; }
        .author {
            margin: 7px 0 12px;
            color: #777;
            font-size: 13px;
        }
        .price {
            margin: 0 0 14px;
            color: #f05a28;
            font-weight: bold;
            font-size: 16px;
        }
        .actions {
            display: flex;
            align-items: center;
            gap: 13px;
            padding-top: 12px;
            border-top: 1px solid #eee;
            font-size: 13px;
        }
        .actions a:hover { color: #ef6a32; }
        .actions form { margin-left: auto; }
        .delete {
            padding: 0;
            border: 0;
            background: none;
            color: #a54438;
            cursor: pointer;
            font: inherit;
        }
        .empty {
            padding: 50px;
            background: white;
            border: 1px solid #e9e7e3;
            text-align: center;
            color: #777;
        }
        @media (max-width: 900px) {
            .book-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 550px) {
            .header-inner { align-items: flex-start; flex-direction: column; }
            .nav { gap: 15px; flex-wrap: wrap; }
            .book-grid { grid-template-columns: 1fr; }
            .cover-area { height: 290px; }
        }
    </style>
</head>
<body>
    <header class="site-header">
        <div class="header-inner">
            <a class="brand" href="{{ route('books.index') }}">
                <span class="brand-mark">K</span>
                <span>BOOK STORE</span>
            </a>
            <nav class="nav">
                <a href="{{ route('books.index') }}">SÁCH</a>
                <a class="add-link" href="{{ route('books.create') }}">+ THÊM SÁCH</a>
            </nav>
        </div>
    </header>

    <main>
        <div class="page-top">
            <div>
                <p class="eyebrow">Tủ sách của bạn</p>
                <h1>Danh sách sách</h1>
            </div>
            <span class="count">{{ $books->count() }} cuốn sách</span>
        </div>

        @if (session('ok'))
            <div class="notice">{{ session('ok') }}</div>
        @endif

        @if ($books->isEmpty())
            <div class="empty">
                Chưa có sách. <a href="{{ route('books.create') }}">Thêm cuốn đầu tiên</a>
            </div>
        @else
            <div class="book-grid">
                @foreach ($books as $book)
                    <article class="book-card">
                        <a href="{{ route('books.show', $book) }}" class="cover-area">
                            <div class="book-cover cover-{{ (($book->id - 1) % 4) + 1 }}">
                                <span class="cover-small">Book Store</span>
                                <span class="cover-title">{{ $book->title }}</span>
                                <span class="cover-author">{{ $book->author }}</span>
                            </div>
                        </a>
                        <div class="card-body">
                            <a class="book-title" href="{{ route('books.show', $book) }}">
                                {{ $book->title }}
                            </a>
                            <p class="author">{{ $book->author }}</p>
                            <p class="price">{{ number_format($book->price, 0, ',', '.') }} ₫</p>
                            <div class="actions">
                                <a href="{{ route('books.show', $book) }}">Xem</a>
                                <a href="{{ route('books.edit', $book) }}">Sửa</a>
                                <form method="POST" action="{{ route('books.destroy', $book) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="delete"
                                            onclick="return confirm('Xóa sách này?')">
                                        Xóa
                                    </button>
                                </form>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </main>
</body>
</html>
