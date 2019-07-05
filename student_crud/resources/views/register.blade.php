<!DOCTYPE html>

<html>

<head>

    <title>Registration</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

</head>

<body>



<div class="container">

    <h3>Registration form</h3>

    @if (count($errors) > 0)

      <div class="alert alert-danger">



          <ul>

              @foreach ($errors->all() as $error)

              <li>{{ $error }}</li>

              @endforeach

          </ul>

      </div>



    @endif



    @if ($message = Session::get('success'))

          <div class="alert alert-success">

              <p>{{ $message }}</p>

          </div>

    @endif



    <form action="{{ url('register') }}" method="POST" id="signupForm">

      {{ csrf_field() }}



        <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">

            <label class="control-label">Name:</label>

            <input type="text" name="name" class="form-control" value="{{ old('name') }}">

            @if ($errors->has('name'))

                <span class="text-danger">{{ $errors->first('name') }}</span>

            @endif

        </div>

        <div class="form-group {{ $errors->has('phone') ? 'has-error' : '' }}">

            <label class="control-label">Phone:</label>

            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">

            @if ($errors->has('phone'))

                <span class="text-danger">{{ $errors->first('phone') }}</span>

            @endif

        </div>

        <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">

            <label class="control-label">Email:</label>

            <input type="email" name="email" class="form-control" value="{{ old('email') }}">

            @if ($errors->has('email'))

                <span class="text-danger">{{ $errors->first('email') }}</span>

            @endif

        </div>

        <div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">

            <label class="control-label">Password:</label>

            <input type="password" name="password" class="form-control">

            @if ($errors->has('password'))

                <span class="text-danger">{{ $errors->first('password') }}</span>

            @endif

        </div>

        <div class="form-group {{ $errors->has('confirm_password') ? 'has-error' : '' }}">

            <label class="control-label">Confirm Password:</label>

            <input type="password" name="confirm_password" class="form-control">

            @if ($errors->has('confirm_password'))

                <span class="text-danger">{{ $errors->first('confirm_password') }}</span>

            @endif

        </div>

        <div class="form-group">

            <button class="btn btn-success" type="submit">Submit</button>

        </div>

    </form>

</div>



</body>

</html>