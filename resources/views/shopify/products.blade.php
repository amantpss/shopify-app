<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" 
    integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"> --}}
</head>
<body>
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    <div class="container text-center">
        
        @php $i = 1; @endphp
        @foreach($products as $product)
            @if($i % 3 == 1)
                <div class="row align-items-start">
            @endif
                <div class="col-sm">
                    @if (isset($product['image']['src']))
                    <a href="/product-detail/{{$product['id']}}">
                        <img src="{{ $product['image']['src'] }}" width="200px">
                    </a>    
                    @endif
                    <h1>{{ $product['title'] }}</h1>
                    <a href="/delete/{{$product['id']}}" type="button" class="btn btn-danger">Delete</a>
                </div>
            @if($i % 3 == 0 || $loop->last)
                </div>
            @endif
            @php $i++; @endphp
        @endforeach
        
      </div>
</body>
</html>