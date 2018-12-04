@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <line-component :messages="{{ $messages }}"></line-component>

</div>
@endsection
