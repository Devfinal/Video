<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Support | {{ $request->get('subject') }}</title>
</head>
<body>
    <div class="container">
        <table class="table">
            <tbody>
                @if (!is_null(Auth::user()))
                    <tr>
                        <td>User id</td>
                        <td>{{ Auth::user()->id }}</td>
                    </tr>
                @endif
                <tr>
                    <td>Name</td>
                    <td>{{ $request->get('name') }}</td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td>{{ $request->get('email') }}</td>
                </tr>
                <tr>
                    <td>Subject</td>
                    <td>{{ $request->get('subject') }}</td>
                </tr>
                <tr>
                    <td>Description</td>
                    <td>{{ $request->get('description') }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
