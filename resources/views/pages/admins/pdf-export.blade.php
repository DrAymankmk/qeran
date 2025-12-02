<!DOCTYPE html>
<html lang="{{app()->getLocale()}}" dir="{{app()->getLocale() == 'ar' ? 'rtl' : 'ltr'}}">
<head>
    <meta charset="UTF-8">
    <title>{{__('admin.admins')}} - {{date('Y-m-d')}}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            direction: {{app()->getLocale() == 'ar' ? 'rtl' : 'ltr'}};
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: {{app()->getLocale() == 'ar' ? 'right' : 'left'}};
        }
        th {
            background-color: #4CAF50;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{__('admin.admins')}}</h1>
        <p>{{__('admin.exported-on')}}: {{date('Y-m-d H:i:s')}}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>{{__('admin.id')}}</th>
                <th>{{__('admin.name')}}</th>
                <th>{{__('admin.email')}}</th>
                <th>{{__('admin.created_at')}}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($admins as $admin)
            <tr>
                <td>{{$admin->id}}</td>
                <td>{{$admin->name}}</td>
                <td>{{$admin->email}}</td>
                <td>{{Carbon\Carbon::parse($admin->created_at)->format('Y-m-d H:i')}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>







