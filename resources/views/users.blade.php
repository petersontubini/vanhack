<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="css/app.css">
        <script
            src="https://code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
            crossorigin="anonymous"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" >
        <script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
        <title>Vanhack</title>
    </head>
    <body>
        <div class="container">
            <div class="col-md-12">
                <img class="logo" src="https://www.7shifts.com/images/media-kit/logo-black.png">
            </div>
            <div class="col-md-12">
                <table id="table" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>User name</th>
                        <th>Total Hours</th>
                        <th>Total Overtime Hours</th>
                        <th>Photo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $collection)
                    <tr>
                        <td>{{ $collection['user']->firstName . " " . $collection['user']->lastName }}</td>    
                        <td>{{ number_format($collection['total_hours'],2) }}</td>
                        <td>{{ number_format($collection['total_overtime_hours'], 2) }}</td>
                        <td>
                            @if (isset($collection['user']->photo))
                                <img src="{{$collection['user']->photo}}">
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </table>
            </div>
        </div>
        
    </body>
    <script>
    $(document).ready( function () {
        $('#table').DataTable();
    } );
    </script>
</html>
