<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ $title }}</title>
<link rel="shortcut icon" href="{{ setting_asset_url('logo_url', asset('assets/images/logo/nexsell.svg')) }}" type="image/x-icon" />
@vite("resources/css/app.css")
