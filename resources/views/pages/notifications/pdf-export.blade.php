<!DOCTYPE html>
<html lang="{{app()->getLocale()}}" dir="{{app()->getLocale() == 'ar' ? 'rtl' : 'ltr'}}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{__('admin.categories')}}</title>
    <style>
        @if(app()->getLocale() == 'ar')
        body {
            direction: rtl;
            text-align: right;
        }
        @else
        body {
            direction: ltr;
            text-align: left;
        }
        @endif
        
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
            font-size: 24px;
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
            background-color: #f2f2f2;
            font-weight: bold;
            color: #333;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .text-center {
            text-align: center;
        }
        
        .header-info {
            margin-bottom: 20px;
            text-align: {{app()->getLocale() == 'ar' ? 'right' : 'left'}};
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <h1>{{__('admin.notifications')}}</h1>
    
    <div class="header-info">
        <p>{{__('admin.created_at')}}: {{Carbon\Carbon::now()->locale(app()->getLocale())->translatedFormat('Y-m-d G:i')}}</p>
    </div>
    
    <table>
        <thead>
            <tr>
             <th>{{__('admin.id')}}</th>
								<th>
									{{__('admin.title-text')}}
								</th>
								<th>
									{{__('admin.description')}}
								</th>
								<th>
									{{__('admin.created_at')}}
								</th>
            </tr>
        </thead>
        <tbody>
            @foreach($notifications as $notification)
            <tr>
              <td>{{$notification->id}} </td>

								<td>{{$notification->title}}</td>
								<td>{{$notification->description}}</td>

								
                <td>{{$notification->created_at}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

