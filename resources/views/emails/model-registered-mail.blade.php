<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Model registered | {{ $user->username }}</title>
</head>
<body>
    <div class="container">
        <table class="table">
            <tbody>
                <tr>
                    <td>Username</td>
                    <td>{{ $user->username }}</td>
                </tr>
                <tr>
                    <td>Model id</td>
                    <td>{{ $user->id }}</td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td>{{ $user->email }}</td>
                </tr>
                <tr>
                    <td>Presentation</td>
                    <td>{{ $user->profile->presentation }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
