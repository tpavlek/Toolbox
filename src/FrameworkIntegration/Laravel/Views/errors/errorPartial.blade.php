@if (count($success) > 0)
    <div class="notification success">
        <h2>Success!</h2>
        <ul>
            @foreach ($success as $message)
                <li>{{ $message }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (count($warn) > 0)
    <div class="notification warn">
        <h2>Warning:</h2>
        <ul>
            @foreach ($warn as $message)
                <li>{{ $message }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (count($errors_arr) > 0)
    <div class="notification error">
        <h2>Errors Occurred!</h2>
        <ul>
            @foreach ($errors_arr as $message)
                <li>{{ $message }}</li>
            @endforeach
        </ul>
    </div>
@endif
