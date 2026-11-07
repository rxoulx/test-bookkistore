<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $book->title }} — Book Store</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; color: #292929; background: white; font-family: Arial, sans-serif; }
        a { color: inherit; text-decoration: none; }
        .header { border-bottom: 1px solid #eceae7; background: white; }
        .header-inner {
            max-width: 1180px; min-height: 74px; margin: auto; padding: 12px 22px;
            display: flex; align-items: center; justify-content: space-between; gap: 20px;
        }
        .brand { display: flex; align-items: center; gap: 10px; font-size: 19px; font-weight: bold; letter-spacing: 2px; }
        .brand-mark { color: #ef6a32; font-size: 38px; line-height: 1; }
        .nav { display: flex; align-items: center; gap: 30px; font-size: 14px; letter-spacing: 1px; }
        .nav a:hover { color: #ef6a32; }
        .add-link { border: 1px solid #ef6a32; color: #ef6a32; padding: 10px 14px; }

        .page-banner { background: #faf9f7; padding: 35px 20px; text-align: center; }
        .page-banner h1 {
            margin: 0 0 12px; font: normal 34px/1.25 Georgia, serif;
            overflow-wrap: anywhere;
        }
        .breadcrumb { color: #777; font-size: 13px; }
        .breadcrumb a:hover { color: #ef6a32; }
        .breadcrumb span { color: #ef6a32; }

        .detail {
            max-width: 1080px; margin: 55px auto; padding: 0 22px;
            display: grid; grid-template-columns: 48% 52%; gap: 35px;
        }
        .cover-panel {
            min-height: 490px; background: #faf9f7;
            display: flex; align-items: center; justify-content: center;
            border: 1px solid #eeeae6;
        }
        .book-cover {
            width: 245px; height: 365px; padding: 32px 23px;
            display: flex; flex-direction: column; justify-content: space-between;
            overflow: hidden; color: white;
            box-shadow: 11px 17px 18px #00000030;
            border-left: 11px solid #00000027;
        }
        .cover-1 { background: linear-gradient(150deg, #233e4e, #101d27); }
        .cover-2 { background: linear-gradient(150deg, #b18454, #684a34); }
        .cover-3 { background: linear-gradient(150deg, #798876, #334d44); }
        .cover-4 { background: linear-gradient(150deg, #a45d56, #522e39); }
        .cover-small, .cover-author { font-size: 12px; text-transform: uppercase; letter-spacing: 2px; }
        .cover-title {
            font: normal 32px/1.2 Georgia, serif;
            overflow-wrap: anywhere; max-height: 225px; overflow: hidden;
        }

        .info { padding: 12px 10px; }
        .info h2 { margin: 0 0 15px; font: normal 30px/1.25 Georgia, serif; }
        .author { margin: 0 0 23px; color: #777; }
        .price { margin: 0 0 27px; color: #f05a28; font-size: 21px; font-weight: bold; }
        .description { color: #595959; font: 17px/1.7 Georgia, serif; white-space: pre-line; }
        .stock { margin: 28px 0; color: #555; font-size: 14px; }
        .actions { display: flex; flex-wrap: wrap; align-items: center; gap: 12px; }
        .button {
            display: inline-block; padding: 13px 21px; border: 1px solid #ef6a32;
            background: #ef6a32; color: white; font: bold 14px Arial, sans-serif;
            cursor: pointer;
        }
        .button:hover { background: #d95722; }
        .button-outline { background: white; color: #ef6a32; }
        .button-outline:hover { background: #fff2eb; }
        .button-delete { border-color: #b94c40; background: white; color: #b94c40; }
        .button-delete:hover { background: #fff0ee; }
        .actions form { margin: 0; }

        @media (max-width: 750px) {
            .detail { grid-template-columns: 1fr; margin-top: 25px; gap: 22px; }
            .cover-panel { min-height: 420px; }
            .header-inner { align-items: flex-start; flex-direction: column; }
            .nav { gap: 15px; flex-wrap: wrap; }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-inner">
            <a class="brand" href="{{ route('books.index') }}">
                <span class="brand-mark">K</span><span>BOOK STORE</span>
            </a>
            <nav class="nav">
                <a href="{{ route('books.index') }}">SÁCH</a>
                <a class="add-link" href="{{ route('books.create') }}">+ THÊM SÁCH</a>
            </nav>
        </div>
    </header>

    <section class="page-banner">
        <h1>{{ $book->title }}</h1>
        <div class="breadcrumb">
            <a href="{{ route('books.index') }}">Trang chủ</a>
            &nbsp;›&nbsp; <span>{{ $book->title }}</span>
        </div>
    </section>

    <main class="detail">
        <div class="cover-panel">
            <div class="book-cover cover-{{ (($book->id - 1) % 4) + 1 }}">
                <span class="cover-small">Book Store</span>
                <span class="cover-title">{{ $book->title }}</span>
                <span class="cover-author">{{ $book->author }}</span>
            </div>
        </div>

        <div class="info">
            <h2>{{ $book->title }}</h2>
            <p class="author">Tác giả: {{ $book->author }}</p>
            <p class="price">{{ number_format($book->price, 0, ',', '.') }} ₫</p>
            <div class="description">{{ $book->description ?: 'Chưa có mô tả cho cuốn sách này.' }}</div>
            <p class="stock">Tồn kho: <strong>{{ $book->stock }}</strong> cuốn</p>

            <div class="actions">
                <a class="button" href="{{ route('books.edit', $book) }}">Sửa sách</a>
                <a class="button button-outline" href="{{ route('books.index') }}">Về danh sách</a>
                <form method="POST" action="{{ route('books.destroy', $book) }}">
                    @csrf
                    @method('DELETE')
                    <button class="button button-delete"
                            onclick="return confirm('Bạn chắc chắn muốn xóa sách này?')">
                        Xóa sách
                    </button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
