<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('translations.manage-translations') }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        @if($locale === 'ar')
        body { direction: rtl; text-align: right; }
        @else
        body { direction: ltr; text-align: left; }
        @endif

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 12px;
            font-size: 18px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            vertical-align: top;
            word-break: break-word;
        }

        @if($locale === 'ar')
        th, td { text-align: right; }
        @else
        th, td { text-align: left; }
        @endif

        th {
            background-color: #f2f2f2;
            font-weight: bold;
            color: #333;
        }

        .text-center { text-align: center; }
    </style>
</head>
<body>
    <h1>{{ __('translations.manage-translations') }}</h1>

    <table>
        <thead>
            <tr>
                <th class="text-center">#</th>
                <th>{{ __('translations.key') }}</th>
                <th>{{ __('translations.value') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($translations as $t)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $t['key'] }}</td>
                    <td>{{ $t['value'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

