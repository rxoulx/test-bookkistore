<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $book->exists ? 'Sửa sách' : 'Thêm sách' }} — Book Store</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; color: #292929; background: #faf9f7; font-family: Arial, sans-serif; }
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

        .page-banner { background: #f5f3f0; padding: 32px 20px; text-align: center; }
        .page-banner h1 { margin: 0 0 12px; font: normal 34px Georgia, serif; }
        .breadcrumb { color: #777; font-size: 13px; }
        .breadcrumb a:hover { color: #ef6a32; }

        .form-card {
            max-width: 760px; margin: 40px auto 70px; padding: 35px;
            background: white; border: 1px solid #e9e7e3;
        }
        .form-card h2 { margin: 0 0 8px; font: normal 27px Georgia, serif; }
        .hint { margin: 0 0 26px; color: #777; font-size: 14px; }
        .field { margin-bottom: 20px; }
        .field label { display: block; margin-bottom: 8px; font-size: 14px; font-weight: bold; }
        .field input, .field textarea {
            width: 100%; padding: 12px 13px; border: 1px solid #d8d5d0;
            border-radius: 0; background: white; color: #292929;
            font: 15px Arial, sans-serif;
        }
        .field input:focus, .field textarea:focus {
            outline: 1px solid #ef6a32; border-color: #ef6a32;
        }
        .field textarea { min-height: 125px; resize: vertical; }
        .row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .errors {
            margin: 0 0 25px; padding: 15px 20px;
            color: #a33c31; background: #fff1ef; border: 1px solid #e9b8b1;
        }
        .errors ul { margin: 8px 0 0; padding-left: 20px; }
        .buttons { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 28px; }
        .button {
            padding: 13px 22px; border: 1px solid #ef6a32; background: #ef6a32;
            color: white; font: bold 14px Arial, sans-serif; cursor: pointer;
        }
        .button:hover { background: #d95722; }
        .button-outline { display: inline-block; background: white; color: #ef6a32; }
        .button-outline:hover { background: #fff2eb; }

        @media (max-width: 600px) {
            .header-inner { align-items: flex-start; flex-direction: column; }
            .nav { gap: 15px; flex-wrap: wrap; }
            .form-card { margin: 20px 15px; padding: 22px; }
            .row { grid-template-columns: 1fr; gap: 0; }
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
        <h1>{{ $book->exists ? 'Sửa sách' : 'Thêm sách mới' }}</h1>
        <div class="breadcrumb">
            <a href="{{ route('books.index') }}">Trang chủ</a>
            &nbsp;›&nbsp; {{ $book->exists ? 'Sửa sách' : 'Thêm sách' }}
        </div>
    </section>

    <main class="form-card">
        <h2>{{ $book->exists ? 'Cập nhật thông tin' : 'Thông tin cuốn sách' }}</h2>
        <p class="hint">Điền thông tin bên dưới rồi bấm Lưu sách.</p>

        @if ($errors->any())
            <div class="errors">
                Vui lòng kiểm tra lại:
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST"
              action="{{ $book->exists ? route('books.update', $book) : route('books.store') }}">
            @csrf
            @if ($book->exists)
                @method('PUT')
            @endif

            <div class="field">
                <label for="title">Tên sách</label>
                <input id="title" name="title" required
                       value="{{ old('title', $book->title) }}">
            </div>

            <div class="field">
                <label for="author">Tác giả</label>
                <input id="author" name="author" required
                       value="{{ old('author', $book->author) }}">
            </div>

            <div class="row">
                <div class="field">
                    <label for="price">Giá (₫)</label>
                    <input id="price" name="price" type="number" step="0.01"
                           min="0" required value="{{ old('price', $book->price) }}">
                </div>
                <div class="field">
                    <label for="stock">Tồn kho</label>
                    <input id="stock" name="stock" type="number" min="0"
                           required value="{{ old('stock', $book->stock) }}">
                </div>
            </div>

            <div class="field">
                <label for="description">Mô tả</label>
                <textarea id="description" name="description">{{ old('description', $book->description) }}</textarea>
            </div>

            <div class="buttons">
                <button class="button" type="submit">Lưu sách</button>
                <a class="button button-outline" href="{{ route('books.index') }}">Hủy</a>
            </div>
        </form>
    </main>
</body>
</html>
