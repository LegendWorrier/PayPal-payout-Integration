<!doctype html>
<html lang='{{ app()->getLocale() }}'>
    <head>
        <meta charset='utf-8'>
        <meta http-equiv='X-UA-Compatible' content='IE=edge'>
        <meta name='viewport' content='width=device-width, initial-scale=1'>
        <title>Spotify Settings</title>
        <!-- Fonts -->
        <link href='https://fonts.googleapis.com/css?family=Raleway:100,600' rel='stylesheet' type='text/css'>        
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">        
        <link rel="stylesheet" href="http://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css">
        <link rel="stylesheet" href="/css/semantic.min.css">
        <link rel="stylesheet" href="/css/dropdown.min.css">
        
    </head>
    <body>    
        <div class='content'>
            <div class='container'>                
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                </form>                
                <form action='{{route("add")}}' method='post'>
                    {{ csrf_field() }}
                    <div class='input-group'>
                        <input type='text' class='form-control' name='title' placeholder='Google Sheet Title' autocomplete='off'/>
                        <input type='text' class='form-control' name='playlist_id' placeholder='Playlist ID' autocomplete='off'/>
                        <button class='btn btn-success'>Add</button>                        
                    </div>
                </form>  
                <form action='{{route("update")}}' method='post'>
                    {{ csrf_field() }}
                    <div class='input-group'>
                        <input type='text' class='form-control' name='email' placeholder='Account Email' autocomplete='off' value='{{$detail->email}}' />
                        <input type='text' class='form-control' name='password' placeholder='Account Password' autocomplete='off' value='{{$detail->password}}'/>
                        <button class='btn btn-success'>Update</button>                        
                    </div>
                </form>          
                <div class='table-responsive table table-hover'>
                    <table id='dataTable' class='table table-hover'>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Playlist ID</th>
                                <th class='actions'>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                foreach($playlists as $key => $playlist) { ?>
                                    <tr>
                                        <td>{{$key + 1}}</td>
                                        <td>{{$playlist->title}}</td>
                                        <td>{{$playlist->playlist}}</td>
                                        <td><a href='/delete/{{$playlist->id}}'>Delete</a></td>
                                    </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </body>
    <script src="/js/semantic.min.js"></script>
    <script src="/js/dropdown.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        $(document).ready(function() {
            console.log('ssssssssssstart')
        })
    </script>
</html>
