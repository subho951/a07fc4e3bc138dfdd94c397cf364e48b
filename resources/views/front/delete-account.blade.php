<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Delete Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <h1 style="text-align: center;">Delete Account</h1>
    @if(session('success_message'))
    <div class="alert alert-success">
        {{ session('success_message') }}
    </div>
@endif

@if(session('error_message'))
    <div class="alert alert-danger">
        {{ session('error_message') }}
    </div>
@endif

<form method="POST" action="{{ route('delete-account.store') }}" class="w-50 mx-auto mt-4">
    @csrf

    <div class="form-group">
        <label for="entity_name">User Type</label>
        <input type="text" class="form-control" id="user_type" name="user_type" required>
    </div>

    <div class="form-group">
        <label for="entity_name">Entity Name</label>
        <input type="text" class="form-control" id="entity_name" name="entity_name" required>
    </div>

    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" class="form-control" id="email" name="email" required>
    </div>

    <div class="form-group">
        <label for="phone">Phone</label>
        <input type="text" class="form-control" id="phone" name="phone" required>
    </div>

    <div class="form-group">
        <label for="comments">Comments (optional)</label>
        <textarea class="form-control" id="comments" name="comments" rows="4"></textarea>
    </div>

    <button type="submit" class="btn btn-danger w-100">Submit Delete Request</button>
</form>

</form>
</body>
</html>